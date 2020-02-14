<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css?family=Oswald:200,300,400,500,600,700|Roboto+Slab:100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">
<?php wp_head(); ?>
</head>
<?php
$postId = get_the_ID();
$banner = get_banner($postId);
$hasBanner = ($banner) ? 'hasbanner':'nobanner';
?>

<body <?php body_class($hasBanner); ?>>
<div id="page" class="site cf">
	<a class="skip-link sr" href="#content"><?php esc_html_e( 'Skip to content', 'bellaworks' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="redVLines"></div>
		<div class="wrapper">
			
			<?php if( get_custom_logo() ) { ?>
	            <div class="logo">
	            	<?php the_custom_logo(); ?>
	            </div>
	        <?php } else { ?>
	            <h1 class="logo">
		            <a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a>
	            </h1>
	        <?php } ?>
			
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><span class="sr"><?php esc_html_e( 'MENU', 'bellaworks' ); ?></span><span class="bar"></span></button>
			<nav id="site-navigation" class="main-navigation" role="navigation">
				<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu','container_class'=>'main-nav-container','link_before'=>'<span>','link_after'=>'</span><i class="arrowUp" aria-hidden="true"></i>' ) ); ?>
			</nav><!-- #site-navigation -->
		</div><!-- wrapper -->
	</header><!-- #masthead -->

	<?php get_template_part("parts/banner"); ?>

	<div id="content" class="site-content fwleft">
