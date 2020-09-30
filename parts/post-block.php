<?php 
	$post_date = get_the_date();
	$placeholder = THEMEURI . 'images/rectangle.png';
	$the_post_date = date('F Y',strtotime($post_date));	
	$custom_post_date = get_field('date_subtitle');	
	$post_id = get_the_ID();
	$thumbnail_id = get_post_thumbnail_id($post_id);
	$featImage = wp_get_attachment_image_src($thumbnail_id,'large');
	$thumbImage = get_field("thumbnail_image");
	$hasImg = ($thumbImage) ? 'hasphoto':'nophoto';
	$imageStyle = ($thumbImage) ? ' style="background-image:url('.$thumbImage['sizes']['medium_large'].')"':'';
 ?>
<div class="post-entry <?php echo $hasImg ?><?php echo $isSticky ?>">
	<div class="inner clear">
		<div class="inside clear">
			<div class="featimage <?php echo $hasImg; ?>"<?php echo $imageStyle ?>>
				<a href="<?php echo $pagelink; ?>"><img src="<?php echo $placeholder;  ?>" alt="" aria-hidden="true" /></a>
			</div>
			<div class="textwrap clear">
				<header class="post-info">
					<h3 class="post-title"><?php the_title(); ?></h3>
					<?php if($custom_post_date) { ?>
					<div class="date"><?php echo $custom_post_date; ?></div>
					<?php } ?>
				</header>
				<!-- <p class="excerpt"><?php echo $excerpt; ?></p> -->
				<div class="buttondiv">
					<a class="more" href="<?php the_permalink(); ?>">Read More <i class="arrow"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>	