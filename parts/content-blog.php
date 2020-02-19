<div id="primary" class="content-area default singlepost">
	<div class="wrapper">

		<?php 
			$obj = get_queried_object();
			$authorID = $obj->post_author;
			$authorName = get_the_author_meta('display_name',$authorID);
			$authorName = ($authorName) ? ucwords($authorName) : '';
			$post_id = get_the_ID();
			$thumbnailId = get_post_thumbnail_id($post_id);
			$featImage = wp_get_attachment_image_src($thumbnailId,'large');
			$imgMeta = ($featImage) ? get_post($thumbnailId) : '';
			$imgALT = ($imgMeta) ? $imgMeta->post_title : '';
			$categories = get_the_terms( $post_id, 'category' );
			$categoryName = ($categories) ? $categories[0]->name : '';
			$categorySlug = ($categories) ? $categories[0]->slug : '';
			$placeholder = THEMEURI . 'images/square.png';
			//print_r($categories);
		?>

		<?php /* MAIN CONTENT */ ?>
		<main id="main" data-id="<?php the_ID(); ?>" class="site-main" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<header class="postheader cf">
				<h1><?php the_title(); ?></h1>
				<?php if ($authorName) { ?>
				<div class="post-meta">
					<span class="by">By</span> <a href="#author" class="red"><?php echo $authorName ?></a>
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

		<?php /* AUTHOR INFO */ ?>
		<?php if ($authorID) { ?>
			<?php 
			$pic = get_avatar_url($authorID);
			$authorBio = get_the_author_meta('description',$authorID); 
			?>
		<div id="author" class="author-info fwleft">
			<div class="head">About The Author</div>
			<div class="description">
				<div class="photo <?php echo ($pic) ? 'haspic':'nopic'; ?>">
					<div class="img">
					<?php if ($pic) { ?>
						<img src="<?php echo $pic ?>" alt="<?php echo $authorName ?>" />
					<?php } else { ?>
						<i class="fas fa-user nopic"></i>
						<img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
					<?php } ?>
					</div>
				</div>
				<div class="text">
					<?php if ($authorName) { ?>
					<h3 class="name"><?php echo $authorName; ?></h3>
					<?php } ?>
					<?php echo $authorBio; ?>
				</div>
			</div>
		</div>	
		<?php } ?>
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
				<?php 
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

	</div>

	<?php /* BOTTOM COLUMNS */ ?>
	<?php 
	$featuredItems = get_field("featured_items","option"); 
	if($featuredItems) { ?>
	<section class="featuredItems fwleft">
		<div class="wrapper">
			<div class="flexwrap">
				<?php $i=1; foreach ($featuredItems as $col) { 
					$icon = $col['icon'];
					$text1 = $col['small_text'];
					$text2 = $col['large_text'];
					$link = $col['link'];
					$openLink = '';
					$closeLink = '';
					if($link) {
						$openLink = '<a href="'.$link.'" class="boxlink">';
						$closeLink = '</a>';
					}
					if($text1 || $text2) { 
						$boxClass = ($i % 2 == 0 ) ? 'even':'odd';
						$third = ($i % 3 == 0 ) ? ' third':'';
					?>
					<div class="fcol <?php echo $boxClass.$third ?>">
						<div class="inside">
							<?php echo $openLink ?>
							<?php if ($icon) { ?>
							<span class="icon"><img src="<?php echo $icon['url'] ?>" alt="<?php echo $icon['title'] ?>"></span>	
							<?php } ?>
							<?php if ($text1 || $text2) { ?>
							<span class="title">
								<?php if ($text1) { ?>
								<span class="smtxt"><?php echo $text1 ?></span>	
								<?php } ?>
								<?php if ($text2) { ?>
								<span class="lgtxt"><?php echo $text2 ?></span>	
								<?php } ?>
							</span>
							<?php } ?>
							<?php echo $closeLink ?>
						</div>
					</div>
					<?php $i++; } ?>
				<?php } ?>
			</div>
		</div>
	</section>
	<?php } ?>
</div>