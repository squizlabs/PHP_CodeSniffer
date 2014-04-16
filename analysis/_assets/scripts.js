function init()
{
    document.getElementById("flyout").addEventListener('mouseover', showFlyout);
    document.getElementById("flyoutFixed").addEventListener('mouseover', showFlyout);
    document.getElementById("flyout").addEventListener('mouseout', hideFlyout);
    document.getElementById("flyoutFixed").addEventListener('mouseout', hideFlyout);

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

    window.addEventListener('scroll', function(e){
        var distanceY = (window.pageYOffset || document.documentElement.scrollTop);
        var height    = 470;
        if (document.getElementById("introductionWrap").className == "introductionWrap expanded") {
            height = (document.getElementById("introductionWrap").clientHeight + 20);
        }

        if (distanceY > height) {
            document.getElementById("introductionWrap").className = "introductionWrap small";
            document.getElementById("contentWrap").className      = "contentWrap small";
            document.getElementById("fixedHeaderContent").style.opacity = "1";
            document.getElementById("reportInstructionsWrap").className  = "reportInstructionsWrap collapsed";
        } else {
            if (document.getElementById("reportInstructionsWrap").className == "reportInstructionsWrap expanded") {
                document.getElementById("introductionWrap").className = "introductionWrap expanded";
            } else {
                document.getElementById("introductionWrap").className = "introductionWrap";
            }
            document.getElementById("contentWrap").className = "contentWrap";
            document.getElementById("fixedHeaderContent").style.opacity = "0";
        }
    });

}//end init()

window.onload = init();

var listClick = function(e) {
    var node = e.target;
    while (node.parentNode) {
        if (node.id === 'listBoxWrap') {
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
    document.getElementById('listBoxWrap').innerHTML      = document.getElementById(id).innerHTML;
    document.getElementById('listBoxWrap').style.display ='block';
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
    document.removeEventListener('mouseup', listClick);

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