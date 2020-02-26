<?php
/**
 * Template Name: Trips
 */
get_header(); 
$postId = get_the_ID();
$banner = get_banner($postId);
?>

	<div id="primary" class="content-area default tripspage">
		<main id="main" data-id="<?php the_ID(); ?>" class="site-main wrapper" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
			 <div class="entry-content">
			 	<?php the_content(); ?>
			 </div>
			<?php endwhile; ?>

			<?php
			$perPage = get_the_per_page();
			$paged = ( get_query_var( 'pg' ) ) ? absint( get_query_var( 'pg' ) ) : 1;
			$args = array(
				'posts_per_page'   => $perPage,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'trips',
				'post_status'      => 'publish',
				'paged'			   => $paged
			);
			$blogs = new WP_Query($args);
			$placeholder = THEMEURI . 'images/rectangle.png';
			if ( $blogs->have_posts() ) {  ?>
			<div class="tripsfeeds cf">
				<div class="blog-inner cf">
					<?php while ( $blogs->have_posts() ) : $blogs->the_post(); 
						$post_id = get_the_ID();
						$featImage = get_field("full_image");
						$description = get_field("trip_short_description");
						$startDate = get_field("trip_start_date");
						$endDate = get_field("trip_end_date");
						$dates = array($startDate,$endDate);
						$eventDate = '';
						if($dates && array_filter($dates)) {
							$countArrs = count( array_filter($dates) );
							$eventDate = ($countArrs > 1) ? implode(" &ndash; ", $dates) : implode("", $dates);
						}
						$styleImg = ($featImage) ? ' style="background-image:url('.$featImage['sizes']['medium_large'].')"':'';
						?>
						<div id="post<?php the_ID(); ?>" class="entry cf">
							<div class="flexwrap">
								<div class="imgcol <?php echo ($featImage) ? 'haspic':'nopic'; ?>"<?php echo $styleImg ?>>
									<img src="<?php echo $placeholder ?>" alt="" aria-hidden="true">
								</div>
								<div class="txtcol">
									<div class="wrap">
										<h2 class="title"><?php echo get_the_title(); ?></h2>
										<?php if ($eventDate) { ?>
										<!-- <div class="info"><span class="lbl">Date:</span> <?php //echo $eventDate ?></div> -->
										<?php } ?>
										<?php if ($description) { ?>
										<div class="info"><?php echo $description ?></div>	
										<?php } ?>
										<div class="btndiv">
											<a href="<?php echo get_permalink(); ?>" class="btn-default-arrow">View Trip <i class="arrow"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endwhile; wp_reset_postdata(); ?>
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
	                        echo paginate_links($pagination);
	                    ?>
	                </div>
	                <?php
	            } ?>

	        </div>
			<?php } ?>


		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
