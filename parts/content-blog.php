<div id="primary" class="content-area default singlepost">
	<div class="wrapper">

		<?php 
			$authorID = $obj->post_author;
			$authorName = get_the_author_meta('display_name',$authorID);
			$post_id = get_the_ID();
			$thumbnailId = get_post_thumbnail_id($post_id);
			$featImage = wp_get_attachment_image_src($thumbnailId,'large');
			$imgMeta = ($featImage) ? get_post($thumbnailId) : '';
			$imgALT = ($imgMeta) ? $imgMeta->post_title : '';
			$categories = get_the_terms( $post_id, 'category' );
			$categoryName = ($categories) ? $categories[0]->name : '';
			$categorySlug = ($categories) ? $categories[0]->slug : '';
			//print_r($categories);
		?>

		<?php /* MAIN CONTENT */ ?>
		<main id="main" data-id="<?php the_ID(); ?>" class="site-main" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<header class="postheader cf">
				<h1><?php the_title(); ?></h1>
				<?php if ($authorName) { ?>
				<div class="post-meta">
					<span class="by">By</span> <span class="red"><?php echo $authorName ?></span>
				</div>	
				<?php } ?>
			</header>
			<?php if ($featImage) { ?>
			<div class="featured-image">
				<img src="<?php echo $featImage[0] ?>" alt="<?php echo $imgALT ?>" />
			</div>	
			<?php } ?>
			<div class="post-container">
				<div class="inside"><?php the_content(); ?></div>
			</div>
		<?php endwhile; ?>
		</main>

		<?php /* SIDEBAR */ ?>
		<aside id="sidebar" class="sidebar">
			<div class="inside cf">

				<?php /* Search Form */ ?>
				<div class="widget searchform">
					<span class="srchIcon"><i class="fas fa-search"></i></span>
					<?php echo get_search_form(); ?>
				</div>
				
				<?php /* Posts */ ?>
				<div class="widget categoryList">
					<?php if ($categoryName) { ?>
					<h3 class="widget-title">More <?php echo $categoryName ?></h3>
					<?php } ?>

					<?php 
					$placeholder = THEMEURI . 'images/portrait.png';
					$paged = ( get_query_var( 'pg' ) ) ? absint( get_query_var( 'pg' ) ) : 1; 
					$args = array(
						'posts_per_page'=> -1,
						'post_type'		=> 'post',
						'post_status'	=> 'publish',
						'post__not_in' => array($post_id),
						'tax_query' => array( 
					        array(
					            'taxonomy' => 'category', 
					            'field'    => 'slug',
					            'terms'    => $categorySlug, 
					        ),
					    ) 
					);
					$perPage = 6;
					$allPost = get_posts($args);
					$totalpost = ($allPost) ? count($allPost) : 0;
					$catid = ( isset($categories[0]->term_id) && $categories[0]->term_id ) ? $categories[0]->term_id : '';
					$catTax = ( isset($categories[0]->taxonomy) && $categories[0]->taxonomy ) ? $categories[0]->taxonomy : '';
					if($categories) {
						$catInfo['taxonomy'] = 'category';
						$catInfo['term_id'] = $categories[0]->term_id;
					} else {
						$catInfo = null;
					}
					$the_post = get_blog_posts($paged,'post',$perPage,$post_id,$catInfo);
					?>


					<div class="sbPostList">
						<div id="postsList"><?php echo $the_post; ?></div>
						<?php if ($totalpost>$perPage) { ?>
						<div class="morePosts fwleft">
							<a href="#" id="morePostsBtn" data-posttype="post" data-currentid="<?php echo $post_id ?>" data-total="<?php echo $totalpost ?>" data-pg="2" data-perpage="<?php echo $perPage ?>" data-catid="<?php echo $catid ?>" data-taxonomy="<?php echo $catTax ?>"><span>More Posts <i class="arrowShape"></i></span></a>
						</div>
						<?php } ?>
					</div>
				</div>

				<?php /* Subscription */ ?>
				<div class="widget subscription">
					<div class="subscriptionWrap">
						<div class="smtxt">Signup to receive email updates and you will receive a</div>
						<div class="lgtxt">Free Basic<br>Survival Guide</div>
						<div class="subscriptionForm">
							<form action="">
								<input type="email" name="email" placeholder="Your Email Address" value="" />
								<button type="submit">Submit <i class="arrowShape"></i></button>
							</form>
						</div>
					</div>
				</div>
				
			</div>
		</aside>
	</div>
</div>