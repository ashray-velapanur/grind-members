<!doctype html>
<!--[if IE 7]><html class="ie7 oldie" lang="en"><![endif]-->
<!--[if IE 8]><html class="ie8 oldie" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title>Grind | Work Liquid | A workspace for free-range humans</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>">
  <link rel="stylesheet" href="//cloud.webtype.com/css/cc08e127-de83-4b8e-bbb5-9de5ebcbd740.css">
  <!--[if lt IE 9]><script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
  <script src="<?= get_bloginfo('stylesheet_directory'); ?>/js/libs/jquery-1.6.2.min.js"></script>

  <?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
  <div id="container">
	<header class="clearfix">
<? if(!isset($newregistration)){?>
    <div id="masthead"><a href="<?= bloginfo('url'); ?>"><img src="<?= get_bloginfo('stylesheet_directory'); ?>/img/member-logo.png" alt="Grind" width="190" height="80"></a></div>
    <aside>
      <ul>
        <li><a href="<?php echo wp_logout_url(); ?>">Logout</a></li>
        <li>Hello <?php echo $_SESSION["wpuser"]['first_name']; ?></li>
      </ul>
    </aside>
<? } else{ ?>
	<div id="masthead"><a href="http://grindspaces.com"><img src="<?= get_bloginfo('stylesheet_directory'); ?>/img/member-logo.png" alt="grind FrontDesk" width="190" height="80"></a></div>
<? }//end if?>
	</header>
	<nav id="primary">
	  <ul class="clearfix">
	  <? $navmenu = isset($newregistration) ? 'registration-menu':'header-menu'; 
	     wp_nav_menu( array( 'theme_location' => $navmenu ) );
	  ?>	  
	  </ul>
	</nav>

  <div id="main" role="main" class="section-content clearfix">