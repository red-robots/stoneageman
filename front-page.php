<?php get_header(); ?>
<div id="primary" class="content-area fwleft">

	<?php while ( have_posts() ) : the_post(); ?>

		<h1 style="display:none"><?php the_title(); ?></h1>
		<?php  
			$bg = THEMEURI . 'images/mountain-bg.png';
			$placeholder = THEMEURI . 'images/rectangle.png';
			$row2title = get_field("row2title");
			$row2text = get_field("row2text");
			$row2buttonName = get_field("row2buttonName");
			$row2buttonLink = get_field("row2buttonLink");
		?>
		<div class="section homerow1" style="background-image:url('<?php echo $bg ?>');">
			<!-- <div class="row1bg"><img src="<?php //echo $bg ?>" alt="" aria-hidden="true"></div> -->
			<div class="wrapper">
				<div class="flexwrap">
					<div class="textcol">
						<?php if ($row2title) { ?>
							<h2 class="stitle"><?php echo $row2title ?></h2>
						<?php } ?>
						<?php if ($row2text) { ?>
						<div class="stext">
							<?php echo $row2text; ?>
						</div>
						<?php } ?>

						<?php if ($row2buttonName && $row2buttonLink) { ?>
						<div class="sbutton">
							<a href="<?php echo $row2buttonLink ?>" class="btn-default"><?php echo $row2buttonName ?></a>
						</div>
						<?php } ?>
						
					</div>

					<?php  
					$videoThumb = get_field("row2VideoThumb");
					$videoThumbStyle = ($videoThumb) ? ' style="background-image:url('.$videoThumb['url'].')"':'';
					$row2VideoMP4 = get_field("row2VideoMP4");
					$video_type = '';
					$videoURL = '';
					if($row2VideoMP4) {
						$path = pathinfo($row2VideoMP4);
				    	$extension = ( isset($path['extension']) && $path['extension'] ) ? strtolower($path['extension']) : '';
				    	if($extension=='mp4') {
				    		$video_type = 'mp4';
				    		$videoURL = $row2VideoMP4;
				    	}
					}
					?>
					<div class="imagecol" <?php echo ($videoThumb) ? 'hasthumb':'nothumb'; ?>>
						<div class="video-wrap">
							<?php if ($video_type=='mp4') { ?>
							<div class="videoContainer hidden">
							<video id="mp4video" width="400" height="300" controls muted playsinline loop>
								<source src="<?php echo $videoURL; ?>" type="video/mp4">
							</video>
							</div>
							<?php } ?>
							<div class="videoThumb"<?php echo $videoThumbStyle ?>></div>
							<img src="<?php echo $placeholder ?>" alt="" aria-hidden="true" />
							<a href="#" id="playBtn" class="play"><span><i></i></span></a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php 
		$row3columns = get_field("row3columns"); 
		if($row3columns) { ?>
		<div class="homerow2 offers cf">
			<div class="wrapper">
				<div class="flexwrap">
					<?php $i=1; foreach ($row3columns as $col) { 
						$icon = $col['icon'];
						$text1 = $col['small_text'];
						$text2 = $col['large_text'];
						$link = $col['link'];
						$openLink = '';
						$closeLink = '';
						if($link) {
							$openLink = '<a href="'.$link.'" class="boxlink">';
							$closeLink = '</a>';
						}
						if($text1 || $text2) { 
							$boxClass = ($i % 2 == 0 ) ? 'even':'odd';
							$third = ($i % 3 == 0 ) ? ' third':'';
						?>
						<div class="fcol <?php echo $boxClass.$third ?>">
							<div class="inside">
								<?php echo $openLink ?>
								<?php if ($icon) { ?>
								<span class="icon"><img src="<?php echo $icon['url'] ?>" alt="<?php echo $icon['title'] ?>"></span>	
								<?php } ?>
								<?php if ($text1 || $text2) { ?>
								<span class="title">
									<?php if ($text1) { ?>
									<span class="smtxt"><?php echo $text1 ?></span>	
									<?php } ?>
									<?php if ($text2) { ?>
									<span class="lgtxt"><?php echo $text2 ?></span>	
									<?php } ?>
								</span>
								<?php } ?>
								<?php echo $closeLink ?>
							</div>
						</div>
						<?php $i++; } ?>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>


		<?php
		$row4image = get_field("row4image");
		$row4title = get_field("row4title");
		$row4text = get_field("row4text");
		$bg3 = THEMEURI . 'images/redbg.png';
		?>
		<?php if ($row4image || $row4text) { ?>
		<div class="homerow3 cf">
			<div class="wrapper">
				<div class="flexwrap">
					<div class="imagecol">
					<?php if ($row4image) { ?>
						<img src="<?php echo $row4image['url'] ?>" alt="<?php echo $row4image['title'] ?>">
					<?php } ?>
					</div>
					<div class="textcol">
						<div class="inner cf">
							<?php if ($row4title) { ?>
								<h2 class="rowtitle"><?php echo $row4title ?></h2>
							<?php } ?>
							<?php if ($row4text) { ?>
								<div class="rowtext"><?php echo $row4text ?></div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="bg3" style="background-image:url('<?php echo $bg3 ?>');"></div>
		</div>
		<?php } ?>		
	<?php endwhile; ?>

	<?php 
		$placeholder = THEMEURI . 'images/rectangle.png';
		$row5title = get_field("row5title"); 
		$args = array(
			'posts_per_page'   => 6,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'post',
			'post_status'      => 'publish'
		);
		$allArgs = array(
			'posts_per_page'   => -1,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'post',
			'post_status'      => 'publish'
		);
		$allBlogs = get_posts($allArgs);
		$totalBlogs = ($allBlogs) ? count($allBlogs) : 0;
		$blogs = new WP_Query($args);
		$blogPage = get_site_url() . '/blogs';
	if ( $blogs->have_posts() ) {  ?>
	<div class="homerow4">
		<div class="wrapper-wide">
			<?php if ($row5title) { ?>
				<h2 class="rowtitle"><?php echo $row5title ?></h2>
			<?php } ?>
			<div class="flexwrap">
				<?php while ( $blogs->have_posts() ) : $blogs->the_post(); 
				$post_id = get_the_ID();
				$thumbnail_id = get_post_thumbnail_id($post_id);
				$feat_image = wp_get_attachment_image_src($thumbnail_id,'medium_large');
				$post_title = get_the_title();
				$content = get_the_content();
				$content = ($content) ? strip_tags($content) : '';
				$excerpt = ($content) ? shortenText($content,60," ") : '';
				$pagelink = get_permalink(); ?>
				<div class="article">
					<div class="inner cf">
						<?php if($feat_image) { ?>
						<div class="featimage" style="background-image:url('<?php echo $feat_image[0]?>')">
							<img src="<?php echo $placeholder ?>" alt="" aria-hidden="true" />
						</div>
						<?php } ?>
						<div class="excerpt">
							<h3 class="posttitle"><?php echo $post_title ?></h3>
							<div class="text"><?php echo $excerpt ?></div>
							<div class="btndiv"><a href="<?php echo $pagelink ?>" class="more">Read More <i class="arrow"></i></a></div>
						</div>
					</div>
				</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<?php if ($totalBlogs>6) { ?>
			<div class="morediv">
				<a href="<?php echo $blogPage ?>" class="btn-default-arrow">More Blog Posts <i class="arrow"></i></a>
			</div>	
			<?php } ?>
		</div>
	</div>
	<?php } ?>

</div><!-- #primary -->
<?php
get_footer();
