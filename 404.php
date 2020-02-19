<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package bellaworks
 */

get_header(); ?>

<div id="primary" class="content-area default error404">
	<main id="main" class="site-main wrapper" role="main">
		
		<header class="entry-header">
			<h1 class="entry-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'bellaworks' ); ?></h1>
		</header><!-- .page-header -->
		<div class="page-content">
			<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'bellaworks' ); ?></p>
			<div class="searchFormWrap">
				<div class="searchform"><span class="srchIcon"><i class="fas fa-search"></i></span><?php get_search_form(); ?></div>
			</div>
			
			<?php get_template_part('parts/content','sitemap'); ?>
		</div>

	</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
