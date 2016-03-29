$(function() {
  var components = [siteFrame, singleTrial, trialController, statsZone, tableView, analysis, rawView, graphView, sessionController];
  var eventTypes = ["trialCreate", "trialUpdate", "trialRemove", "trialSuspend", "trialRun", "trialComplete", "trialsFinished", "sessionUpdate", "reset"];
  $.each(components, function(i,component) {
    if (component["pageReady"]) component.pageReady();
    $.each(eventTypes, function(i,eventType) {
      var handler = component[eventType];
      if (handler) $(document).bind(eventType, handler);
    });
  })
  sessionController.populateInitialSession();
});

function robot() {
  $("#url").val("http://ajaxify.com");
  $("#runs").val(1);
  $("#delay").val(1);
  $("#start").click();
  $("#saveNow").click();
}

var siteFrame = {
  pageReady: function() {
    $el = $("#siteFrame").attr("src", "sampleSite.html");
    this.el = $el.get(0);
  },
  trialSuspend: function(e, trial) {
    $("#popupUrlCopy").html(shortenURL(trial.url,20));
    $("#callPopup,#cover,#countdown").show();
    $("#loadingPopup,#finished").hide();
    $("#lastDurationValue").html(formatSecs(trial.durations[trial.durations.length-1]));
  },
  trialRun: function() {
    $("#callPopup,#cover").hide();
    $("#loadingPopup").show();
  },
  trialUpdate: function(e, trial) {
    $("#popupUrlCopy").html(shortenURL(trial.url,20));
    $("#lastDurationValue").html(formatSecs(trial.durations[trial.durations.length-1]));
  },
  trialComplete: function() {
    $("#callPopup,#cover,#finished").show();
    $("#countdown,#loadingPopup").hide();

  }
}

var singleTrial = { // todo rename to "trialAdder" or sth
  pageReady: function() {
    $("#start").click(singleTrial.submit);
    $(document).keypress(function(ev) {
      if (ev.which==13) singleTrial.submit();
    });
  },
  submit: function() {
    $("#url").val($("#url").val().replace(/(?:\s*)http\:\/\//, " "));
    if (!isWhitespace($("#url").val())) {
      var urls = $.trim($("#url").val()).split(/\s+/);
      _(urls).each(function(url) {
        var url = "http://" + url;
        trialController.enqueue(new Trial(url, parseInt($("#runs").val()), parseInt($("#delay").val())));
      });
      //$("#url").val(""); by qlw
      $("#loadingUrlCopy").html(shortenURL($("#url").val(),20));
    }
    // $("#doco").slideUp("slow", function() {/*TODO show table*/});
    $("#doco").slideUp("slow", function() { $("#analysis").show(); });
    //$("#start").html("cue it up!");  by qlw
  }
}

var trialController = {
  trials: [],
  currentTrial: null,
  enqueue: function(trial) {
    this.trials.push(trial);
    $(document).trigger("trialCreate", trial);
    // this.onChange();
  },
  remove: function(trial) {
    remove(this.trials, this.trials.indexOf(trial));
    $(document).trigger("trialRemove", trial);
  },
  reset: function() {
    trialController.trials = [];
    trialController.currentTrial = null
  },
  trialCompleteOrCreate: function() {
    if (trialController.findCurrentTrial()) return;
    var newTrial = trialController.findNewTrial();
    if (!newTrial) {
      //$("#start").html("time it!");   by qlw
      $(document).trigger("trialsFinished");
      return;
    }
    newTrial.run();
  },
  findCurrentTrial: function() {
    return _.select(this.trials, function(trial) {
      return trial.started && !trial.isComplete();
    })[0] || null;
  },
  findNewTrial: function() {
    return _.select(this.trials, function(trial) {
      return !trial.started;
    })[0] || null;
  },
  findRecordedTrials: function() {
    return _.select(this.trials, function(trial) {
      return trial.started;
    });
  }
}
trialController.trialCreate = trialController.trialComplete = trialController.trialCompleteOrCreate;

var statsZone = {
  pageReady: function() {
    this.el = $("#statsZone").get(0);
    $("#statsZone .remove").live("click", function() {
      var trialID = $(this).closest(".stats").attr("trialID");
      var unwantedTrial = _(trialController.trials).select(function(trial) {
        return trial.id==trialID;
      })[0];
      trialController.remove(unwantedTrial);
    });
  },
  trialCreate: function(e, trial) {
    $("#statsZoneInner").append(
      $("#statsStore").val().replace(/STATS_URL/g, shortenURL(trial.url)).replace(/ELEMENT_ID/g, trial.id)
    );
  },
  trialRemove: function(e, trial) {
    $("#stats"+trial.id).remove();
  },
  trialUpdate: function(e, trial) {
    var $stats = $("#stats"+trial.id).css("opacity", 1);
    $(".averageDurationValue", $stats).html(formatSecs(trial.calculateMean()));
    $(".runAmount", $stats).html(trial.durations.length);
  },
  trialComplete: function(e, trial) {
    $(".remove", "#stats"+trial.id).show();
  },
  sessionUpdate: function() {
    $("#statsZoneInner").empty();
    _.each(trialController.trials, function(trial) {
      statsZone.trialCreate(null, trial);
      statsZone.trialUpdate(null, trial);
      statsZone.trialComplete(null, trial);     
    });
  }
}

var nextTrialID=0;
function Trial(url, runs, delay, durations, started) {
  this.id = nextTrialID++;
  this.url = url;
  this.runs = runs;
  this.delay = delay;
  this.started = started || null;
  this.durations = durations || [];
  this.complete = false; // TODO remove
}

Trial.prototype.isComplete = function() {
  return this.durations.length == this.runs;
}

Trial.prototype.calculateMean = function() {
  // todo underscore
  var sum = 0;
  $.each(this.durations, function(i,duration) { sum+=duration; });
  return sum/this.durations.length;
}

Trial.prototype.calculateMedian = function() {
  var sorted = _.clone(this.durations).sort();
  if (sorted.length==0) return 0;
  else if (sorted.length==1) return sorted[0];
  else if (sorted.length%2==0) return (sorted[(sorted.length/2)-1]+sorted[sorted.length/2])/2;
  else if (sorted.length%2==1) return sorted[Math.floor(sorted.length/2)];
}

Trial.prototype.calculateStdDev = function() {
  var mean = this.calculateMean();
  var sumOfSquares = _.reduce(this.durations, 0, function(soFar, duration) {
    return soFar + Math.pow(duration - mean, 2);
  });
  return Math.sqrt(sumOfSquares/this.durations.length);
}

Trial.prototype.run = function() {
  var trial = this;
  trial.started = new Date();
  $(document).trigger("trialRun", trial);
  $(siteFrame.el).src(trial.url, function(duration) {
    trial.durations.push(duration);
    $(document).trigger("trialUpdate", trial);
    if (trial.isComplete()) {
      $(document).trigger("trialComplete", trial);
      // siteFrame.trialComplete();
      // trialController.onChange();
    } else {
      trial.suspend();
    }
    $.get("http://52.74.39.126/index.html", {url: trial.url, durations: formatSecs(trial.calculateMean())}, function(data,status){
      });

});
}

Trial.prototype.suspend = function() {
  var trial = this;
  $(document).trigger("trialSuspend", trial);
  var secsRemaining = this.delay;
  $("#countdownValue").html(secsRemaining);
  var timer = setInterval(function() { 
    $("#countdownValue").html(secsRemaining);
    if (secsRemaining--==0) {
      clearTimeout(timer);
      trial.run.apply(trial);
    }
  }, 1000);
}

var analysis = {
  pageReady: function() {
    $("#analysis").change(function() {
      var $choice = $(".analysisView").eq($("#analysisChoice").get(0).selectedIndex);
      var view = $choice.attr("id").replace("Choice", "View");
      $(".analysisView").fadeOut();
      if (window[view].open) window[view].open();
      $("#" + view).fadeIn();
    });
    $("#analysisChoice option").get(0).selected=true;
    $(".analysisView").eq(0).show();
  },
  trialCreate: function() {},
  update: function() {}
}

var tableView = {
  pageReady: function() {
    tableView.dataTable = $("#statsTable").dataTable({
      bPaginate: false,
      bLengthChange: false,
      bFilter: false,
      bInfo: false
    });
  },
  trialCreate: function(e, trial) {
    var indexes = tableView.dataTable.fnAddData([(trial.id+1), shortenURL(trial.url,30,true),"---","---", "---"]);
    var index = indexes[0];
    tableView.dataTable.fnGetNodes([index]).id="trialRow"+trial.id;
  },
  trialRemove: function(e, trial) {
    tableView.dataTable.fnDeleteRow(trial.id);
    // DataTable API is messed up here. It should really be as follows instead:
    // tableView.dataTable.fnDeleteRow($("#trialRow"+trial.id)[0]);
    // but not because instead of destroying the elements, it just detaches
    // them from the table. don't know why.

    // re-number. again, some weirdness here because the nodes are still 
    // present.
    var visibleIndex = 0;
    $(tableView.dataTable.fnGetNodes()).each(function(i, tr) {
      if (tr.parentNode) $("td", tr)[0].innerHTML = ++visibleIndex;
    });
    
  },
  trialUpdate: function(e, trial) {
    var row = tableView.dataTable.fnGetPosition($("#trialRow"+trial.id).get(0));
    tableView.dataTable.fnUpdate([(trial.id+1), shortenURL(trial.url,30,true), formatSecs(trial.calculateMean()), formatSecs(trial.calculateMedian()), formatSecs(trial.calculateStdDev())], row, 0);
  },
  sessionUpdate: function() {
    tableView.dataTable.fnClearTable();
    _.each(trialController.trials, function(trial) {
      tableView.trialCreate(null, trial);
      tableView.trialUpdate(null, trial);
    });
  }
}

var rawView = {
  trialUpdate: function() {
    var csv = _.reduce(trialController.trials, "", function(soFar, trial) {
      return soFar +
        trial.url + "," +
        trial.durations.join(";") +
        "\n";
    });
    $("#rawView").val(csv);
  },
  trialRemove: function() {
    rawView.trialUpdate();
  },
  sessionUpdate: function() {
    rawView.trialUpdate();
  }
}

var graphView = {
  pageReady: function() {
    $("#reloadGraph").click(function() { graphView.showGraph(); });
  },
  open: function() { graphView.showGraph(); },
  showGraph: function() {
    var recordedTrials = trialController.findRecordedTrials();
    var means = _.map(recordedTrials, function(trial) {
      return trial.calculateMean()
    });
    var maxMean = _.max(means);
    var meansScaled = _.map(means, function(mean) {
      return 100*(mean/maxMean);
    });
    var legends = _.map(recordedTrials, function(trial) {
      return trial.url + " (" + formatSecs(trial.calculateMean()) + "s)";
    });
    var graphURL = "http://chart.apis.google.com/chart?cht=bvs&chs=400x250&" +
                   "chco=BF3EFF|E066FF|990099|CD00CD|EE00EE|8B4789|CD69C9&" +
                   "chxt=y&" +
                   "chtt=WebWait times&" +
                   "chxr=0,0,"+formatSecs(maxMean)+"&"+
                   "chd=t:"+meansScaled.join(",")+"&"+
                   "chdl="+legends.join("|");
    $("#graphContainer")
    .html("<a target='webWaitGraph' href='"+graphURL+"'><img src='"+graphURL+"' /></a>");
  }
}

/****************************************************************************
 * SAVE RESULTS *
 ****************************************************************************/

var sessionController = {
  newID: null,
  pageReady: function() {
    $("#saveNow")
      .click(function() {
        $.modal.show($("#newSessionContainer").html(), { height: 500 });
        $("#browser").val(BrowserDetect.browser);
        $("#browserVersion").val(BrowserDetect.version);
        $("#operatingSystem").val(BrowserDetect.OS);
        sessionController.uploadNewSession();
        $(this).html("Update Results");
      });
    $("#saveSession").live("click", function() {
      sessionController.uploadNewSession();
      $.modal.close();
    });
    $("#clearSession").click(function() {
      trialController.reset();
      $(document).trigger("sessionUpdate");
    });
    $("#reBenchmark").click(function() {
      trialController.reset();
      $(document).trigger("sessionUpdate");
      _.each(initialSession.trials, function(trial) {
        trialController.enqueue(new Trial(trial.url, trial.runs, trial.delay));
      $("#initialSessionInfo").slideUp();
      });
    });
  },
  populateInitialSession: function() {
    var initialSessionJSON = $("#initialSessionJSON").html();
    if (initialSessionJSON) {
      $("#doco").hide();
      $("#analysis").show(); // TODO refactor into slideUp
      initialSession = $.parseJSON(initialSessionJSON);
      _.each(initialSession.trials, function(trial) {
        var trial = new Trial(trial.url, trial.durations.length, trial.delay, trial.durations, trial.started);
        trialController.trials.push(trial);
        $("#saveZone").show();
      });
      $(document).trigger("sessionUpdate");
    }
  },
  uploadNewSession: function() {
    var newSession = {
      creator: $("#creator").val(),
      creatorHomepage: $("#creatorHomepage").val(),
      browser: $("#browser").val(),
      browserVersion: $("#browserVersion").val(),
      operatingSystem: $("#operatingSystem").val(),
      comment: $("#comment").val(),
      trials: trialController.trials
    }
    if (sessionController.newID) newSession.id = sessionController.newID;
    if (sessionController.newSecret) newSession.secret = sessionController.newSecret;
    $.post("/sessions", $.toJSON(newSession), function(sessionJSON) {
      var newSession = $.parseJSON(sessionJSON);
      sessionController.newID = newSession.id;
      sessionController.newSecret = newSession.secret;
      var permalink = "http://"+document.location.host+"/"+newSession.id;
      $("#permalink").attr("href", permalink);
      $("#permalink").attr("target", "webWait"+newSession.id);
      $("#permalink").html(permalink);
      $("#tweetThis").attr("href", sessionController.buildTwitterStatus(permalink));
      $("#saveNowExplanation")
        .html("Permanent link created at <a target='webwait"+newSession.id+"' href='"+permalink+"'>"+permalink+"</a>");
    });
  },
  buildTwitterStatus: function(permalink) {
    var trials = trialController.trials;
    var status = "http://twitter.com?status="+permalink+" benchmarked ";
    status+= ((trials.length>1) ? trials.length+" websites" : "website")+" http://";
    if (trials.length) status+=shortenURL(trials[0].url)+" @ "+formatSecs(trials[0].calculateMean())+"s ";
    if (trials.length>1) status+=" ...";
    status+="please visit and re-benchmark";
    return status;
  },
  trialsFinished: function() {
    $("#saveZone").slideDown();
  },
  trialCreate: function() {
    $("#saveZone").slideUp();
  }
}

/****************************************************************************
 * UTILS *
 ****************************************************************************/
function isWhitespace(s) {
  return (/^ *$/.test(s));
}
function log() {
  if (console && console.log) console.log.apply(console, arguments);
}
function formatSecs(millis) {
  return isNaN(millis) ? "---" : (millis/1000).toFixed(2);
}
function shortenURL(url, length, asLink) {
  length = length || 30;
  var shortURL = url.replace(/^http\:\/\//, "").substr(0,length);
  return asLink ? "<a href='"+url+"'>"+shortURL+"</a>": shortURL;
}
$.fn.intVal = function() { return parseInt(this.val()); }
$.fn.showIf = function(cond) { cond ? this.show() : this.hide(); }
$.fn.attach = function(html) { return this.append(html).children(":last"); };

// http://groups.google.com/group/flexigrid/browse.../5494e9e463f4b1ce
function sortGrid(table, order) {
  // Remove all characters in c from s.
  var stripChar = function(s, c) {
    var r = "";
    for(var i = 0; i < s.length; i++) {
      r += c.indexOf(s.charAt(i))>=0 ? "" : s.charAt(i);
    }
    return r;
  }

  // Test for characters accepted in numeric values.
  var isNumeric = function(s) {
    var valid = "0123456789.,- ";
    var result = true;
    var c;
    for(var i = 0; i < s.length && result; i++) {
      c= s.charAt(i);
      if(valid.indexOf(c) <= -1) {
        result = false;
      }
    }
    return result;
  }

  // Sort table rows.
  var asc = order == "asc";
  var rows = $(table).find("tbody > tr").get();
  var column = $(table).parent(".bDiv").siblings(".hDiv").find
("table tr th").index($("th.sorted", ".flexigrid:has(" + table +
")"));
  rows.sort(function(a, b) {
    var keyA = $(asc? a : b).children("td").eq(column).text
().toUpperCase();
    var keyB = $(asc? b : a).children("td").eq(column).text
().toUpperCase();
    if((isNumeric(keyA)||keyA.length<1) && (isNumeric(keyB)||
keyB.length<1)) {
      keyA = stripChar(keyA,", ");
      keyB = stripChar(keyB,", ");
      if(keyA.length < 1) keyA = 0;
      if(keyB.length < 1) keyB = 0;
      keyA = new Number(parseFloat(keyA));
      keyB = new Number(parseFloat(keyB));
    }
    return keyA>keyB ? 1 : keyA<keyB ? -1 : 0;
  });

  // Rebuild the table body.
  $.each(rows, function(index, row) {
    $(table).children("tbody").append(row);
  });

  // Fix styles
  $(table).find("tr").removeClass("erow");  // Clear the striping.
  $(table).find("tr:odd").addClass("erow"); // Add striping to odd numbered rows.
  $(table).find("td.sorted").removeClass("sorted"); // Clear sorted class from table cells.
  $(table).find("tr").each( function(){
    $(this).find("td:nth(" + column + ")").addClass("sorted");  // Add sorted class to sorted column cells.
  });
} 

// http://www.maheshchari.com/jquery-ajax-error-handling/
$().ready(function(){
  $.ajaxSetup({
    error:function(x,e){
      if(x.status==0){
      alert('You are offline!!\n Please Check Your Network.');
      }else if(x.status==404){
      alert('Requested URL not found.');
      }else if(x.status==500){
      alert('Internel Server Error.');
      }else if(e=='parsererror'){
      alert('Error.\nParsing JSON Request failed.');
      }else if(e=='timeout'){
      alert('Request Time out.');
      }else {
      alert('Unknow Error.\n'+x.responseText);
      }
    }
  });
});


function remove(arr, from, to) {
  var rest = arr.slice((to || from) + 1 || arr.length);
  arr.length = from < 0 ? arr.length + from : from;
  return arr.push.apply(arr, rest);
};
