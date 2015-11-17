<?php
/**
 * @Template Name: Authors 
 * @package: WordPress
 * @subpackage: members.grindspaces.com
 * @author: @mattkosoy
 * @descrip: This is the file that is loaded into the pop up window on the "Agora" sections of the site.
 */

flush_rewrite_rules( true );

// the fields we're using.
$fields = array(
	'first_name'	=>	'Jerry',
	'last_name'		=>	'Grinder',
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
	'dribbble'		=>	'',
	'i_need'		=>	''
);
$url_link = false;
$twitter = false;

?>
   <?php
   
   global $wp_query;
   #print_r($wp_query);
   
   // $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
	$curauth = get_user_by('id', $wp_query->query_vars['author']);
	
	// override the default w/ the data saved in WP
	foreach($fields as $key=>$val){
		$tmp = get_user_meta($curauth->ID, $key, true);
				
		if($tmp != '' && $tmp != 'undefined'){
			// show wp usermeta data
			if($key == 'twitter'){
				$twitter_at = $tmp;
				$twitter_at = str_replace("http://www.twitter.com/", '', $tmp);
				$twitter_at = str_replace("http://twitter.com/", '', $tmp);
				$twitter_at = substr($twitter_at, 0, 20);
				if(!strstr($twitter_at, '@')){
					$twitter_at = "@".$twitter_at;
				} 	
				$twitter = $tmp;
				if(strstr($twitter, '@')){
					$twitter = str_replace('@', '', $twitter);
				}				

				if(!strstr($twitter, 'http')){
					$twitter = 'http://www.twitter.com/'.$twitter;
				}				
			}
			
			if($key == 'URL'){
				if(!strstr($tmp, 'http://')){
					$url_link = "http://".$tmp;
				} else {
					$url_link = $tmp;
				}								
			}
			$fields[$key] = $tmp;
		
		}
	}

?>

<!doctype html>
<!--[if IE 7]><html class="ie7 oldie" lang="en"><![endif]-->
<!--[if IE 8]><html class="ie8 oldie" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
<meta charset="utf-8">
<title>Grind | Work Liquid | A workspace for free-range humans</title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="http://members.grindspaces.com/wp-content/themes/grind/style.css">
<link rel="stylesheet" href="//cloud.webtype.com/css/cc08e127-de83-4b8e-bbb5-9de5ebcbd740.css">
<!--[if lt IE 9]><script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script src="http://members.grindspaces.com/wp-content/themes/grind/js/libs/jquery-1.6.2.min.js"></script>
<script src="http://members.grindspaces.com/wp-content/themes/grind/js/plugins.js"></script>
<script src="http://members.grindspaces.com/wp-content/themes/grind/js/script.js"></script>

<meta name='robots' content='noindex,nofollow' />
<script type='text/javascript' src='http://members.grindspaces.com/wp-includes/js/l10n.js?ver=20101110'></script>
<link rel="EditURI" type="application/rsd+xml" title="RSD" href="http://members.grindspaces.com/xmlrpc.php?rsd" />
<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="http://members.grindspaces.com/wp-includes/wlwmanifest.xml" /> 
<link rel='index' title='Grind' href='http://members.grindspaces.com' />
<meta name="generator" content="WordPress 3.2.1" />
<style type="text/css">
	html, body{ margin:0; padding:0; }
	body{ 
		 background:#efefec;
	}
</style>
</head>
<body>
<div id="profile_container" class="author">

<header>
    <h1><?php echo $fields['first_name'] ?> <?= $fields['last_name']; ?> <? if($twitter){ ?> / <a href="<?= $twitter; ?>" target="_blank"><?=$twitter_at;?></a><? } ?></h1>
</header>

<section id="profile_info">
<!-- avatar -->
<div class="myAvatar col_1 left">
<?= get_avatar( $curauth->ID, 120 ); ?>
</div>
<!-- contact information -->
<div class="myProfile left">
	<ul>
		<? if($fields['company_name']){ ?><li><?= $fields['company_name']; ?></li><? } ?>
		<? if($fields['email']){ ?><li><?= $fields['email']; ?></li><? } ?>
		<? if($fields['phone']){ ?><li><?= $fields['phone']; ?></li><? } ?>
		<? if($url_link){ ?><li><a href="<?= $url_link; ?>" target="_blank"><?= $fields['URL']; ?></a></li> <? } ?>
	</ul>
	<? if($fields['company_desc']){ ?><?= apply_filters('the_content', substr($fields['company_desc'], 0, 282)); ?><? } ?>
</div>


<div class="left clear socials">
<ul class="socials">
	<? if($fields['twitter']){ ?><li class="social twitter"><a href="<?= $fields['twitter']; ?>" target="_blank">Twitter<span class="icon">&nbsp;</span></a></li><? } ?>
	<? if($fields['behance']){ ?><li class="social behance"><a href="<?= $fields['behance']; ?>" target="_blank">Behance<span class="icon">&nbsp;</span></a></li><? } ?>
	<? if($fields['foursquare']){ ?><li class="social foursquare"><a href="<?= $fields['foursquare']; ?>" target="_blank">FourSquare<span class="icon">&nbsp;</span></a></li><? } ?>
	<? if($fields['linkedin']){ ?><li class="social linkedin"><a href="<?= $fields['linkedin']; ?>" target="_blank">LinkedIn<span class="icon">&nbsp;</span></a></li><? } ?>
	<? if($fields['facebook']){ ?><li class="social facebook"><a href="<?= $fields['facebook']; ?>" target="_blank">Facebook<span class="icon">&nbsp;</span></a></li><? } ?>
	<? if($fields['dribbble']){ ?><li class="social dribbble"><a href="<?= $fields['dribbble']; ?>" target="_blank">Dribbble<span class="icon">&nbsp;</span></a></li><? } ?>
</ul>

</div>


</section>

<section id="what_i_need">

<h6>I know...</h6>
<ul class="skillset">
<?php
$skills = array();
for($i=1; $i<7; $i++){
	$skills[$i] = get_user_meta($curauth->ID, 'skill_'.$i, true);
	if($skills[$i] != ''){
		if(($i == 1 && $skills[$i] == 'I do this...') || ($i == 6 && $skills[$i] == 'And also this...') || $skills[$i] == 'And This...'){ } else {			
			echo '<li class="skillset col_1"><span class="icon">&nbsp;</span>'.$skills[$i].'</li>';	
		}
	}
}
?>
</ul>
<? if($fields['i_need'] != ''){ ?>
<div class="left clear">
<em>I need...</em><?= apply_filters('the_content',substr($fields['i_need'], 0, 135)); ?>
<? } ?>
</div>
</section>

</div>


</body>
</html>