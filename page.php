<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package bellaworks
 */
get_header(); 
$postId = get_the_ID();
$banner = get_banner($postId);
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
