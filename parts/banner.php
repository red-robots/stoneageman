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

<?php } ?>