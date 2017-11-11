/*http://jsfiddle.net/qLyVc/4/*/

var scrollByY = 1;
var delay = 0.01 * 1000;
var waitTime = 4 * 1000;
//var scrollDelay;

function getScrollingDistanceLeft() {
    // (pageHeight - windowHeight) - scrollingPosition
    return ($(document).height() - $(window).height()) - $(document).scrollTop();
}

function scrollPage() {
    if (getScrollingDistanceLeft() == $(document).scrollTop()) {
        delay = waitTime;
    } else if (getScrollingDistanceLeft() <= 0) { // reset position
        delay = waitTime;
        window.scroll(0, 0);
    }

    window.scrollBy(0, scrollByY);
    scrollDelay = setTimeout('scrollPage()', delay);
}

function startScrolling() {
    scrollDelay = setTimeout('scrollPage()', waitTime);
}

/*$(window).resize(function() {
 window.scroll(0, 0);
 startScrolling();
 });*/