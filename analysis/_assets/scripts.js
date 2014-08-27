var valOptions = {
    pieHole:0.55,
    backgroundColor:'transparent',
    legend:{position:'none'},
    enableInteractivity:false,
    pieSliceText:'none',
    chartArea:{left:0,top:0,width:'100%',height:'100%'},
    colors:['#2D3F50','#91A2B2','#D1D4DB','#E5E5E5']
};

var repoOptions = valOptions;

var undecidedColour = "#3690AC";
var dataSeries = {};
var itemSeries = {targetAxisIndex:1,pointSize:0,color:"#aed2dd"};

var trendOptions = {
    backgroundColor:"transparent",
    legend:{position:'none'},
    chartArea:{left:50,top:20,width:'735',height:'170'},
    colors:['#2D3F50','#91A2B2','#D1D4DB','#E5E5E5'],
    fontName:"arial",
    fontSize:"11",
    hAxis:{slantedText:true,slantedTextAngle:60,textStyle:{color:"#9F9F9F",fontName:"arial",fontSize:11}},
    vAxes:[{gridlines:{count:7},textStyle:{color:"#9F9F9F",fontName:"arial",fontSize:11},format:"##.##'%'",baseline:"transparent"},{viewWindowMode:"maximized",gridlines:{color:"transparent"},textStyle:{color:"#9F9F9F",fontName:"arial",fontSize:11},titleTextStyle:{color:"#9F9F9F",fontName:"arial",fontSize:13,italic:false},baseline:"transparent"}],
    focusTarget:"category",
    tooltip:{textStyle:{fontName:"arial",fontSize:13}},
    pointSize:6,
    crosshair:{trigger:"both",orientation:"vertical",opacity:0.2}
};

var perfectTrendOptions = JSON.parse(JSON.stringify(trendOptions));
perfectTrendOptions.vAxes[0].ticks = [0,20,40,60,80,100];
perfectTrendOptions.vAxes[0].maxValue = 100;
perfectTrendOptions.series = [dataSeries,itemSeries];


function init()
{
    document.getElementById("flyout").addEventListener('mouseover', showFlyout);
    document.getElementById("flyoutFixed").addEventListener('mouseover', showFlyout);
    document.getElementById("flyout").addEventListener('mouseout', hideFlyout);
    document.getElementById("flyoutFixed").addEventListener('mouseout', hideFlyout);

    var consistencyColour = document.getElementById("consistency").className;

    var click = function(e) {
        if (document.getElementById("flyout").className == "flyout expanded pinned") {
            return;
        }

        if (document.getElementById("flyout").className == "flyout expanded") {
            document.getElementById("flyout").className      = "flyout";
            document.getElementById("flyoutFixed").className = "flyoutFixed";
        } else {
            document.getElementById("flyout").className      = "flyout expanded";
            document.getElementById("flyoutFixed").className = "flyoutFixed expanded";
        }
    };

    document.getElementById("flyoutControl").addEventListener('click', click);

    var scroll = function(e) {
        var flyoutScroll = (document.getElementById('flyout').scrollHeight - document.getElementById('flyout').clientHeight);
        var delta        = e.wheelDelta || -e.detail;
        if (this.scrollTop >= flyoutScroll && delta < 0) {
          e.preventDefault();
        } else if (this.scrollTop <= 0 && delta > 0) {
          e.preventDefault();
        }
    };

    document.getElementById('flyout').addEventListener('mousewheel', scroll);
    document.getElementById('flyout').addEventListener('DOMMouseScroll', scroll);

    document.getElementById("flyoutPin").addEventListener('click', function(e) {
        if (document.getElementById("flyoutPin").className == "flyoutPin") {
            document.getElementById("flyoutPin").className   = "flyoutPin active";
            document.getElementById("flyout").className      = "flyout expanded pinned";
            document.getElementById("mainWrap").className    = "mainWrap pinned";
            document.getElementById("fixedHeader").className = "fixedHeader pinned";
        } else {
            document.getElementById("flyoutPin").className   = "flyoutPin";
            document.getElementById("flyout").className      = "flyout expanded";
            document.getElementById("mainWrap").className    = "mainWrap";
            document.getElementById("fixedHeader").className = "fixedHeader";
        }
    });

    var scroll = function(e) {
        var distanceY = (window.pageYOffset || document.documentElement.scrollTop);
        var height    = 449;
        if (document.getElementById("introductionWrap").className == "introductionWrap expanded") {
            height = (document.getElementById("introductionWrap").clientHeight + 20);
        }

        // Switch the consistency banner.
        if (distanceY > 300) {
            document.getElementById("consistency").className = consistencyColour + " small";
        } else {
            document.getElementById("consistency").className = consistencyColour;
        }

        if (distanceY > height) {
            if (document.getElementById("reportInstructionsWrap").className == "reportInstructionsWrap expanded") {
                document.getElementById("contentWrap").className = "contentWrap small expanded";
                document.getElementById("introductionWrap").className = "introductionWrap small expanded";
            } else {
                document.getElementById("contentWrap").className = "contentWrap small";
                document.getElementById("introductionWrap").className = "introductionWrap small";
            }

            document.getElementById("fixedHeaderContent").style.opacity = "1";
            
        } else {
            if (document.getElementById("reportInstructionsWrap").className == "reportInstructionsWrap expanded") {
                document.getElementById("introductionWrap").className = "introductionWrap expanded";
            } else {
                document.getElementById("introductionWrap").className = "introductionWrap";
            }

            document.getElementById("contentWrap").className = "contentWrap";
            document.getElementById("fixedHeaderContent").style.opacity = "0";
        }
    }

    scroll();
    window.addEventListener('scroll', scroll);

}//end init()

var listClick = function(e) {
    var node = e.target;
    while (node.parentNode) {
        if (node.id === 'listBoxWrap'
            || node.id === 'gradeInstructionsWrap'
        ) {
            return;
        }

        node = node.parentNode;
    }

    hideListBox();
}

function showFlyout()
{
    if (document.getElementById("flyout").className != "flyout expanded pinned") {
        document.getElementById("flyout").className      = "flyout expanded";
        document.getElementById("flyoutFixed").className = "flyoutFixed expanded";
    }

}//end showFlyout()


function hideFlyout()
{
    if (document.getElementById("flyout").className != "flyout expanded pinned") {
        document.getElementById("flyout").className      = "flyout";
        document.getElementById("flyoutFixed").className = "flyoutFixed";
    }

}//end hideFlyout()


function showListBox(id)
{
    document.getElementById('listBoxWrap').innerHTML     = document.getElementById(id).innerHTML;
    document.getElementById('listBoxWrap').style.display = 'block';
    var scroll = function(e) {
        var listScroll = (document.getElementById(id + 'listBoxListWrap').scrollHeight - document.getElementById(id + 'listBoxListWrap').clientHeight);
        var delta      = e.wheelDelta || -e.detail;
        if (this.scrollTop >= listScroll && delta < 0) {
          e.preventDefault();
        } else if (this.scrollTop <= 0 && delta > 0) {
          e.preventDefault();
        }
    };

    document.getElementById(id + 'listBoxListWrap').addEventListener('mousewheel', scroll);
    document.getElementById(id + 'listBoxListWrap').addEventListener('DOMMouseScroll', scroll);

    document.addEventListener('mouseup', listClick);

}//end showListBox()


function hideListBox()
{
    document.getElementById('listBoxWrap').style.display ='none';
    document.getElementById('gradeInstructionsWrap').style.display ='none';
    document.removeEventListener('mouseup', listClick);

}//end hideListBox()


function toggleGradeBox()
{
    if (document.getElementById("gradeInstructionsWrap").style.display == "none"
        || document.getElementById("gradeInstructionsWrap").style.display == ""
    ) {
        document.getElementById('gradeInstructionsWrap').style.display ='block';
        document.addEventListener('mouseup', listClick);
    } else {
        document.getElementById('gradeInstructionsWrap').style.display ='none';
        document.removeEventListener('mouseup', listClick);
    }

}//end hideListBox()


function toggleInstructions()
{
    if (document.getElementById("reportInstructionsWrap").className == "reportInstructionsWrap collapsed") {
        document.getElementById("reportInstructionsWrap").className = "reportInstructionsWrap expanded";
    } else {
        document.getElementById("reportInstructionsWrap").className = "reportInstructionsWrap collapsed";
    }

    if (document.getElementById("introductionWrap").className == "introductionWrap") {
        document.getElementById("introductionWrap").className = "introductionWrap expanded";
    } else if (document.getElementById("introductionWrap").className == "introductionWrap expanded") {
        document.getElementById("introductionWrap").className = "introductionWrap";
    }

}//end toggleInstructions()
