$(function () {
    var prevScroll = 0,
        curDir = 'down',
        prevDir = 'up';

    var $topbar = $('#header');
    var $bottomBar = $('#mbtn');

    var $winHeight = $(window).height();
    var $topImageHeight = $winHeight*0.65;

    var topSectionTextHeight = 190;
    if (($winHeight - topSectionTextHeight) > 30) {
        $topImageHeight = $winHeight - topSectionTextHeight;
    }

    $('#section-top-image').css({
        'height': $topImageHeight
    });

    $(window).scroll(function () {
        scrollY = $(this).scrollTop();
        // Ignore elastic scrolling.
        if (scrollY < 0 ) {
            return;
        }

        if ($(this).scrollTop() >= prevScroll) {
            curDir = 'down';
            if (curDir != prevDir) {

                $bottomBar.stop();
                $bottomBar.animate({bottom: '-70px'}, 300);

                //$topbar.animate({top: '-54px'}, 300);

                prevDir = curDir;
            }
        } else {
            curDir = 'up';
            if (curDir != prevDir) {
                $bottomBar.stop();
                $bottomBar.animate({bottom: '0px'}, 300);
                //$topbar.animate({top: '0px'}, 300);

                prevDir = curDir;
            }
        }

        prevScroll = $(this).scrollTop();

    });

});