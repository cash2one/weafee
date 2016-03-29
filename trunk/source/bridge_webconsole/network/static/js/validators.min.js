var validators = {
    'integer': function (v) {
        return (v.match(/^-?[0-9]+$/) != null)
    },
    'uinteger': function (v) {
        return (validators.integer(v) && (v >= 0))
    },
    'float': function (v) {
        return !isNaN(parseFloat(v))
    },
    'ufloat': function (v) {
        return (validators['float'](v) && (v >= 0))
    },
    'ipaddr': function (v) {
        return validators.ip4addr(v) || validators.ip6addr(v)
    },
    'check_mask': function (mask) {
        var exp = /^(254|252|248|240|224|192|128|0)\.0\.0\.0|255\.(254|252|248|240|224|192|128|0)\.0\.0|255\.255\.(254|252|248|240|224|192|128|0)\.0|255\.255\.255\.(254|252|248|240|224|192|128|0)$/;
        if (!mask.match(exp)) {
            return false
        }
        return true
    },
    'check_ip_mask_gw': function (static_ip, static_mask, static_gw) {
        if (static_ip == static_mask || static_mask == static_gw || static_ip == static_gw) {
            return false
        }
        var static_ip_arr = [
        ];
        var static_mask_arr = [
        ];
        var static_gw_arr = [
        ];
        static_ip_arr = static_ip.split('.');
        static_mask_arr = static_mask.split('.');
        static_gw_arr = static_gw.split('.');
        for (var i = 0; i < 4; i++) {
            var res = parseInt(static_ip_arr[i]) & parseInt(static_mask_arr[i]);
            var res_gw = parseInt(static_gw_arr[i]) & parseInt(static_mask_arr[i]);
            if (res != res_gw) {
                return false
            }
        }
        return true
    },
    'neg_ipaddr': function (v) {
        return validators.ip4addr(v.replace(/^\s*!/, '')) || validators.ip6addr(v.replace(/^\s*!/, ''))
    },
    'ip4addr': function (v) {
        
        return v.match(/^([1-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))\.(([0-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))\.){2}([0-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))$/);        
        /*
        if (v.match(/^(\d+)\.(\d+)\.(\d+)\.(\d+)(\/(\d+))?$/)) {           
            return (RegExp.$1 >= 0) && (RegExp.$1 <= 255) && (RegExp.$2 >= 0) && (RegExp.$2 <= 255) && (RegExp.$3 >= 0) && (RegExp.$3 <= 255) && (RegExp.$4 >= 0) && (RegExp.$4 <= 255) && (!RegExp.$5 || ((RegExp.$6 >= 0) && (RegExp.$6 <= 32)))
        }
        return false
        */
    },
    'neg_ip4addr': function (v) {
        return validators.ip4addr(v.replace(/^\s*!/, ''))
    },
    'ip6addr': function (v) {
        if (v.match(/^([a-fA-F0-9:.]+)(\/(\d+))?$/)) {
            if (!RegExp.$2 || ((RegExp.$3 >= 0) && (RegExp.$3 <= 128))) {
                var addr = RegExp.$1;
                if (addr == '::') {
                    return true
                }
                if (addr.indexOf('.') > 0) {
                    var off = addr.lastIndexOf(':');
                    if (!(off && validators.ip4addr(addr.substr(off + 1)))) {
                        return false
                    }
                    addr = addr.substr(0, off) + ':0:0'
                }
                if (addr.indexOf('::') >= 0) {
                    var colons = 0;
                    var fill = '0';
                    for (var i = 1; i < (addr.length - 1); i++) {
                        if (addr.charAt(i) == ':') {
                            colons++
                        }
                    }
                    if (colons > 7) {
                        return false
                    }
                    for (var i = 0; i < (7 - colons); i++) {
                        fill += ':0'
                    }
                    if (addr.match(/^(.*?)::(.*?)$/)) {
                        addr = (RegExp.$1 ? RegExp.$1 + ':' : '') + fill + (RegExp.$2 ? ':' + RegExp.$2 : '')
                    }
                }
                return (addr.match(/^(?:[a-fA-F0-9]{1,4}:){7}[a-fA-F0-9]{1,4}$/) != null)
            }
        }
        return false
    },
    'port': function (v) {
        return validators.integer(v) && (v >= 0) && (v <= 65535)
    },
    'portrange': function (v) {
        if (v.match(/^(\d+)-(\d+)$/)) {
            var p1 = RegExp.$1;
            var p2 = RegExp.$2;
            return validators.port(p1) && validators.port(p2) && (parseInt(p1) <= parseInt(p2))
        } else {
            return validators.port(v)
        }
    },
    'macaddr': function (v) {
        return (v.match(/^([a-fA-F0-9]{2}(:|-)){5}[a-fA-F0-9]{2}$/) != null)
    },
    'host': function (v) {
        return validators.hostname(v) || validators.ipaddr(v)
    },
    'hostname': function (v) {
        if (v.length <= 253) {
            return (v.match(/^[a-zA-Z0-9][a-zA-Z0-9\-.]*[a-zA-Z0-9]$/) != null)
        }
        return false
    },
    'wpakey': function (v) {
        if (v.length == 64) {
            return (v.match(/^[a-fA-F0-9]{64}$/) != null)
        } else {
            return (v.length >= 8) && (v.length <= 63)
        }
    },
    'wepkey': function (v) {
        if (v.substr(0, 2) == 's:') {
            v = v.substr(2)
        }
        if ((v.length == 10) || (v.length == 26)) {
            return (v.match(/^[a-fA-F0-9]{10,26}$/) != null)
        } else {
            return (v.length == 5) || (v.length == 13)
        }
    },
    'uciname': function (v) {
        return (v.match(/^[a-zA-Z0-9_]+$/) != null)
    },
    'neg_network_ip4addr': function (v) {
        v = v.replace(/^\s*!/, '');
        return validators.uciname(v) || validators.ip4addr(v)
    },
    'range': function (v, args) {
        var min = parseInt(args[0]);
        var max = parseInt(args[1]);
        var val = parseInt(v);
        if (!isNaN(min) && !isNaN(max) && !isNaN(val)) {
            return ((val >= min) && (val <= max))
        }
        return false
    },
    'min': function (v, args) {
        var min = parseInt(args[0]);
        var val = parseInt(v);
        if (!isNaN(min) && !isNaN(val)) {
            return (val >= min)
        }
        return false
    },
    'max': function (v, args) {
        var max = parseInt(args[0]);
        var val = parseInt(v);
        if (!isNaN(max) && !isNaN(val)) {
            return (val <= max)
        }
        return false
    },
    'neg': function (v, args) {
        if (args[0] && typeof validators[args[0]] == 'function') {
            return validators[args[0]](v.replace(/^\s*!\s*/, ''))
        }
        return false
    }
};