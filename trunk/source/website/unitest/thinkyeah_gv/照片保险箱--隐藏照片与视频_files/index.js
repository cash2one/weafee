($(document).on('ready', function(){
	var bottomBarHeight = $(".bottom-bar").outerHeight();
    var $gallery = $('#homer-gallery');
    //var $allVideos = $gallery.find('.slide > video');
    var $allImages = $gallery.find('.slide > img');
    var $header = $("#header");
    var $slideTextCovers = $gallery.find('.slide > .cover-text');

    //function stopVideo(video, timeout) {
    //    if (!video) {
    //        return;
    //    }
    //    setTimeout(function() {
    //        video.pause();
    //        video.currentTime = 0;
    //        video.load();
    //    }, timeout || 500);
    //}

    function headerHeight() {
        return $(window).height() - bottomBarHeight;
    }

    function nextHeaderSlide() {
        var $slides = $gallery.children('.slide');
        var $activeSlide = $gallery.children('.slide.active');
        var nextIndex = ($activeSlide.index() + 1) % $slides.length;

        // handle current slide
        //var video = $activeSlide.find('video')[0];
        //stopVideo(video);
        $activeSlide.removeClass('active');

        // handle next slide
        var $nextSlide =  $($slides[nextIndex])
        $nextSlide.addClass('active');
        //$nextSlide.find('video')[0].play();

        setTimeout(nextHeaderSlide, 4500);
    }

    // When the window is resized
	if(!(Modernizr.mq('(max-device-width: 640px)'))){
		$(window).resize(function() {

			var newWidth = $('#homer-gallery').width();
			var newHeight = newWidth * (8/15);

			// Resize all videos according to their own aspect ratio
			//$allVideos.each(function() {
			//	var $el = $(this);
            //
			//	$el.width(newWidth).height(newWidth * (9/16));
			//});

            $allImages.each(function() {
            	var $el = $(this);

            	$el.width(newWidth).height(newWidth * (8/15));
            });

			var height = Math.min(newHeight,headerHeight());
			$header.height(height);
			$slideTextCovers.css('margin-bottom', 350 + (height-400)/2);

			// Kick off one resize to fix all videos on page load
		}).resize();

		nextHeaderSlide();
	}

	$(document).ready(function(){
		//$("a[rel^='prettyPhoto']").prettyPhoto({
		//	theme: 'pp_default', /* light_rounded / dark_rounded / light_square / dark_square / facebook */
		//	social_tools:false
		//});
		$("a[rel^='prettyPhoto']").nivoLightbox({
			effect: 'fade',                             // The effect to use when showing the lightbox
			theme: 'default',                           // The lightbox theme to use
			keyboardNav: true,                          // Enable/Disable keyboard navigation (left/right/escape)
			clickOverlayToClose: true,
			afterHideLightbox: function() {
				$(".nivo-lightbox-content").empty();
				$('.nivo-lightbox-overlay').empty().remove();
			}
		});
	});
}));


$(function() {
    $('a[href*=#]:not([href=#])').click(function() {
        if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
            if (target.length) {
                $('html,body').animate({
                    scrollTop: target.offset().top
                }, 600);
                return false;
            }
        }
    });
});
