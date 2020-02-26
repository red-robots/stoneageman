<?php
/**
 * Template Name: Content with Sidebar
 */
get_header(); 
$postId = get_the_ID();
$banner = get_banner($postId);
?>

<div id="primary" class="content-area default singlepost">
	<div id="post-<?php the_ID(); ?>" class="wrapper">
		<main id="main" class="site-main wrapper" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
			 <header class="postheader cf">
			 	<h1><?php the_title(); ?></h1>
			 </header>

			 <div class="post-container">
				<div class="inside"><?php the_content(); ?></div>
			</div>
			<?php endwhile; ?>

		</main><!-- #main -->

		<?php /* SIDEBAR */ ?>
		<aside id="sidebar" class="sidebar">
			<div class="inside cf">
				<?php 
				/* Subscription */ 
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
</div><!-- #primary -->

<?php
get_footer();
