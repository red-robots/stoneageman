/**
 *	Custom jQuery Scripts
 *	
 *	Developed by: Austin Crane	
 *	Designed by: Austin Crane
 */

jQuery(document).ready(function ($) {

	// alert('asdfasdfah');

	$('.flexslider').flexslider({
	    animation: "slide",
	    animationLoop: false,
	    itemWidth: 210,
	    itemMargin: 5,
	    slideshow: false
	  });

	$(document).on("click",".menu-toggle",function(){
		$(this).toggleClass('open');
		$('body').toggleClass('openNav');
	});

	
	  
	
	
	/* Play / Pause Video */
 	$(document).on("click","#playBtn",function(e){
 		e.preventDefault();
 		if( $(this).hasClass('play') ) {
 			$(this).removeClass('play').addClass('paused');
 			$('#mp4video')[0].play();
 			$(".videoContainer").removeClass("hidden");
 		} else {
 			//$(this).removeClass('paused').addClass('play paused');
 			$(this).removeClass('paused').addClass('play');
 			$('#mp4video')[0].pause();
 			//$(".videoContainer").addClass("hidden");
 		}
 	});
 	
 	$(document).on("click","#playBtnEmbed",function(e){
 		e.preventDefault();
 		var symbol = $(".videoIframe").attr('data-symbol');
 		$(".videoIframe iframe")[0].src += symbol + "autoplay=1";
 		$(this).hide();
 		$(".videoThumb").hide();
 	});

	new WOW().init();


	

	$('a[href*="#"]')
	  // Remove links that don't actually link to anything
	  .not('[href="#"]')
	  .not('[href="#0"]')
	  .click(function(event) {
	    // On-page links
	    if (
	      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
	      && 
	      location.hostname == this.hostname
	    ) {
	      // Figure out element to scroll to
	      var target = $(this.hash);
	      var offset = $("#masthead").outerHeight();
	      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
	      // Does a scroll target exist?
	      if (target.length) {
	        // Only prevent default if animation is actually gonna happen
	        event.preventDefault();
	        $('html, body').animate({
	          scrollTop: target.offset().top - offset
	        }, 1000, function() {
	          // Callback after animation
	          // Must change focus!
	          var $target = $(target);
	          $target.focus();
	          if ($target.is(":focus")) { // Checking if the target was focused
	            return false;
	          } else {
	            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
	            $target.focus(); // Set focus again
	          };
	        });
	      }
	    }
	});

	if( $(".wp-embed-aspect-16-9 iframe").length > 0 ) {
		$(".wp-embed-aspect-16-9 iframe").each(function(){
			var wrapper = $(this).parents(".wp-embed-aspect-16-9");
			var placeholder = imgDIR + 'video-placeholder.png';
			if( wrapper.find(".wp-block-embed__wrapper").length > 0 ) {
				wrapper.find(".wp-block-embed__wrapper").append('<img src="'+placeholder+'" alt="" aria-hidden="true" class="vidplaceholder">');
				wrapper.addClass("has-placeholder");
			}
		});
	}

});// END #####################################    END