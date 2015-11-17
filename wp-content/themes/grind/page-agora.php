<?php

header("HTTP/1.1 301 Moved Permanently"); 
header("Location: http://agora.grindspaces.com"); 
/**
 * Template Name: Agora
 *
 * @package: WordPress
 * @subpackage: members.grindspaces.com
 * @author: @mattkosoy
 */

get_header(); 

/* a bit of preprocessing */
$checked_in_users = grind_get_who_has_checkedin();
$to_include = array();
foreach($checked_in_users as $c){
	$to_include[] = $c->wp_users_id;
}

$here_args = array(
	'blog_id' => $GLOBALS['blog_id'],
	'orderby' => 'login',
	'order' => 'ASC',
	'fields' => 'all',
	'exclude' => array(1),
	'include' => $to_include
 );
 

// add the admin user to our 'exclude' list. 
$to_exclude = $to_include;
$to_exclude[] = 1;

$not_here_args = array(
	'blog_id' => $GLOBALS['blog_id'],
	'orderby' => 'login',
	'order' => 'ASC',
	'fields' => 'all',
	'exclude' => $to_exclude
 );
if (count($checked_in_users) >0){
	if(is_numeric($to_include[0])){
		$here 	=	get_users($here_args);
	}

	#print_r($here);
} else {
	$here = false;
}
$not 	=	get_users($not_here_args);

#print_r($not);

// the fields we're using.
$fields = array(
	'first_name'	=>	'',
	'last_name'		=>	'',
	'email'			=>	'',
	'company_name'	=>	'',
	'company_desc'	=>	'',
	'phone'			=>	'',
	'URL'			=>	'',
	'twitter'		=>  '',
	'behance'		=>  '',
	'foursquare'	=>	'',
	'linkedin'		=>	'',
	'facebook'		=>	'',
	'dribble'		=>	'',
	'i_need'		=>	''
);



?>

<h1 class="welcome"><span class="orange">Welcome Grindist.</span><br/>Come.  Sit. Conquer.</h1>

<?php /* ************** CHECKINS ************   */ ?>
	<div id="checkinWrapper">
		<?php $checkinData = grind_get_location_checkins();
			foreach($checkinData as $location) { ?>
				<div class="checkinLocationWrapper">
					<h3>Grindists<br>Checked In<br>Today</h3>
					<div id="chart-holder">
            		<div class="pie">
    					<?php
    					$capacity = intval($location->capacity);
    					$checkins = intval($location->checkins);
    	
    					?>
    					<script type="text/javascript+protovis">
      					var checkins = <?= $checkins?>;
      					var total = <?=$capacity?>;
      					var remainder = total-checkins
      					var data = [checkins,remainder], sum = pv.sum(data);
      					
      					var vis = new pv.Panel()
      					    .width(130)
      					    .height(130);
      					
      					vis.add(pv.Wedge)
      					    .data(data)
      					    .left(65)
      					    .bottom(65)
      					    .innerRadius(48)
      					    .outerRadius(65)
      					    .strokeStyle("white")
      					     .lineWidth(0)
      					     .fillStyle(pv.colors("#ef4925", "#929497"))
      					    .angle(function(d) d / sum * 2 * Math.PI);
      					vis.render();
    					</script>		
            </div>		    
            <div class="amount-in"><span><?=$checkins; ?></span></div>
            <div class="amount-total"><span><?=$capacity; ?></span></div>
          </div>
        </div>
			<?php } ?>
	</div>

<div id="agora">
<?php // ************** WHO IS HERE ************    ?>
<?php
 if(count($checked_in_users) > 0){
 	echo '<h4>Checked In:</h4>';
	echo '<div id="whos_here_now" class="agora page">';
	echo '<ul class="agora">';
	foreach($here as $u){
		$fn = get_user_meta($u->ID ,'first_name', true);
		if(!$fn){ $fn = 'First Name'; }
		
		$ln = get_user_meta($u->ID, 'last_name', true);
		if(!$ln){ $ln = "Last Name"; }

		$twitter = get_user_meta($u->ID, 'twitter', true);
		if(!$twitter){ 
			$twitter = ""; 
			$twitter_at = '&nbsp;';
		} else {	
			$twitter_at = str_replace("http://www.twitter.com/", '', $twitter);
			if(strstr($twitter_at, 'http://')){
				$twitter_at = str_replace("http://twitter.com/", '', $twitter);
			}			
			if(!strstr($twitter_at, '@')){
					$twitter_at = "@".$twitter_at;
			} 	
		}	
		
		



		echo '<li href="'.get_author_posts_url($u->ID).'">';
		// query for info from the 'user' table.
		// get the avatar for this USER
		echo get_avatar( $u->ID, 120 );
		echo '<div class="user_meta">';
			echo '<span class="fn">'.$fn.'</span>';
			echo '<span class="ln">'.$ln.'</span>';
			if($twitter != ''){
				echo '<span class="twitter">'.$twitter_at.'</span>';
			}  else {
				echo '<span class="twitter">&nbsp;</span>';
			}
		echo '</div>';
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
}
?>

<hr class="pagehead" />

<?php // ************** EVERYONE ELSE THAT'S NOT HERE ************    ?>

<?php
 if(count($not) > 0){
	echo '<h4>All Grindists:</h4>';
	echo '<div id="who_isnt_here">';
	echo '<ul class="agora">';
	foreach($not as $u){
		$ud = get_userdata($u->ID);
	
		$nickname = get_user_meta($u->ID ,'nickname', true);


		$fn = get_user_meta($u->ID ,'first_name', true);
		if(!$fn){ $fn = 'First Name'; }
		
		$ln = get_user_meta($u->ID, 'last_name', true);
		if(!$ln){ $ln = "Last Name"; }

		$twitter = get_user_meta($u->ID, 'twitter', true);
		if(!$twitter){ 
			$twitter = ""; 
			$twitter_at = '&nbsp;';
		} else {	
			$twitter_at = str_replace("http://www.twitter.com/", '', $twitter);
			
			if(strstr($twitter_at, 'http://')){
				$twitter_at = str_replace("http://twitter.com/", '', $twitter);
			}			
			
			if(!strstr($twitter_at, '@')){
					$twitter_at = "@".$twitter_at;
			} 	
		}	


		echo '<li href="'.get_author_posts_url($ud->ID, $nickname).'">';
		// query for info from the 'user' table.
		// get the avatar for this USER
		echo get_avatar( $u->ID, 120 );
		echo '<div class="user_meta">';
			echo '<span class="fn">'.$fn.'</span>';
			echo '<span class="ln">'.$ln.'</span>';
			if($twitter != ''){
				echo '<span class="twitter">'.$twitter_at.'</span>';
			} else {
						echo '<span class="twitter">&nbsp;</span>';
			}

		echo '</div>';
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';
}
?>
</div>
<script type="text/javascript" src="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/protovis.min.js"></script>
<script type="text/javascript" src="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {

			if($.browser.mozilla){
				var width = 833;
			} else  if($.browser.msie ){
				var width = 840;
			} else { 
				var width = 820;
			}



		$(".agora li").fancybox({
			'width'				: width,
			'height'			: 510,
			'autoScale'			: false,
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'type'				: 'iframe'
		});
	});
</script>
<?php get_footer(); ?>

