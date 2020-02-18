<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bellaworks
 */

get_header(); 
$postType = get_post_type();
$obj = get_queried_object();
?>
<?php if ($postType=='post') { ?>

	<?php get_template_part("parts/content","blog"); ?>

<?php } else { ?>
<div id="primary" class="content-area default">
	<main id="main" data-id="<?php the_ID(); ?>" class="site-main wrapper" role="main">

		<?php while ( have_posts() ) : the_post(); ?>
		 <header class="entry-header">
		 	<h1><?php the_title(); ?></h1>
		 </header>

		 <div class="entry-content">
		 	<?php the_content(); ?>
		 </div>
		<?php endwhile; ?>

	</main>
</div>
<?php } ?>

<?php
get_footer();
