<?php
/*
  Plugin Name: SuperSaaS Login
  Plugin URI: http://www.supersaas.com/tutorials/wordpress_appointment_scheduling
  Description: This module displays a 'Book now' button that automatically logs the user into a SuperSaaS schedule using his WordPress user name. It passes the user's information along, creating or updating the user's information on SuperSaaS as needed. This saves users from having to log in twice. Works with both the free and paid versions of SuperSaaS.
  Text Domain: supersaas
  Domain Path: /
  Version: 1.7
  Author: SuperSaaS
  Author URI: http://www.supersaas.com
  License: GPL2
 */
/*  Copyright 2010  SuperSaaS  (email : info@supersaas.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/* this one is obsolete */

function supersaas_button() {
  $account = get_option('ss_account_name');
  $password = get_option('ss_password');
  $after = get_option('ss_schedule');
  if ($account && $password && $after) {
    global $current_user;
    get_currentuserinfo();
    if ($current_user->ID) {
      $image = get_option('ss_button_image');
      $label = get_option('ss_button_label');
      if (!$label)
        $label = __("Book Now!", 'supersaas');
      $domain = get_option('ss_domain');
      $user_login = $current_user->user_login;
      $account = str_replace(' ', '_', $account);
?>
      <form method="post" action="http://<?php echo $domain ? $domain : __('www.supersaas.com', 'supersaas') ?>/api/users">
        <input type="hidden" name="account" value="<?php echo $account ?>"/>
        <input type="hidden" name="id" value="<?php echo $current_user->ID ?>fk"/>
        <input type="hidden" name="user[name]" value="<?php echo htmlspecialchars($user_login) ?>"/>
        <input type="hidden" name="user[full_name]" value="<?php echo htmlspecialchars($current_user->user_firstname . ' ' . $current_user->user_lastname) ?>"/>
        <input type="hidden" name="user[email]" value="<?php echo htmlspecialchars($current_user->user_email) ?>"/>
        <input type="hidden" name="checksum" value="<?php echo md5("$account$password$user_login"); ?>"/>
        <input type="hidden" name="after" value="<?php echo htmlspecialchars(str_replace(' ', '_', $after)) ?>"/>
  <?php if ($image) {
  ?>
        <input type="image" src="<?php echo $image ?>" alt="<?php echo htmlspecialchars($label) ?>" name="submit"/>
  <?php } else {
  ?>
        <input type="submit" value="<?php echo htmlspecialchars($label) ?>"/>
  <?php } ?>
    </form>
<?php
    }
  } else
    _e('(Setup incomplete)', 'supersaas');
}

function supersaas_handler($atts) {
  extract(shortcode_atts(array('after' => get_option('ss_schedule'), 'image' => get_option('ss_button_image'), 'label' => get_option('ss_button_label')), $atts));
  $account = get_option('ss_account_name');
  $password = get_option('ss_password');
  if ($account && $password && $after) {
    global $current_user;
    get_currentuserinfo();
    if ($current_user->ID) {
      if (!$label)
        $label = __("Book Now!", 'supersaas');
      $domain = get_option('ss_domain');
      $user_login = $current_user->user_login;
      $account = str_replace(' ', '_', $account);
      $out = '<form method="post" action="http://' . ($domain ? $domain : __('www.supersaas.com', 'supersaas')) . '/api/users">';
      $out.= '<input type="hidden" name="account" value="' . $account . '"/>';
      $out.= '<input type="hidden" name="id" value="' . $current_user->ID . 'fk"/>';
      $out.= '<input type="hidden" name="user[name]" value="' . htmlspecialchars($user_login) . '"/>';
      $out.= '<input type="hidden" name="user[full_name]" value="' . htmlspecialchars($current_user->user_firstname . ' ' . $current_user->user_lastname) . '"/>';
      $out.= '<input type="hidden" name="user[email]" value="' . htmlspecialchars($current_user->user_email) . '"/>';
      $out.= '<input type="hidden" name="checksum" value="' . md5("$account$password$user_login") . '"/>';
      $out.= '<input type="hidden" name="after" value="' . htmlspecialchars(str_replace(' ', '_', $after)) . '"/>';
      if ($image)
        $out.= '<input type="image" src="' . $image . '" alt="' . htmlspecialchars($label) . '" name="submit"/>';
      else
        $out.= '<input type="submit" value="' . htmlspecialchars($label) . '"/>';
      $out.= '</form>';
    } else
      $out='';
  } else
    $out = __('(Setup incomplete)', 'supersaas');
  return $out;
}

add_shortcode('supersaas', 'supersaas_handler');

add_action('admin_menu', 'supersaas_menu');
load_plugin_textdomain('supersaas', false, dirname(plugin_basename(__FILE__)));

function supersaas_menu() {
  add_options_page(__('SuperSaaS Settings', 'supersaas'), 'SuperSaaS', 'manage_options', 'supersaas-settings', 'supersaas_options');
  add_action('admin_init', 'register_supersaas_settings');
}

function register_supersaas_settings() {
  register_setting('supersaas-settings', 'ss_account_name');
  register_setting('supersaas-settings', 'ss_password');
  register_setting('supersaas-settings', 'ss_schedule');
  register_setting('supersaas-settings', 'ss_button_label');
  register_setting('supersaas-settings', 'ss_button_image');
  register_setting('supersaas-settings', 'ss_domain');
}

function supersaas_options() {
  if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
  }
?>
  <div class="wrap">
    <h2><?php _e('SuperSaaS Settings', 'supersaas') ?></h2>

    <form method="post" action="options.php">
    <?php settings_fields('supersaas-settings'); ?>
    <table class="form-table">
      <tr valign="top">
        <th scope="row"><?php _e('SuperSaaS account name', 'supersaas') ?></th>
        <td><input type="text" name="ss_account_name" value="<?php echo get_option('ss_account_name') ?>" /><br/>
          <span class='description'><?php _e("The account name of your SuperSaaS account. If you don't have an account name yet then please create one at supersaas.com.", 'supersaas') ?></span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e('SuperSaaS password', 'supersaas') ?></th>
        <td><input type="password" name="ss_password" value="<?php echo get_option('ss_password'); ?>" /><br/>
          <span class='description'><?php _e('The password for the administrator of your SuperSaaS account.', 'supersaas') ?></span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e('Schedule name or URL', 'supersaas') ?></th>
        <td><input type="text" name="ss_schedule" value="<?php echo get_option('ss_schedule') ?>" /><br/>
          <span class='description'><?php _e('The name of the schedule or URL to redirect to after login.', 'supersaas') ?></span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e('Button Label', 'supersaas') ?></th>
        <td><input type="text" name="ss_button_label" value="<?php echo get_option('ss_button_label') ? get_option('ss_button_label') : __('Book Now!', 'supersaas'); ?>" /><br/>
          <span class='description'><?php _e("The text to be put on the button that is displayed, for example 'Create Appointment'.", 'supersaas') ?></span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e('Button Image', 'supersaas') ?> <em>(<?php _e('optional', 'supersaas') ?>)</em></th>
        <td><input type="text" name="ss_button_image" value="<?php echo get_option('ss_button_image') ?>" /><br/>
          <span class='description'><?php _e('Location of an image file to use as the button. Can be left blank.', 'supersaas') ?></span>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row"><?php _e('Custom Domain Name', 'supersaas') ?> <em>(<?php _e('optional', 'supersaas') ?>)</em></th>
        <td><input type="text" name="ss_domain" value="<?php echo get_option('ss_domain') ?>" /><br/>
          <span class='description'><?php _e('If you created a custom domain name that points to SuperSaaS enter it here. Can be left blank.', 'supersaas') ?></span>
        </td>
      </tr>

    </table>

    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

  </form>
</div>

<?php
  }
?>
