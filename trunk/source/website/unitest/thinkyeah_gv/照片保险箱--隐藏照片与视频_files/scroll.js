($(document).on('ready', function(){
    /* on load fade-in effect */
	$(this).scrollTop(0);
	$('html').animate({scrollTop:0}, 1);
	$('body').animate({scrollTop:0}, 1);
	$(window).scrollTop(0);

    /* animate in page anchor scroll */
	$(".scroll").click(function(event){
		event.preventDefault();
		//calculate destination place
		var dest = 0;
        var offset = $(this.hash).offset() || {top:0};
		if(offset.top > $(document).height()-$(window).height()){
			dest=$(document).height()-$(window).height();
		} else {
			dest= offset.top;
		}
		//go to destination
		$('html,body').animate({scrollTop:dest-50}, 1324,'swing');
	});
}));
