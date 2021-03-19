<?php
/**
 * Template Name: Learn
 */
get_header(); 
$postId = get_the_ID();
$banner = get_banner($postId);
?>

	<div id="primary" class="content-area default">
		<main id="main" data-id="<?php the_ID(); ?>" class="site-main wrapper" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
			 <div class="entry-content">
			 	<?php the_content(); ?>
			 </div>
			<?php endwhile; ?>

			<?php

			$muhTerms = get_terms('category');

			foreach($muhTerms as $sTerms) {
				$cSlug = $sTerms->slug;
				$link = get_term_link( $sTerms->term_id );
				// echo '<pre>';
				// print_r($sTerms);
				// echo '</pre>';


			$perPage = get_the_per_page();
			// echo $perPage;
			$paged = ( get_query_var( 'pg' ) ) ? absint( get_query_var( 'pg' ) ) : 1;
			$args = array(
				// 'posts_per_page'   => $perPage,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'post',
				'post_status'      => 'publish',
				'paged'			   => $paged,
				'category_name'    => $cSlug,
				//'post__not_in'     => array($post_id),
				'posts_per_page'   => '-1'
			);
			$blogs = new WP_Query($args);
			$placeholder = THEMEURI . 'images/rectangle.png';
			if ( $blogs->have_posts() ) :  ?>
				<div class="cat-title">
					<h2><?php echo $sTerms->name; ?></h2>
				</div>
			<div class="bloglist clear">
				<!-- <div class="flexwrap blog-inner clear"> -->
					<div class="flexslider learning carousel clear">
						<ul class="slides">
					<?php while ( $blogs->have_posts() ) : $blogs->the_post(); 
						$post_id = get_the_ID();
						$thumbnail_id = get_post_thumbnail_id($post_id);
						$featImage = wp_get_attachment_image_src($thumbnail_id,'large');
						$post_title = get_the_title();
						$content = get_the_content();
						$content = strip_tags($content);
						$excerpt = shortenText($content,110," ");
						$post_date = get_the_date();
						$the_post_date = date('F Y',strtotime($post_date));	
						$custom_post_date = get_field('date_subtitle');	
						$pagelink = get_permalink(); 
						//$imageStyle = ($featImage) ? ' style="background-image:url('.$featImage[0].')"':'';
						$thumbImage = get_field("thumbnail_image");
						$hasImg = ($thumbImage) ? 'hasphoto':'nophoto';
						$imageStyle = ($thumbImage) ? ' style="background-image:url('.$thumbImage['sizes']['medium_large'].')"':'';
						$isSticky = ( is_sticky($post_id) ) ? ' sticky':'';
						?>
						<li>
						<div class="post-entry <?php echo $hasImg ?><?php echo $isSticky ?>">
							<div class="inner clear">
								<div class="inside clear">
									<div class="featimage <?php echo $hasImg ?>"<?php echo $imageStyle ?>>
										<a href="<?php echo $pagelink; ?>"><img src="<?php echo $placeholder  ?>" alt="" aria-hidden="true" /></a>
									</div>
									<div class="textwrap clear">
										<header class="post-info">
											<h3 class="post-title"><?php echo $post_title; ?></h3>
											<?php if($custom_post_date) { ?>
											<div class="date"><?php echo $custom_post_date; ?></div>
											<?php } ?>
										</header>
										<!-- <p class="excerpt"><?php echo $excerpt; ?></p> -->
										<div class="buttondiv">
											<a class="more" href="<?php echo $pagelink; ?>">Read More <i class="arrow"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>	
						
					<?php endwhile; wp_reset_postdata(); ?>
					
				</div>
				<div class="readall-naked">
					<a href="<?php echo $link; ?>">
						View All <?php echo $sTerms->name; ?> <i class="fas fa-chevron-circle-right"></i>
					</a>
				</div>

				<?php
	            $total_pages = $blogs->max_num_pages;
	            if ($total_pages > 1){ ?>
	                <div id="pagination" class="pagination">
	                    <?php
	                        $pagination = array(
	                            'base' => @add_query_arg('pg','%#%'),
	                            'format' => '?paged=%#%',
	                            'current' => $paged,
	                            'total' => $total_pages,
	                            'prev_text' => __( '&laquo;', 'red_partners' ),
	                            'next_text' => __( '&raquo;', 'red_partners' ),
	                            'type' => 'plain'
	                        );
	                        //echo paginate_links($pagination);
	                    ?>
	                </div>
	                </li>
	                <?php
	            } ?>
	            </ul>
	        </div>
			<?php endif;
		} // end for loop?>


		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
