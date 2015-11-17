<?
if ( current_user_can( 'unconfigured' ) ){
	wp_redirect(site_url('member-registration'));
	exit;
}
?>
<?php get_header(); ?>

  <h1 class="welcome"><span class="orange">Welcome Grindist.</span><br>Come. Sit. Conquer.</h1>

<?php // ************** CHECKINS ************    ?>
	<div id="checkinWrapper">
	</div>



	
	
	
	
	
<?php // ************** SLIDESHOW ************    ?>	

		<?php if (is_home()) {
			
			// show promo
			?>
			<div id="gallerypromo" class="clearfix">
				<div class="left"><img src="<?= get_bloginfo('stylesheet_directory'); ?>/img/promos/gallerypromo-w.png" width="480" height="100" alt="Grind Gallery Wall"/></div>
				<div class="right"><div class="title">Be part of the grind gallery</div>Use the gallery builder to upload images, then just touch your membership card to the wall to show them off (Champagne and passed Hors d'oeurves not included).<br /><br />
				<a href="http://wall.grindspaces.com" class="btn" target="_blank">Launch gallery builder</a>
				</div>
			</div>
		
			<?
			// end promo
			/*
			// insert feed
			$content = file_get_contents(site_url("/ci/feed/displayslideshow"));
			echo $content;
			*/
		} else { ?>

			  <?php while (have_posts()) : the_post(); ?>
				<h2><?php the_title();?></h2>
				<div><?php the_content();?></div>
			  <?php endwhile;?>
			  </ul>
		<?php } ?>

	  </div>
  <!-- end #inspiration-->
<script type="text/javascript" src="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/protovis.min.js"></script>
<?php get_footer(); ?>
