$(document).ready(function(){

  // scroll all the way up
  $(window).scrollTop(0);

  var bottomBarHeight = $(".bottom-bar").outerHeight();
  var headerHeight = $(window).height() - bottomBarHeight;

  var scroll_pos = 0;
  var animation_begin_pos = 0;
  var animation_end_pos = headerHeight;
  var beginning_color = new $.Color( "rgba(255,255,255,0)" );
  var ending_color = new $.Color( "rgba(255,255,255,1)" );
  var beginningText_color = new $.Color( "rgba(255,255,255,1)" );
  var endingText_color = new $.Color( "rgba(0,153,204,1)" );

  $(document).scroll(function() {

    scroll_pos = $(this).scrollTop();
    if(scroll_pos >= animation_begin_pos && scroll_pos <= animation_end_pos ) {
      var percentScrolled = scroll_pos / ( animation_end_pos - animation_begin_pos );
      var alpha = beginning_color.alpha() + ( ( ending_color.alpha() - beginning_color.alpha() ) * percentScrolled );
      var newRed = beginning_color.red() + ( ( ending_color.red() - beginning_color.red() ) * percentScrolled );
      var newGreen = beginning_color.green() + ( ( ending_color.green() - beginning_color.green() ) * percentScrolled );
      var newBlue = beginning_color.blue() + ( ( ending_color.blue() - beginning_color.blue() ) * percentScrolled );

      var newRedText = beginningText_color.red() + ( ( endingText_color.red() - beginningText_color.red() ) * percentScrolled );
      var newGreenText = beginningText_color.green() + ( ( endingText_color.green() - beginningText_color.green() ) * percentScrolled );
      var newBlueText = beginningText_color.blue() + ( ( endingText_color.blue() - beginningText_color.blue() ) * percentScrolled );
      var newColor = new $.Color( newRed, newGreen, newBlue, alpha );
      var newTextColor = new $.Color( newRedText, newGreenText, newBlueText, 1 );

      $('.topMenu#desktop').animate({ backgroundColor: newColor}, 0);
      $('.animateBtn').animate({ color: newTextColor }, 0);

      $('.inversion').animate({ opacity: 1-alpha }, 0);
    } else if ( scroll_pos > animation_end_pos ) {
      $('.topMenu#desktop').animate({ backgroundColor: newColor}, 0);
      $('.animateBtn').animate({ color: newTextColor }, 0);

      $('.inversion').animate({ opacity: 1-alpha }, 0);
    } else if ( scroll_pos < animation_begin_pos ) {
      $('.topMenu#desktop').animate({ backgroundColor: newColor}, 0);
      $('.animateBtn').animate({ color: newTextColor }, 0);

      $('.inversion').animate({ opacity: 1-alpha }, 0);
    } else { }
  });

  $('.plain-menu').removeClass('plain-menu');
});
