jQuery(document).ready(function($){

	$(document).on("click","#morePostsBtn",function(e){
		e.preventDefault();
		var target = $(this);
		var paged = target.attr('data-pg');
		var perPage = target.attr('data-perpage');
		var newPg = ( parseInt(paged) ) + 1;
		target.attr('data-pg',newPg);
		var catid = target.attr('data-catid');
		var tax = target.attr('data-taxonomy');

		var total = target.attr('data-total');
		var post_type = target.attr('data-posttype');

		$.ajax({
			url : myAjax.ajaxurl,
			type : 'post',
			dataType : "json",
			data : {
				action : 'get_next_posts',
				pg : paged,
				posttype : post_type,
				perpage: perPage,
				termid: catid,
				taxonomy: tax
			},
			beforeSend:function(){
				//$("#loaderdiv").addClass("show");
			},
			success : function( obj ) {
				var newPosts = obj.content;
				var isLastBatch = obj.isLastBatch;
				console.log(obj);
				if(newPosts) {
					$("#postsList").append(newPosts);
					if(isLastBatch) {
						var noMorePosts = '<div class="noMore">No more posts to load.</div>';
						$(".morePosts").html(noMorePosts);
					}
					//target.attr('data-pg',nextpage);
					// setTimeout(function(){
					// 	$("#loaderdiv").removeClass("show");
					// 	$(".postresults .postflex").append(result);
					// 	var count = $(".post-item").length;
					// 	if(total==count) {
					// 		$(".lastposts").removeClass("hide");
					// 		target.hide();
					// 	}
					// },300);
				} else {
					// $("#loaderdiv").removeClass("show");
					// $(".lastposts").removeClass("hide");
					// target.hide();
				}
			}
		});
	});

});