<?php
$obj = get_queried_object();
$post_id = get_the_ID();
//$categories = get_the_terms( $post_id, 'category' );
$categories = false;
$categoryName = ($categories) ? $categories[0]->name : '';
$categorySlug = ($categories) ? $categories[0]->slug : '';
$postType = 'trips';
?>
<aside id="sidebar" class="sidebar">
	<div class="inside cf">

		<?php /* Search Form */ ?>
		<div class="widget searchform">
			<span class="srchIcon"><i class="fas fa-search"></i></span>
			<?php echo get_search_form(); ?>
		</div>
		
		<?php 
			/* Posts */ 
			$placeholder = THEMEURI . 'images/portrait.png';
			$paged = ( get_query_var( 'pg' ) ) ? absint( get_query_var( 'pg' ) ) : 1; 
			$args = array(
				'posts_per_page'=> -1,
				'post_type'		=> $postType,
				'post_status'	=> 'publish',
				'post__not_in' => array($post_id),
			);


			// if($categories) {
			// 	$args['tax_query'] = array( 
			//         array(
			//             'taxonomy' => 'category', 
			//             'field'    => 'slug',
			//             'terms'    => $categorySlug, 
			//         ),
			//     );
			// }

			$perPage = 6;
			$allPost = get_posts($args);
		if($allPost) { ?>
		<div class="widget categoryList">
			<?php if ($categoryName) { ?>
			<h3 class="widget-title">More <?php echo $categoryName ?></h3>
			<?php } else { ?>
			<h3 class="widget-title">More Trips</h3>
			<?php } ?>

			<?php 
			$totalpost = ($allPost) ? count($allPost) : 0;
			// $catid = ( isset($categories[0]->term_id) && $categories[0]->term_id ) ? $categories[0]->term_id : '';
			// $catTax = ( isset($categories[0]->taxonomy) && $categories[0]->taxonomy ) ? $categories[0]->taxonomy : '';
			// if($categories) {
			// 	$catInfo['taxonomy'] = 'category';
			// 	$catInfo['term_id'] = $categories[0]->term_id;
			// } else {
			// 	$catInfo = null;
			// }

			$catInfo = null;
			$the_post = get_blog_posts($paged,$postType,$perPage,$post_id,$catInfo);
			?>


			<div class="sbPostList">
				<div id="postsList"><?php echo $the_post; ?></div>
				<?php if ($totalpost>$perPage) { ?>
				<div class="morePosts fwleft">
					<a href="#" id="morePostsBtn" data-posttype="<?php echo $postType ?>" data-currentid="<?php echo $post_id ?>" data-total="<?php echo $totalpost ?>" data-pg="2" data-perpage="<?php echo $perPage ?>" data-catid="<?php echo $catid ?>" data-taxonomy="<?php echo $catTax ?>"><span>More Trips <i class="arrowShape"></i></span></a>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>

		<?php 
		/* Subscription */ 
		$subscribeToptext = get_field("subscribe_small_text","option");
		$subscribeBottomtext = get_field("subscribe_large_text","option");
		$subscribe_form = get_field("subscribe_form","option");
		if($subscribe_form) { ?>
		<div class="widget subscription">
			<div class="subscriptionWrap">
				<?php if ($subscribeToptext) { ?>
					<div class="smtxt"><?php echo $subscribeToptext ?></div>
				<?php } ?>
				<?php if ($subscribeBottomtext) { ?>
					<div class="lgtxt"><?php echo $subscribeBottomtext ?></div>
				<?php } ?>
				<div class="subscriptionForm">
					<?php echo $subscribe_form; ?>
				</div>
			</div>
		</div>
		<?php } ?>
		
	</div>
</aside>