<?php

function ecpt_settings_page()
{
	global $ecpt_options;
	global $user_levels;
	?>
	<div class="wrap">
		<div id="ecpt-wrap" class="ecpt-help">
			<h2>Easy Custom Content Types Settings</h2>
			<?php
			if ( ! isset( $_REQUEST['updated'] ) )
				$_REQUEST['updated'] = false;
			?>
			<?php if ( false !== $_REQUEST['updated'] ) : ?>
			<div class="updated fade"><p><strong><?php _e( 'Options saved' ); ?></strong></p></div>
			<?php endif; ?>
			<form method="post" action="options.php">

				<?php settings_fields( 'ecpt_settings_group' ); ?>
				
				<?php if(!is_writable(TEMPLATEPATH)) { ?>
				<div class="error fade"><p><strong><?php _e( 'Template folder is not writable! Please change permissions on your theme\'s root folder to 755 or 777 in order to enable these template settings' ); ?></strong></p></div>
				<?php } ?>
				
				<h4>Post Type Templates</h4>
				
				<p>
					<input id="ecpt_settings[create_single_templates]" name="ecpt_settings[create_single_templates]" type="checkbox" value="1" <?php checked( '1', $ecpt_options['create_single_templates'] ); ?> <?php if(!is_writable(TEMPLATEPATH)) { ?> disabled="disabled"<?php } ?>/>
					<label class="description" for="ecpt_settings[create_single_templates]"><?php _e( 'Check this box to enable automatic single template creation for custom post types' ); ?></label>
				</p>
				<p>
					<input id="ecpt_settings[create_archive_templates]" name="ecpt_settings[create_archive_templates]" type="checkbox" value="1" <?php checked( '1', $ecpt_options['create_archive_templates'] ); ?> <?php if(!is_writable(TEMPLATEPATH)) { ?> disabled="disabled"<?php } ?>/>
					<label class="description" for="ecpt_settings[create_archive_templates]"><?php _e( 'Check this box to enable automatic template creation for custom post types archives' ); ?></label>
				</p>
				
				<h4>Taxonomy Templates</h4>
				
				<p>
					<input id="ecpt_settings[create_tax_templates]" name="ecpt_settings[create_tax_templates]" type="checkbox" value="1" <?php checked( '1', $ecpt_options['create_tax_templates'] ); ?> <?php if(!is_writable(TEMPLATEPATH)) { ?> disabled="disabled"<?php } ?>/>
					<label class="description" for="ecpt_settings[create_tax_templates]"><?php _e( 'Check this box to enable automatic template creation for custom taxonomy archives' ); ?></label>
				</p>
				
				<h4>User Levels</h4>
				<p>
                    <select name="ecpt_settings[menu_user_level]">
						<?php foreach ($user_levels as $option) { ?>
							<option <?php if ($ecpt_options['menu_user_level'] == $option ){ echo 'selected="selected"'; } ?>><?php echo htmlentities($option); ?></option>
						<?php } ?>
					</select>					
					<label class="description" for="ecpt_settings[menu_user_level]"><?php _e( 'Choose the user level that can access the custom content menu' ); ?></label>
				</p>
				<p>
                    <select name="ecpt_settings[posttype_user_level]">
						<?php foreach ($user_levels as $option) { ?>
							<option <?php if ($ecpt_options['posttype_user_level'] == $option ){ echo 'selected="selected"'; } ?>><?php echo htmlentities($option); ?></option>
						<?php } ?>
					</select>					
					<label class="description" for="ecpt_settings[posttype_user_level]"><?php _e( 'Choose the user level that can create custom post types.' ); ?></label>
				</p>
				<p>
					<select name="ecpt_settings[tax_user_level]">
						<?php foreach ($user_levels as $option) { ?>
							<option <?php if ($ecpt_options['tax_user_level'] == $option ){ echo 'selected="selected"'; } ?>><?php echo htmlentities($option); ?></option>
						<?php } ?>
					</select>					
					<label class="description" for="ecpt_settings[tax_user_level]"><?php _e( 'Choose the user level that can create custom taxonomies.' ); ?></label>
				</p>
				<p>
					<select name="ecpt_settings[metabox_user_level]">
						<?php foreach ($user_levels as $option) { ?>
							<option <?php if ($ecpt_options['metabox_user_level'] == $option ){ echo 'selected="selected"'; } ?>><?php echo htmlentities($option); ?></option>
						<?php } ?>
					</select>					
					<label class="description" for="ecpt_settings[metabox_user_level]"><?php _e( 'Choose the user level that can create custom meta boxes.' ); ?></label>
				</p>

				<h4>Auto Flush Permalinks</h4>
				
				<p>
					<input id="ecpt_settings[auto_flush_permalinks]" name="ecpt_settings[auto_flush_permalinks]" type="checkbox" value="1" <?php checked( '1', $ecpt_options['auto_flush_permalinks'] ); ?>/>
					<label class="description" for="ecpt_settings[auto_flush_permalinks]"><?php _e( 'Check this box to automatically flush permalinks. Note, this causes conflicts with some themes' ); ?></label>
				</p>
				
				<h4>Date Picker Format</h4>
				
				<p>
					<input id="ecpt_settings[date_format]" name="ecpt_settings[date_format]" type="text" value="<?php echo $ecpt_options['date_format']; ?>"/>
					<label class="description" for="ecpt_settings[date_format]"><?php _e( 'Enter the date format you would like used for the date picker, or leave blank for default. Use <a href="http://docs.jquery.com/UI/Datepicker/formatDate">this page for reference.</a>' ); ?></label>
				</p>
				
				<!-- save the options -->
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
				</p>
								
				
			</form>
		</div><!--end ecpt-wrap-->
	</div><!--end wrap-->
		
	<?php
}

// register the plugin settings
function ecpt_register_settings() {

  	global $ecpt_db_version;
  	global $ecpt_db_tax_version;
  	global $ecpt_db_name;
  	global $ecpt_db_tax_name;

	// create whitelist of options
	register_setting( 'ecpt_settings_group', 'ecpt_settings' );
}
//call register settings function
add_action( 'admin_init', 'ecpt_register_settings' );
?>