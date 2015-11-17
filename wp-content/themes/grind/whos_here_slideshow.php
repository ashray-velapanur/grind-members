<?php
/*
	@name:  "who's here".  
	@descrip: this bit creates the slideshow that appears on the homepage.   it contains the list of users who are currently logged in to the system.
	@author:  Matthew Kosoy <matt.kosoy@gmail.com>
*/ 
?>

<?php 
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

if (count($checked_in_users) > 0){

if(is_numeric($to_include[0])){
	$here 	=	get_users($here_args);
	
	
	// the fields we're using.
	$fields = array(
		'first_name'	=>	'Jerry',
		'last_name'		=>	'Grinder',
		'email'			=>	'',
		'company_name'	=>	'',
		'company_desc'	=>	'',
		'phone'			=>	'(215) 123.4567',
		'URL'			=>	'grindspaces.com',
		'twitter'		=>  '',
		'behance'		=>  '',
		'foursquare'	=>	'',
		'linkedin'		=>	'',
		'facebook'		=>	'',
		'dribble'		=>	'',
		'i_need'		=>	'One walrus.'
	);


	
 if(count($here) > 0){
		echo '<div id="whos_here_now" class="agora homepage">';
		echo '<ul class="agora">';


		foreach($here as $u){
				$fn = get_user_meta($u->ID ,'first_name', true);
				if(!$fn){ $fn = $fields['first_name']; }
				
				$ln = get_user_meta($u->ID, 'last_name', true);
				if(!$ln){ $ln = $fields['last_name']; }
		
				$twitter = get_user_meta($u->ID, 'twitter', true);
				if(!$twitter){ 
					$twitter = ""; 
					$twitter_at = '&nbsp;';
				}	else {	
						$twitter_at = str_replace("http://www.twitter.com/", '', $twitter);
						if(strstr($twitter_at, 'http://')){
							$twitter_at = str_replace("http://twitter.com/", '', $twitter);
						}			
						if(!strstr($twitter_at, '@')){
							$twitter_at = "@".$twitter_at;
						} 	
				}	
		
		
		
				echo '<li href="'.get_author_posts_url($u->ID).'" class="user_'.$u->ID.'">';
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
		echo '<div class="visit_agora"><a href="http://agora.grindspaces.com" target="_blank">Visit Agora</a></div>';
		echo '<div class="prev-agora"></div>';
		echo '<div class="next-agora"></div>';
		
	} else {
		echo "nobody's home. ";
	}
	}
	// endif get_users_online_count
	?>
	<script src="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	<script src="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	<link rel="stylesheet" type="text/css" href="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
	
	<script type="text/javascript">
	
	
		$(window).load(function(){
		//$(document).ready(function() {

			$("#whos_here_now.agora.homepage").each(function(){
					$(this).animate({'opacity':1},1000, function(){/* */});
			}).jCarouselLite({
			  btnPrev: $(".prev-agora"),
			  btnNext: $(".next-agora"),
			  speed: 1600,
			  circular: true,
			  visible:6,
			  scroll: 3
			});

			if($.browser.mozilla){
				var width = 840;
				$('.author header h1').css('padding-bottom', '16px');
			} else  if($.browser.msie ){
				var width = 837;
			} else { 
				var width = 820;
			}

			/* pop up jawn for the profiles */
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
<? } ?>	
