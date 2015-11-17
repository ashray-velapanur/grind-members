<?
/**
 * Simple template for misc functions outside admin but retains its core functions
 * should merge with admin in the future
 * 
 *
 * @joshcampbell
 * @view template
 */
 
?>

<?
if(!isset($_SERVER["HTTPS"])&&(SITE=="PRODUCTION")) {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
   exit();
}
include_once "../_siteconfig.php";
$pwdBypass = false;

echo $header;
?>

		
		
		<?
		if (!$pwdBypass && !isset($_SESSION['wpuser'])) {
		?>
			Only administrators can access this page after logging in.<br />
			<a href="<?=ROOTMEMBERPATH?>wp-login.php?redirect_to=<?php echo urlencode(ROOTMEMBERPATH.'ci/'); ?>">Log in now</a>.
		<?		
		} elseif (!$pwdBypass && $_SESSION['wpuser']['wp_role'] != WP_ADMIN_ROLE) {
		?>
			Only administrators can access this page.

			You are
			<?=(in_array(substr($_SESSION['wpuser']['wp_role'], 0, 1), array('a', 'e', 'i', 'o', 'u')) ? "an" : "a") . " \"" . $_SESSION['wpuser']['wp_role'] . "\"" ?>.<br />

			<a href="<?=ROOTMEMBERPATH?>wp-login.php/redirect_to=/frontdesk/">Log in as an administrator now</a>.
		<?
		} else {
			if (isset($error)){
			display_msg($error);
			}
        	echo $content;
		}


echo $footer;

 
