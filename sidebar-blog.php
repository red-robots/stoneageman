<?php
$obj = get_queried_object();
$post_id = get_the_ID();
$categories = get_the_terms( $post_id, 'category' );
// $categoryName = ($categories) ? $categories[0]->name : '';
// $categorySlug = ($categories) ? $categories[0]->slug : '';


// SHOW YOAST PRIMARY CATEGORY, OR FIRST CATEGORY
$category = get_the_terms( $post_id, 'category' );
// If post has a category assigned.
if ($category){
	$category_display = '';
	$category_link = '';
	if ( class_exists('WPSEO_Primary_Term') )
	{
		// Show the post's 'Primary' category, if this Yoast feature is available, & one is set
		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
		$term = get_term( $wpseo_primary_term );
		if (is_wp_error($term)) { 
			// Default to first category (not Yoast) if an error is returned
			$categoryName = $category[0]->name;
			$categorySlug = $category[0]->slug;
		} else { 
			// Yoast Primary category
			$categoryName = $term->name;
			$categorySlug = $term->slug;
		}
	} 
	else {
		// Default, display the first category in WP's list of assigned categories
		$categoryName = $category[0]->name;
	}
}

// echo '<pre>';
// print_r($categorySlug);
// echo '</pre>';

$postType = 'post';
$promoImg = get_field("sidebar_promotion_image","option");
$promoLink = get_field("sidebar_promotion_link","option");
?>
<aside id="sidebar" class="sidebar">
	<div class="inside cf">

		<?php /* Search Form */ ?>
		<div class="widget searchform">
			<span class="srchIcon"><i class="fas fa-search"></i></span>
			<?php echo get_search_form(); ?>
		</div>

		<?php if( $promoImg ) { ?>
			<div class="promo widget">
				<?php if( $promoLink ) { ?><a href="<?php echo $promoLink; ?>"><?php } ?>
					<img src="<?php echo $promoImg['url']; ?>" alt="<?php echo $promoImg['alt']; ?>">
				<?php if( $promoLink ) { ?></a><?php } ?>
			</div>
		<?php } ?>
		
		<?php 
			/* Posts */ 
			$placeholder = THEMEURI . 'images/portrait.png';
			$paged = ( get_query_var( 'pg' ) ) ? absint( get_query_var( 'pg' ) ) : 1; 
			$args = array(
				'posts_per_page'=> -1,
				'post_type'		=> $postType,
				'post_status'	=> 'publish',
				'post__not_in' => array($post_id),
				// 'tax_query' => array( 
			 //        array(
			 //            'taxonomy' => 'category', 
			 //            'field'    => 'slug',
			 //            'terms'    => $categorySlug, 
			 //        ),
			 //    ) 
			);

			if($categories) {
				$args['tax_query'] = array( 
			        array(
			            'taxonomy' => 'category', 
			            'field'    => 'slug',
			            'terms'    => $categorySlug, 
			        ),
			    );
			}
			$perPage = 6;
			$allPost = get_posts($args);
		if($allPost) { ?>
		<div class="widget categoryList">
			<?php if ($categoryName) { ?>
			<!-- <h3 class="widget-title">More <?php //echo $categoryName ?></h3> -->
			<h3 class="widget-title">More <?php get_template_part('inc/primary-cat-name'); ?></h3>
			<?php } ?>

			<?php 
			$totalpost = ($allPost) ? count($allPost) : 0;
			$catid = ( isset($categories[0]->term_id) && $categories[0]->term_id ) ? $categories[0]->term_id : '';
			$catTax = ( isset($categories[0]->taxonomy) && $categories[0]->taxonomy ) ? $categories[0]->taxonomy : '';
			if($categories) {
				$catInfo['taxonomy'] = 'category';
				$catInfo['term_id'] = $categories[0]->term_id;
			} else {
				$catInfo = null;
			}
			$the_post = get_blog_posts($paged,$postType,$perPage,$post_id,$catInfo);
			// echo '<pre>';
			// print_r($the_post);
			// echo '</pre>';
			?>


			<div class="sbPostList">
				<div id="postsList"><?php echo $the_post; ?></div>
				<?php if ($totalpost>$perPage) { ?>
				<div class="morePosts fwleft">
					<a href="#" id="morePostsBtn" data-posttype="<?php echo $postType ?>" data-currentid="<?php echo $post_id ?>" data-total="<?php echo $totalpost ?>" data-pg="2" data-perpage="<?php echo $perPage ?>" data-catid="<?php echo $catid ?>" data-taxonomy="<?php echo $catTax ?>"><span>More Posts <i class="arrowShape"></i></span></a>
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


		<?php  
		/* Featured Post */
		$sbfeatImg = get_field("sb_fp_image","option"); 
		$sbText1 = get_field("sb_fp_text1","option"); 
		$sbText2 = get_field("sb_fp_text2","option"); 
		$sbHasText = ($sbText1 || $sbText2) ? true : false;
		$sbLinkType = get_field("sb_link_type","option"); 
		$sbType = ($sbLinkType) ? strtolower($sbLinkType) : '';
		$sbArrs = array($sbfeatImg,$sbText1,$sbText2);
		$sbContent = ($sbArrs && array_filter($sbArrs)) ? true : false;
		$sbOpenLink = '';
		$sbCloseLink = '';
		$target = ($sbType=='external') ? ' target="_blank"':'';
		if($sbType) {
			$link = get_field("sblink_".$sbType,"option");
			if($link) {
				$sbOpenLink = '<a href="'.$link.'"'.$target.' class="sblink">';
				$sbCloseLink = '</a>';
			}
		}

		if($sbContent) { ?>
		<div class="widget featuredArticle">
			<div class="inside">
				<?php echo $sbOpenLink; ?>
				<?php if ($sbfeatImg) { ?>
					<img src="<?php echo $sbfeatImg['sizes']['medium_large'] ?>" alt="<?php echo $sbfeatImg['title'] ?>" class="fi" />
				<?php } ?>

				<?php if ($sbHasText) { ?>
					<span class="fatext">
					<?php if ($sbText1) { ?>
						<span class="sm"><?php echo $sbText1 ?></span>
					<?php } ?>
					<?php if ($sbText2) { ?>
						<span class="lg"><?php echo $sbText2 ?></span>
					<?php } ?>
					</span>
				<?php } ?>
				
				<?php echo $sbCloseLink; ?>
			</div>
		</div>
		<?php } ?>
	</div>
</aside>