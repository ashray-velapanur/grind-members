<?php
/*
Plugin Name: Custom Login Page
Plugin URI: http://phiredesign.net
Description: Custom Login Page
Author: Denzel Chia | Phire Design
Version: 2.0
Author URI: http://phiredesign.net
*/

//Register form settings
function register_dd_login_page_settings(){
		
$form_field_names = array(

    'dd_login_logo_link_url',
	'dd_login_logo_link_title',
	'dd_login_logo_image_url',
	'dd_login_background_color',
	'dd_login_background_image',
	'dd_login_background_position',
	'dd_login_background_repeat',
	'dd_loginform_background_color',
	'dd_loginform_background_image',
	'dd_loginform_background_position',
	'dd_loginform_background_repeat',
	'dd_login_custom_css',
	'dd_loginform_labelfont_color',
	'dd_loginform_inputborder_color',
	'dd_loginform_passwordlink_color',
	'dd_loginform_passwordlinkhover_color',
	'dd_loginform_buttonfont_color',
	'dd_loginform_buttonfonthover_color',
	'dd_loginform_buttonborder_color',
	'dd_loginform_buttonborderhover_color',
	'dd_loginform_buttonbackground_color',
	'dd_loginform_buthover_color',

);

//insert all form variables in database to be used later
update_option('dd_customlogin_form_field_array',$form_field_names);	

		foreach ($form_field_names as $setting) {
		
		$form_fields = $setting;
	

		register_setting('dd_login_page_settings', $form_fields);

		}

}
add_action('admin_init','register_dd_login_page_settings');

//Make our admin page function
function dd_customlogin_adminpage(){

//check user can manage options
if(!current_user_can('manage_options')) {
	die('Access Denied');
}

// get option from database
$stored_field_array = get_option('dd_customlogin_form_field_array');
		
		foreach ($stored_field_array as $value) {
		
		$form_input_name = $value;
        //$dd_login_logo_link_url= get_option('dd_login_logo_link_url');
        $$form_input_name = get_option($form_input_name);
		}




  //Check if the admin form has been submited
    if( $_POST['submitted_hidden'] == 'yes_change' ){

	    //check nonce
		check_admin_referer('dd_login_page_settings-options');

        $stored_field_array2 = get_option('dd_customlogin_form_field_array');
	
		//loop through form fields array to construct the following for all posted form values
		foreach ($stored_field_array2 as $value2) {
	
		$form_input_name2 = $value2;
		
		//$dd_login_logo_link_url = $_POST['dd_login_logo_link_url'];
		$$form_input_name2 = stripslashes($_POST[$form_input_name2]);

		}
		
		
		$stored_field_array3 = get_option('dd_customlogin_form_field_array');
		
		//loop through form fields array to construct the following for updating all value
		foreach ($stored_field_array3 as $value3) {

		$form_input_name3 = $value3;

		
		//update_option('dd_login_logo_link_url', $dd_login_logo_link_url);
		 update_option($form_input_name3, $$form_input_name3);

		}
     
		
		
        //echo a 'Options Edited' message
        echo "<div id=\"message\" class=\"updated fade\"><p><strong>Custom Login Page options updated.</strong></p></div>";
    }
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div>
<h2>Custom Login Page Setup<span style="float:right"><p>Please leave option value blank if not using. Click on the above Admin Help for instructions on how to use these plugin.
</p></span></h2>
<br />

<form method="post" name="options" action="" >

<?php settings_fields('dd_login_page_settings'); ?>

<h4><u>Login Page Background (1500px by 400px maximum)</u></h4>

<table class="widefat" cellspacing="0">
<thead><tr><th width="200px">Option</th><th>Value</th></tr></thead>
<tfoot></tfoot>
<tbody>
<tr>
<td>Background Image URL</td>
<td><input name="dd_login_background_image" type="text" style="width:100%;" value="<?php echo $dd_login_background_image;?>" /></td>
</tr>
<tr>
<td>Background Image Position</td>
<td><input name="dd_login_background_position" type="text" style="width:100%;" value="<?php echo $dd_login_background_position;?>" /></td>
</tr>
<tr>
<td>Background Image Repeat</td>
<td><input name="dd_login_background_repeat" type="text" style="width:100%;" value="<?php echo $dd_login_background_repeat;?>" /></td>
</tr>
</tbody>
</table>

<h4><u>Login Logo (310px by 70px)</u></h4>

<table class="widefat" cellspacing="0">
<thead><tr><th width="200px">Option</th><th>Value</th></tr></thead>
<tfoot></tfoot>
<tbody>
<tr>
<td>Login Logo Image URL</td>
<td><input name="dd_login_logo_image_url" type="text" style="width:100%;" value="<?php echo $dd_login_logo_image_url;?>" /></td>
</tr>
<tr>
<td>Login Logo Image Link</td>
<td><input name="dd_login_logo_link_url" type="text" style="width:100%;" value="<?php echo $dd_login_logo_link_url;?>" /></td>
</tr>
<tr>
<td>Login Logo Image Title</td>
<td><input name="dd_login_logo_link_title" type="text" style="width:100%;" value="<?php echo $dd_login_logo_link_title;?>" /></td>
</tr>
</tbody>
</table>

<h4><u>Login Form</u></h4>

<table class="widefat" cellspacing="0">
<thead><tr><th width="200px">Option</th><th>Value</th></tr></thead>
<tfoot></tfoot>
<tbody>
<tr>
<td>Background Color (enter "none" if using a semi-transparent png)</td>
<td><input name="dd_loginform_background_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_background_color;?>" /></td>
</tr>
<tr>
<td>Background Image URL</td>
<td><input name="dd_loginform_background_image" type="text" style="width:100%;" value="<?php echo $dd_loginform_background_image;?>" /></td>
</tr>
<tr>
<td>Background Image Position</td>
<td><input name="dd_loginform_background_position" type="text" style="width:100%;" value="<?php echo $dd_loginform_background_position;?>" /></td>
</tr>
<tr>
<td>Background Image Repeat</td>
<td><input name="dd_loginform_background_repeat" type="text" style="width:100%;" value="<?php echo $dd_loginform_background_repeat;?>" /></td>
</tr>
<td>Label Font Color</td>
<td><input name="dd_loginform_labelfont_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_labelfont_color;?>" /></td>
</tr>
<td>Text Box Border Color</td>
<td><input name="dd_loginform_inputborder_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_inputborder_color;?>" /></td>
</tr>
<td>Login Button Font Color</td>
<td><input name="dd_loginform_buttonfont_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_buttonfont_color;?>" /></td>
</tr>
<td>Login Button Font Mouseover Color</td>
<td><input name="dd_loginform_buttonfonthover_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_buttonfonthover_color;?>" /></td>
</tr>
<td>Login Button Border Color</td>
<td><input name="dd_loginform_buttonborder_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_buttonborder_color;?>" /></td>
</tr>
<td>Login Button Border Mouseover Color</td>
<td><input name="dd_loginform_buttonborderhover_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_buttonborderhover_color;?>" /></td>
</tr>
<td>Login Button Background Image URL<br/>(68px by 22px)</td>
<td><input name="dd_loginform_buttonbackground_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_buttonbackground_color;?>" /></td>
</tr>
<td>Login Button Background Mouseover Image URL<br/>(68px by 22px)</td>
<td><input name="dd_loginform_buthover_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_buthover_color;?>" /></td>
</tr>
<td>"Lost your password?"<br/>Link Font Color</td>
<td><input name="dd_loginform_passwordlink_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_passwordlink_color;?>" /></td>
</tr>
<td>"Lost your password?"<br/>Link Mouseover Font Color</td>
<td><input name="dd_loginform_passwordlinkhover_color" type="text" style="width:100%;" value="<?php echo $dd_loginform_passwordlinkhover_color;?>" /></td>
</tr>
</tbody>
</table>

<h4><u>Custom CSS</u></h4>

<table class="widefat" cellspacing="0">
<thead><tr><th width="200px">Option</th><th>Value</th></tr></thead>
<tfoot></tfoot>
<tbody>
<tr>
<td>CSS Code, do not include<br/>&lt;style type="text/css"&gt;&lt;/style&gt;</td>
<td><textarea class="widefat" rows="10" cols="20" name="dd_login_custom_css"><?php echo $dd_login_custom_css; ?></textarea></td>
</tr>
</tbody>
</table>

<p class="submit">
<input type="hidden" name="submitted_hidden" value="yes_change">
<input type="submit" name="Submit" value="Update Options" class="button-primary" />
</p>
</form>
</div>

<?php 
}

//Add the options page under the Options tab in the admin panel
function loginlogo_addpage() {
    $manage_dd_customlogin_admin = add_submenu_page('options-general.php', 'Custom Login Page', 'Custom Login Page', 10, __FILE__, 'dd_customlogin_adminpage');
	
//add help instructions 
$manage_dd_customlogin_help = "
<p><b><u>Login Page Background (1500px by 400px maximum)</u></b></p>
<ol>
<li>Create an image for the background, a maximum size of 1500px by 400px.</li>
<li>Upload to WordPress, using media uploader in WordPress Admin.</li>
<li>Copy the full url to the image and paste under Background Image URL option.</li>
<li>Enter position of image in the Background Image Position option.</li>
<div style='padding:0px 5px 5px 5px;border:1px solid #000000;'>
<p><b><u>List of Background Position Values</u></b></p>
<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\" >
  <tr>
    <th align=\"left\" valign=\"top\" width=\"20%\">Value</th>
    <th align=\"left\" valign=\"top\" width=\"70%\">Description</th>
  </tr>  
  <tr>

    <td valign=\"top\">top left<br />
      top center<br />
      top right<br />
      center left<br />
      center center<br />
      center right<br />
      bottom left<br />
      bottom center<br />
      bottom right</td>
    <td valign=\"top\">If you only specify one keyword, the second value will be
      &quot;center&quot;. Default value is: 0% 0%</td>
  </tr>
  <tr>

    <td valign=\"top\"><i>x% y%</i></td>
    <td valign=\"top\">The first value is the horizontal position and the second
      value is the vertical. The top left corner is 0% 0%.
      The right bottom corner is 100% 100%. If you only specify one
      value, the other value will be 50%.</td>
  </tr>
  <tr>
    <td valign=\"top\"><i>xpos ypos</i></td>
    <td valign=\"top\">The first value&nbsp;is the horizontal position and the second
      value is the vertical. The top left corner is 0 0. Units can be pixels
      (0px 0px) or any other CSS units. If you only specify one value, the other 
	value will be 50%. You can mix % and positions</td>

  </tr>
  <tr>
    <td valign=\"top\">inherit</td>
    <td valign=\"top\">Specifies that the setting of the background-position 
	property should be inherited 
	from the parent element</td>
  </tr>
</table>
</div>
<br/>
<li>Enter no-repeat or other values in the Background Image Repeat option.</li>
<br/>
<div style='padding:0px 5px 5px 5px;border:1px solid #000000;'>
<p><b><u>List of Background Repeat Values</u></b></p>
<table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
  <tr>
    <th align=\"left\" width=\"15%\" valign=\"top\">Value</th>

    <th align=\"left\" width=\"85%\" valign=\"top\">Description</th>
  </tr>  
  <tr>
    <td valign=\"top\">repeat</td>
    <td valign=\"top\">The background image will be repeated both vertically and
      horizontally.</td>
  </tr>
  <tr>
    <td valign=\"top\">repeat-x</td>

    <td valign=\"top\">The background image will be repeated only horizontally</td>
  </tr>
  <tr>
    <td valign=\"top\">repeat-y</td>
    <td valign=\"top\">The background image will be repeated only vertically</td>
  </tr>
  <tr>

    <td valign=\"top\">no-repeat</td>
    <td valign=\"top\">The background-image will not be repeated</td>
  </tr>
  <tr>
    <td valign=\"top\">inherit</td>
    <td valign=\"top\">Specifies that the setting of the background-repeat 
	property should be inherited 
	from the parent element</td>
  </tr>
</table>
</div>
</ol>

<p><b><u>Login Logo (310px by 70px)</u></b></p>
<ol>
<li>Create an image for the login logo, size of 310px by 70px.</li>
<li>Upload to WordPress, using media uploader in WordPress Admin.</li>
<li>Copy the full url to the image and paste under Login Logo Image URL option.</li>
<li>Enter your site url (http://example.com) in the Login Logo Image Link option.</li>
<li>Enter your site name (Example.com) in the Login Logo Image Title option.</li>
</ol>

<p><b><u>Login Form Background</u></b></p>
<ol>
<li>Enter background color (in HTML color code or text) in Background Color Option.</li>
<li>To use an image as background, leave the Background Color Option blank. Create an image for the form background.</li>
<li>Upload to WordPress, using media uploader in WordPress Admin.</li>
<li>Copy the full url to the image and paste under Background Image URL option.</li>
<li>Enter position of image in the Background Image Position option.</li>
<li>Enter no-repeat or other values in the Background Image Repeat option.</li>
<li>The other options are self-explanatory.</li>
</ol>

<p><b><u>Custom CSS</u></b></p>
<ol>
<li>You can enter css codes here, to overwrite default login page CSS.</li>
<li>For example, hiding the back to Example.com link, simply enter the following in the Custom CSS option. </li>
<code>
#backtoblog {
display:none;
}
</code>
<li>Do not include the opening and closing style HTML tags &lt;style type=\"text/css\"&gt;&lt;/style&gt;</li>
</ol>

<br/>
<br/>
";
add_contextual_help($manage_dd_customlogin_admin, $manage_dd_customlogin_help);
	
}
add_action('admin_menu', 'loginlogo_addpage');


function dd_loginLogoLink() {
$Logo_Url = get_option("dd_login_logo_link_url");
echo "$Logo_Url";
}

function dd_loginLogoLinkTitle(){
$Logo_Title = get_option("dd_login_logo_link_title");
echo "$Logo_Title";
}

function dd_loginPageCss() {
//get all values from options database
$dd_login_logo = get_option("dd_login_logo_image_url");
$dd_background_image = get_option("dd_login_background_image");
$dd_background_position = get_option("dd_login_background_position");
$dd_background_repeat = get_option("dd_login_background_repeat");
$dd_form_background_color = get_option("dd_loginform_background_color");
$dd_form_background_image = get_option("dd_loginform_background_image");
$dd_form_background_position = get_option("dd_loginform_background_position");
$dd_form_background_repeat = get_option("dd_loginform_background_repeat");
$dd_login_custom_css = get_option("dd_login_custom_css");
$dd_loginform_labelfont_color = get_option("dd_loginform_labelfont_color");
$dd_loginform_inputborder_color = get_option("dd_loginform_inputborder_color");
$dd_loginform_passwordlink_color = get_option("dd_loginform_passwordlink_color");
$dd_loginform_passwordlinkhover_color = get_option("dd_loginform_passwordlinkhover_color");

$dd_loginform_buttonfont_color = get_option("dd_loginform_buttonfont_color");
$dd_loginform_buttonfonthover_color = get_option("dd_loginform_buttonfonthover_color");
$dd_loginform_buttonborder_color = get_option("dd_loginform_buttonborder_color");
$dd_loginform_buttonborderhover_color = get_option("dd_loginform_buttonborderhover_color");
$dd_loginform_buttonbackground_color = get_option("dd_loginform_buttonbackground_color");
$dd_loginform_buthover_color = get_option("dd_loginform_buthover_color");

//check options, only echo out css if option not empty
if(empty($dd_login_logo)){$dd_log_logo = "";}else{$dd_log_logo = "h1 a {background: url(".$dd_login_logo.") no-repeat top center;width: 326px;height: 67px;text-indent: -9999px;overflow: hidden;padding-bottom: 15px;display: block;}";};
if(empty($dd_background_image)){$dd_back_image = "";}else{$dd_back_image = "background-image:url(".$dd_background_image.");";};
if(empty($dd_background_position)){$dd_back_position = "";}else{$dd_back_position = "background-position:".$dd_background_position.";";};
if(empty($dd_background_repeat)){$dd_back_repeat = "";}else{$dd_back_repeat = "background-repeat:".$dd_background_repeat.";";};
if(empty($dd_form_background_color)){$dd_form_back_color = "";}else{$dd_form_back_color = "background:".$dd_form_background_color.";";};
if(empty($dd_form_background_image)){$dd_form_back_image = "";}else{$dd_form_back_image = "background-image:url(".$dd_form_background_image.");";};
if(empty($dd_form_background_position)){$dd_form_back_position = "";}else{$dd_form_back_position = "background-position:".$dd_form_background_position.";";};
if(empty($dd_form_background_repeat)){$dd_form_back_repeat = "";}else{$dd_form_back_repeat = "background-repeat:".$dd_form_background_repeat.";";};
if(empty($dd_loginform_labelfont_color)){$dd_form_label_color = "";}else{$dd_form_label_color ="label {color:".$dd_loginform_labelfont_color.";}";};
if(empty($dd_loginform_inputborder_color)){$dd_form_inputborder_color = "";}else{$dd_form_inputborder_color = "#login form input {	
border:1px solid ".$dd_loginform_inputborder_color.";}";};
if(empty($dd_loginform_passwordlink_color)){$dd_form_passwordlink_color = "";}else{$dd_form_passwordlink_color =".login #nav a {color:".$dd_loginform_passwordlink_color." !important;}";};
if(empty($dd_loginform_passwordlinkhover_color)){$dd_form_passwordlinkhover_color = "";}else{$dd_form_passwordlinkhover_color =".login #nav a:hover {color:".$dd_loginform_passwordlinkhover_color." !important;}";};
if(empty($dd_loginform_buttonfont_color)){$dd_form_buttonfont_color = "";}else{$dd_form_buttonfont_color = "color: ".$dd_loginform_buttonfont_color." !important;";};
if(empty($dd_loginform_buttonfonthover_color)){$dd_form_buttonfonthover_color = "";}else{$dd_form_buttonfonthover_color = "color: ".$dd_loginform_buttonfonthover_color." !important;";};
if(empty($dd_loginform_buttonborder_color)){$dd_form_buttonborder_color = "";}else{$dd_form_buttonborder_color = "border-color: ".$dd_loginform_buttonborder_color." !important;";};
if(empty($dd_loginform_buttonborderhover_color)){$dd_form_buttonborderhover_color = "";}else{$dd_form_buttonborderhover_color = "border-color: ".$dd_loginform_buttonborderhover_color." !important;";};
if(empty($dd_loginform_buttonbackground_color)){$dd_form_buttonbackground_color = "";}else{$dd_form_buttonbackground_color = "background: #21759B url(".$dd_loginform_buttonbackground_color.") repeat-x scroll left top;";};

if(empty($dd_loginform_buthover_color)){$dd_form_buthover_color = "";}else{$dd_form_buthover_color = "background: #21759B url(".$dd_loginform_buthover_color.") repeat-x scroll left top;";};

echo <<<END
<style type="text/css">

body{
$dd_back_image
$dd_back_position
$dd_back_repeat
}

$dd_log_logo

form {
margin-left: 8px;
padding: 16px 16px 40px 16px;
font-weight: normal;
-moz-border-radius: 11px;
-khtml-border-radius: 11px;
-webkit-border-radius: 11px;
border-radius: 5px;
$dd_form_back_color
$dd_form_back_image
$dd_form_back_position
$dd_form_back_repeat
border:1px solid #e5e5e5;
-moz-box-shadow: rgba(200,200,200,1) 0 4px 18px;
-webkit-box-shadow: rgba(200,200,200,1) 0 4px 18px;
-khtml-box-shadow: rgba(200,200,200,1) 0 4px 18px;
box-shadow: rgba(200,200,200,1) 0 4px 18px;
}

.submit .button-primary,
#login form .submit input {
	$dd_form_buttonborder_color
	font-weight: bold;
	$dd_form_buttonfont_color
	$dd_form_buttonbackground_color
}
.submit .button-primary,
#login form .submit input:hover {
	$dd_form_buttonborderhover_color
	font-weight: bold;
	$dd_form_buttonfonthover_color
	$dd_form_buthover_color
}

$dd_form_label_color

$dd_form_inputborder_color

$dd_form_passwordlink_color

$dd_form_passwordlinkhover_color

$dd_login_custom_css

</style>

END;
}

add_filter('login_headerurl','dd_loginLogoLink');
add_filter('login_headertitle','dd_loginLogoLinkTitle');
add_action('login_head', 'dd_loginPageCss');

?>
