<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bellaworks
 */

get_header(); 
?>

<div id="primary" class="content-area default singlepost">
	<div class="wrapper">

		<?php 
			$obj = get_queried_object();
			$authorID = $obj->post_author;
			$authorName = get_the_author_meta('display_name',$authorID);
			$authorName = ($authorName) ? ucwords($authorName) : '';
			$post_id = get_the_ID();
			$postType = 'trips';
			$categories = array();
			$categoryName = ($categories) ? $categories[0]->name : '';
			$categorySlug = ($categories) ? $categories[0]->slug : '';
			$placeholder = THEMEURI . 'images/square.png';
			$featImage = get_field("full_image");
			$imgALT = ($featImage) ? $featImage['title'] : '';
			$authorBio = get_the_author_meta('description',$authorID); 
		?>

		<?php /* MAIN CONTENT */ ?>
		<main id="main" data-id="<?php the_ID(); ?>" class="site-main" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<header class="postheader cf">
				<h1><?php the_title(); ?></h1>
				<?php if ($authorName) { ?>
				<div class="post-meta">
					<span class="by">By</span>
					<?php if ($authorBio) { ?>
					<a href="#author" class="red"><?php echo $authorName ?></a>
					<?php } else { ?>
					<span class="red"><?php echo $authorName ?></span>
					<?php } ?>
				</div>	
				<?php } ?>
			</header>
			<?php if ($featImage) { ?>
			<div class="featured-image">
				<img src="<?php echo $featImage['url'] ?>" alt="<?php echo $imgALT ?>" />
			</div>	
			<?php } ?>
			<div class="post-container">
				<div class="inside"><?php the_content(); ?></div>
			</div>
		<?php endwhile; ?>

		<?php /* AUTHOR INFO */ ?>
		<?php if ($authorBio) { ?>
		<?php  $pic = get_avatar_url($authorID); ?>
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
		<?php get_sidebar('trip'); ?>

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

<?php
get_footer();
