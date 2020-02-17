/**
 *	Custom jQuery Scripts
 *	
 *	Developed by: Austin Crane	
 *	Designed by: Austin Crane
 */

jQuery(document).ready(function ($) {
	
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


	$(document).on("click",".menu-toggle",function(){
		$(this).toggleClass('open');
		$('body').toggleClass('openNav');
	});

});// END #####################################    END