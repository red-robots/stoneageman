<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package bellaworks
 */

get_header(); ?>

	<div id="primary" class="content-area default searchPage">
		<main id="main" class="site-main wrapper" role="main">

		<?php
		if ( have_posts() ) : ?>

			<header class="entry-header mainHeader">
				<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'bellaworks' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'parts/content', 'search' );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
