<?

if(!isset($_SERVER["HTTPS"])&&(SITE=="PRODUCTION")) {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
   exit();
}

include_once MEMBERINCLUDEPATH."/_siteconfig.php";
$pwdBypass = false;
?>
<!doctype html>
<!--[if IE 7]><html class="ie7 oldie" lang="en"><![endif]-->
<!--[if IE 8]><html class="ie8 oldie" lang="en"><![endif]-->
<!--[if gt IE 8]><!--><html lang="en"><!--<![endif]-->
<head>
  <meta charset="utf-8">
  <title><?=$title?></title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?=ROOTMEMBERPATH?>grind-code/css/style.css">
  <link rel="stylesheet" href="//cloud.webtype.com/css/cc08e127-de83-4b8e-bbb5-9de5ebcbd740.css">
  <!--[if lt IE 9]><script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	
	<script>window.jQuery || document.write('<script src="<?=ROOTMEMBERPATH?>grind-code/js/libs/jquery-1.6.2.min.js"><\/script>')</script>
	<script src="<?=ROOTMEMBERPATH?>grind-code/js/plugins.js"></script>
	<script src="<?=ROOTMEMBERPATH?>grind-code/js/script.js"></script>
	<script src="<?=ROOTMEMBERPATH?>grind-code/js/jquery.timeago.js" type="text/javascript"></script>
	
</head>
<body>
  <div id="container">
  
    <header class="clearfix">
      <aside>
        <ul>
          <li id="greeting">
    				<?
    				if(!isset($_SESSION))
					@session_start();
    				if (!isset($_SESSION['wpuser'])) {
    				?>
    					<a href="<?=ROOTMEMBERPATH?>wp-login.php">Log in now</a>.
    				<?
    				} else {
    					echo "Hello, " . $_SESSION['wpuser']["user_login"];
						echo "&nbsp;<a href=\"/wp-login.php?action=logout\">Logout</a>";
    				}
    				?>
          </li>
			<?=form_open("/admin/index/updatenavlocation", array("id" => "locationnavform"));?>
			<input type="hidden" id="locationnav" name="locationnav" value="<?=$navlocation_current?>" />
          <li id="locations">
			<?
			foreach($navlocations as $id => $loc) {
				if ($navlocation_current == $id) {
			?>
					<a href="#" class="selected"><?=$loc?></a>
			<?
					break;
				}
			}
			?>
            <ul id="location-menu">
				<?
				foreach($navlocations as $id => $loc) {
					if ($navlocation_current != $id) {
				?>
					<li id="loc_<?=$id?>" class="location-menu-option"><a href="#"><?=$loc?></a></li>
				<?
					}
				}
				?>
            </ul>
			<?=form_close();?>

		  <script type="text/javascript">
			$(".location-menu-option").click(function() {
				var optionParts = this.id.split("_");
				$("#locationnav").val(optionParts[1]);
				//alert($("#locationnav").val());
				$("#locationnavform").submit();
				return false;
			});
		  </script>

          </li>
        </ul>
      </aside>
      <div id="masthead"><a href="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/index"><img src="<?=ROOTMEMBERPATH?>grind-code/img/grind-frontdesk.png" alt="grind FrontDesk" width="355" height="59"></a></div>
    </header>
    
    <nav id="primary">
      <ul class="clearfix">
        <li><?=g_anchor("/admin/index", "Dashboard")?></li>
        <li><?=g_anchor("/admin/usermanagement/users", "Members")?></li>
		<li><?=g_anchor("/emailtemplate", "Email Templates")?></li>
        <li><?=g_anchor("/admin/locationmanagement/locations", "Spaces")?></li>
      </ul>
    </nav>
	<? if(isset($show_member_subnav)) { ?>
		<? if($show_member_subnav == true) { ?>
			<nav id="secondary">
			  <ul class="clearfix">
				  <li class="<?=($member_subnav_current == "Members Listing" ? "selected" : "")?>"><?=g_anchor("/admin/usermanagement/users", "Members Listing")?></li>
				  <li class="<?=($member_subnav_current == "Applicants" ? "selected" : "")?>"><?=g_anchor("/admin/applicantmanagement/applicants", "Applicants")?></li>
				  <li class="<?=($member_subnav_current == "Member Registration" ? "selected" : "")?>"><?=g_anchor("/admin/usermanagement/user", "Member Registration")?></li>
				  <li class="<?=($member_subnav_current == "Member Invite" ? "selected" : "")?>"><?=g_anchor("/admin/usermanagement/invite", "Member Invite")?></li>
				  <li class="<?=($member_subnav_current == "Waitlist" ? "selected" : "")?>"><?=g_anchor("/admin/usermanagement/waitlist", "Waitlist")?></li>
				  <li class="<?=($member_subnav_current == "Remove Member" ? "selected" : "")?>"><?=g_anchor("/members/profile/delete", "Remove Member")?></li>				  
			  </ul>
			</nav>
		<? } ?>
	<? } ?>
	
		
	
    <div id="main" class="clearfix">
		    	
    </div><!-- end #main -->
    <footer>
      <p>&copy;GRIND 2011</p>
    </footer>
  </div><!-- end #container -->

  <script>
  
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-23061703-1']);
    _gaq.push(['_setDomainName', 'grindspaces.com']);
    _gaq.push(['_setAllowHash', false]);
    _gaq.push(['_trackPageview']);
  
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  
 	$(document).ready(function() {
  		$("abbr.timeago").timeago();
	});
  </script>

</body>
</html>