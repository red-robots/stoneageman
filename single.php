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

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
