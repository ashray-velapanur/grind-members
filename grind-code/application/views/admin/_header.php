<?
if(!isset($_SERVER["HTTPS"])) {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
   exit();
}
include_once "../_siteconfig.php";
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
        </ul>
      </aside>
      <div id="masthead"><a href="<?=ROOTMEMBERPATH?>grind-code/index.php/admin/index"><img src="<?=ROOTMEMBERPATH?>grind-code/img/grind-frontdesk.png" alt="grind FrontDesk" width="355" height="59"></a></div>
    </header>
     
     <div id="main" class="clearfix">