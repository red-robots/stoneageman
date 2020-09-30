<?php 
	$promoImg = get_field("footer_promotion_image","option");
	$promoLink = get_field("footer_promotion_link","option");

	$socialOptions = get_social_links();
	$footlogo = get_field("footlogo","option");
?>

<?php if( $promoImg ) { ?>
	<div class="promo">
		<?php if( $promoLink ) { ?><a href="<?php echo $promoLink; ?>"><?php } ?>
			<img src="<?php echo $promoImg['url']; ?>" alt="<?php echo $promoImg['alt']; ?>">
		<?php if( $promoLink ) { ?></a><?php } ?>
	</div>
<?php } ?>

	</div><!-- #content -->

	
	<footer id="colophon" class="site-footer cf" role="contentinfo">
		<div class="wrapper">
			<div class="flexwrap">
				<div class="footlogo footcol">
					<?php if ($footlogo) { ?>
					<img src="<?php echo $footlogo['url'] ?>" alt="<?php echo $footlogo['title'] ?>" class="foot-logo">	
					<?php } ?>
				</div>
				<div class="footnavs footcol">
					<?php wp_nav_menu( array( 'menu' => 'Footer', 'menu_id' => 'footer-menu','container_class'=>'footerMenu' ) ); ?>
				</div>

				<div class="socialMedia footcol">
					<?php foreach ($socialOptions as $k=>$s) { 
					$link = $s['link'];
					$iconClass = $s['icon']; 
					$name = $s['name'];
					$socialClass = strtolower($name);
					?>
					<a href="<?php echo $link ?>" target="_blank" class="<?php echo $socialClass ?>"><i class="<?php echo $iconClass ?>"></i><span class="sr"><?php echo $name ?></span></a>
					<?php } ?>
				</div>
			</div>
		</div><!-- wrapper -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<script src="https://www.youtube.com/iframe_api"></script>
<?php wp_footer(); ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-159268763-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-159268763-1');
</script>
</body>
</html>
