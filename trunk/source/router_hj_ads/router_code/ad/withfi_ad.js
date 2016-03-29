
var ad_info = {
    proj: ""
};

(function (win, doc, undefined) {
    window.miwifi_toolbar_info = window.miwifi_toolbar_info || {};
    var CONF = {
        device_info: "/withficode/miwifi_toolbar_info.js",
        jquery: "/withficode/com_src/jquery.min.js",
        monitor: "/withficode/com_src/withfi_monitor.js"
    };

    var isMobile = (function () {
        return {
            Android: function () {
                return navigator.userAgent.match(/Android/i)
            },
            BlackBerry: function () {
                return navigator.userAgent.match(/BlackBerry/i)
            },
            iOS: function () {
                return navigator.userAgent.match(/iPhone|iPad|iPod/i)
            },
            Opera: function () {
                return navigator.userAgent.match(/Opera Mini/i)
            },
            Windows: function () {
                return navigator.userAgent.match(/IEMobile/i)
            },
            any: function () {
                return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows())
            }
        }
    }());
    var isIframe = function () {
        return win.self !== win.top
    };
    var loadJs = function (url, callback, options) {
        options = options || {};
        var head = document.getElementsByTagName("head")[0] || document.documentElement,
                script = document.createElement("script"),
                done = false;
        script.src = url;
        if (options.charset) {
            script.charset = options.charset
        }
        if ("async" in options) {
            script.async = options["async"] || ""
        }
        script.onerror = script.onload = script.onreadystatechange = function () {
            if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
                done = true;
                if (callback) {
                    callback()
                }
                script.onerror = script.onload = script.onreadystatechange = null;
                head.removeChild(script)
            }
        };
        head.insertBefore(script, head.firstChild)
    };
    var init = function () {

        loadJs(CONF.device_info,
                function () {
                }
        );

        if (isIframe()) {
            //return;
            loadJs(CONF.jquery,
                    function () {
                        var wjq = $.noConflict(true);
                        loadJs(CONF.monitor,
                                function () {
                                    MIWIFI_MONITOR.setProject("iframe");
                                    if (isMobile.any()) {
                                        MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                    } else {
                                        MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                    }
                                })
                    }
            );

        } else {
            loadJs(CONF.jquery,
                    function () {
                        var wjq = $.noConflict(true);

                        /*load icon ad from com_src*/
                        var url_dest = '/withficode/com_src/icon/ad';
                        wjq.ajax({
                            type: "GET",
                            url: url_dest,
                            dataType: "json",
                            success: function (resp) {
                                if (resp.adtype) {
                                    ad_info.proj = resp.adtype;
                                }
                                wjq("body").prepend(resp.adhtml);
                                loadJs(CONF.monitor,
                                        function () {
                                            MIWIFI_MONITOR.setProject(ad_info.proj);
                                            if (isMobile.any()) {
                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                            } else {
                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                            }
                                        })
                            },
                            error: function (resp) {
                            }
                        });
                        /*end load icon ad from com_src*/


                        if (isMobile.any()) {
                            //wjq.getScript("/withficode/com_src/ggj.js");
                            wjq.getScript("/withficode/com_src/zhengbang.js");

                            if (isMobile.iOS()) {
                                //IOS
                                var bid_url = 'http://admin.withfi.com/bid?device_id=' + miwifi_toolbar_info.device_id;
                                wjq.ajax({
                                    //async: false,
                                    type: "GET",
                                    url: bid_url,
                                    dataType: "jsonp",
                                    jsonp: "bidjsonp",
                                    success: function (resp) {
                                        var url_dest = '/withficode/' + resp.adhtml + '/ad';
                                        wjq.ajax({
                                            type: "GET",
                                            url: url_dest,
                                            dataType: "json",
                                            success: function (resp) {
                                                if (resp.adtype) {
                                                    ad_info.proj = resp.adtype;
                                                }
                                                wjq("body").prepend(resp.adhtml);
                                                loadJs(CONF.monitor,
                                                        function () {
                                                            MIWIFI_MONITOR.setProject(ad_info.proj);
                                                            //MIWIFI_MONITOR.setProject('tt');
                                                            if (isMobile.any()) {
                                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                            } else {
                                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                            }
                                                        })
                                            },
                                            error: function (resp) {
                                                return;
                                            }
                                        });
                                    },
                                    error: function (resp) {

                                        var ad_array = new Array("1446997292.47", "1447119624.0", "1447132953.44", "1447228943.29");

                                        var ad_number = ad_array.length;
                                        var ad_id = Math.floor(Math.random() * ad_number);

                                        var url_dest = '/withficode/' + ad_array[ad_id] + '/ad';

                                        wjq.ajax({
                                            type: "GET",
                                            url: url_dest,
                                            dataType: "json",
                                            success: function (resp) {
                                                if (resp.adtype) {
                                                    ad_info.proj = resp.adtype;
                                                }
                                                wjq("body").prepend(resp.adhtml);
                                                loadJs(CONF.monitor,
                                                        function () {
                                                            MIWIFI_MONITOR.setProject(ad_info.proj);
                                                            //MIWIFI_MONITOR.setProject('tt');
                                                            if (isMobile.any()) {
                                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                            } else {
                                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                            }
                                                        })
                                            },
                                            error: function (resp) {
                                                return;
                                            }
                                        });
                                    }

                                });

                            } else {
                                //andriod and other cell phone
                                var bid_url = 'http://admin.withfi.com/bid?device_id=' + miwifi_toolbar_info.device_id;
                                wjq.ajax({
                                    //async: false,
                                    type: "GET",
                                    url: bid_url,
                                    dataType: "jsonp",
                                    jsonp: "bidjsonp",
                                    success: function (resp) {
                                        var url_dest = '/withficode/' + resp.adhtml + '/ad';
                                        wjq.ajax({
                                            type: "GET",
                                            url: url_dest,
                                            dataType: "json",
                                            success: function (resp) {
                                                if (resp.adtype) {
                                                    ad_info.proj = resp.adtype;
                                                }
                                                wjq("body").prepend(resp.adhtml);
                                                loadJs(CONF.monitor,
                                                        function () {
                                                            MIWIFI_MONITOR.setProject(ad_info.proj);
                                                            //MIWIFI_MONITOR.setProject('tt');
                                                            if (isMobile.any()) {
                                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                            } else {
                                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                            }
                                                        })
                                            },
                                            error: function (resp) {
                                                return;
                                            }
                                        });
                                    },
                                    error: function (resp) {

                                        var ad_array = new Array("1446997292.47", "1447119624.0", "1447132953.44", "1447228943.29");

                                        var ad_number = ad_array.length;
                                        var ad_id = Math.floor(Math.random() * ad_number);

                                        var url_dest = '/withficode/' + ad_array[ad_id] + '/ad';

                                        wjq.ajax({
                                            type: "GET",
                                            url: url_dest,
                                            dataType: "json",
                                            success: function (resp) {
                                                if (resp.adtype) {
                                                    ad_info.proj = resp.adtype;
                                                }
                                                wjq("body").prepend(resp.adhtml);
                                                loadJs(CONF.monitor,
                                                        function () {
                                                            MIWIFI_MONITOR.setProject(ad_info.proj);
                                                            //MIWIFI_MONITOR.setProject('tt');
                                                            if (isMobile.any()) {
                                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                            } else {
                                                                MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                            }
                                                        })
                                            },
                                            error: function (resp) {
                                                return;
                                            }
                                        });
                                    }
                                });
                            }

                        } else {
                            //pc
                            /*
                            var adhouyi = document.createElement('script');
                            adhouyi.src = 'http://www.adhouyi.com/js/pub/172020201/adhj.js';
                            adhouyi.type = 'text/javascript';
                            document.body.insertBefore(adhouyi, document.body.children.item(0));
                            */

                            wjq.getScript("/withficode/com_src/zhengbang_pc.js");

                            var bid_url = 'http://admin.withfi.com/bid?device_id=' + miwifi_toolbar_info.device_id;
                            wjq.ajax({
                                //async: false,
                                type: "GET",
                                url: bid_url,
                                dataType: "jsonp",
                                jsonp: "bidjsonp",
                                success: function (resp) {
                                    var url_dest = '/withficode/' + resp.adhtml + '/ad';
                                    wjq.ajax({
                                        type: "GET",
                                        url: url_dest,
                                        dataType: "json",
                                        success: function (resp) {
                                            if (resp.adtype) {
                                                ad_info.proj = resp.adtype;
                                            }
                                            wjq("body").prepend(resp.adhtml);
                                            loadJs(CONF.monitor,
                                                    function () {
                                                        MIWIFI_MONITOR.setProject(ad_info.proj);
                                                        //MIWIFI_MONITOR.setProject('tt');
                                                        if (isMobile.any()) {
                                                            MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                        } else {
                                                            MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                        }
                                                    })
                                        },
                                        error: function (resp) {
                                            return;
                                        }
                                    });
                                },
                                error: function (resp) {
                                    var ad_array = new Array("1446997476.14", "1447046014.79", "1447046044.46", "1447119693.92");
                                    var ad_number = ad_array.length;

                                    var ad_id = Math.floor(Math.random() * ad_number);

                                    var url_dest = '/withficode/' + ad_array[ad_id] + '/ad';

                                    wjq.ajax({
                                        type: "GET",
                                        url: url_dest,
                                        dataType: "json",
                                        success: function (resp) {
                                            if (resp.adtype) {
                                                ad_info.proj = resp.adtype;
                                            }
                                            wjq("body").prepend(resp.adhtml);
                                            loadJs(CONF.monitor,
                                                    function () {
                                                        MIWIFI_MONITOR.setProject(ad_info.proj);
                                                        //MIWIFI_MONITOR.setProject('tt');
                                                        if (isMobile.any()) {
                                                            MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                        } else {
                                                            MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
                                                        }
                                                    })
                                        },
                                        error: function (resp) {
                                            return;
                                        }
                                    });
                                }

                            });

                        }

                    })


        }

    };

    init()

})(window, document, undefined);

	