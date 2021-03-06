<?php 
if( is_front_page() ) { 
$banner = get_field("banner");
$caption = get_field("banner_caption");
?>
	<?php if ($banner) { ?>
	<div class="home-banner banner-wrapper fwleft">
		<img src="<?php echo $banner['url'] ?>" alt="<?php echo $banner['title'] ?>" />
		<?php if ($caption) { ?>
		<div class="banner-caption">
			<div class="caption"><span class="mid"><?php echo $caption ?></span></div>
		</div>	
		<?php } ?>
	</div>
	<?php } ?>

<?php } else { ?>

	<?php
	$placeholder = THEMEURI . 'images/rectangle.png';
	$postId = get_the_ID();
	$customPageTitle = get_field("custom_page_title");
	$pageTitle = get_the_title();
	$title = ($customPageTitle) ? $customPageTitle : $pageTitle;
	if( $banner = get_banner($postId) ) { ?>
	<div class="subpage-banner banner-wrapper fwleft">
		<div class="banner-image" style="background-image:url('<?php echo $banner['url']?>');"><img src="<?php echo $placeholder ?>" alt="" aria-hidden="true" class="placeholder"/>
			<div class="titlediv">
				<div class="wrapper">
					<h1><?php echo $title ?></h1>
				</div>
			</div>
		</div>
		<div class="banner-overlay"></div>
	</div>
	<?php } ?>

<?php } ?>