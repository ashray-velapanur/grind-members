<?php
function ecpt_posttype_manager() {

	global $wpdb;
	global $ecpt_db_name;
	global $ecpt_base_dir;
	?>
	<div class="wrap">
	<div id="ecpt-wrap">
	<?php if($_GET['post-type-added']) : ?>
		<div class="updated fade">
			<p>Post type added. <!--You should update your <a href="options-permalink.php">permalinks</a> now--></p>
		</div>
	<?php endif; ?>	
	
	<?php if($_GET['posttype-edit']) : ?>
	<h2>
		Edit Post Types - 
		<a href="admin.php?page=easy-content-types/easy-content-types.php?posttypes" title="Go Back">Go Back</a>
	</h2>
	<?php else : ?>
	<h2>Easy Custom Post Types</h2>
	<?php endif; ?>
		
		<table class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th>Name</th>
					<th>Labels</th>
					<th>Slug</th>
					<?php if($_GET['posttype-edit']) : ?>
					<th>Position</th>
					<?php endif; ?>
					<th>Attributes</th>
					<?php if($_GET['posttype-edit']) : ?>
					<th style="width: 350px!important;">Menu Icon</th>
					<?php else : ?>
					<th>Menu Icon</th>
					<?php endif; if(!$_GET['posttype-edit']) : ?>
					<th>Template File</th>
					<?php endif; if($_GET['posttype-edit']) : ?>
					<th>Update</th>
					<?php else : ?>
					<th>Edit</th>
					<?php endif; ?>
				</tr>	
			</thead>
			<tfoot>
				<tr>
					<th>Name</th>
					<th>Labels</th>
					<th>Slug</th>
					<?php if($_GET['posttype-edit']) : ?>
					<th>Position</th>
					<?php endif; ?>
					<th>Attributes</th>
					<th>Menu Icon</th>
					<?php if(!$_GET['posttype-edit']) : ?>
					<th>Template File</th>
					<?php endif; if($_GET['posttype-edit']) : ?>
					<th>Update</th>
					<?php else : ?>
					<th>Edit</th>
					<?php endif; ?>
				</tr>	
			</tfoot>
			<tbody>
				<?php
				$i = 0;
				
				// editing a posttype
				if($_GET['posttype-edit']) : 
				
					foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_name . " WHERE id='" . $_GET['posttype-edit'] . "';") as $key => $posttype)
					{			
						?>			
							<tr <?php if(ecpt_is_odd($i)) { echo 'class="alternate"'; } ?>>
								<td>
									<span><strong>Name</strong></span><br/>
									<input type="text" name="posttype-name" class="ecpt-text no-float" id="posttype-name" value="<?php echo $posttype->name; ?>" /><br/>
									<p>This is the name that will be used to query the post type from the database. Keep it a single word and simple.</p>
								</td>
								<td>
									<span><strong>Single</strong></span><br/>
									<input type="text" name="posttype-singlular" class="ecpt-text no-float" id="posttype-singlular" value="<?php echo $posttype->singular_name; ?>" /><br/>
									
									<p>The single label is used to refer to single post type items, such as "Add New Book".</p>
									
									<span><strong>Plural</strong></span><br/>
									<input type="text" name="posttype-plural" class="ecpt-text no-float" id="posttype-plural" value="<?php echo $posttype->plural_name; ?>" />
									<p>The plural label is used to refer to plural post type items, such as "Search Books".</p>
									
								</td>
								<td>
									<span><strong>Slug</strong></span><br/>
									<input type="text" name="posttype-slug" class="ecpt-text no-float" id="posttype-slug" value="<?php echo $posttype->slug; ?>" />
								</td>
								<td>
									<span><strong>Menu Position</strong></span><br/>
									<input type="text" name="posttype-position" class="ecpt-text no-float" id="posttype-position" value="<?php echo $posttype->menu_position; ?>" />
								</td>
								<td>
									<div class="ecpt-atts">
									<?php 

										if($posttype->has_archive == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->has_archive . '">Archives</label>';
										echo '<input type="checkbox" name="' . $posttype->has_archive . '" id="posttype-has_archive"' . $checked . '/><br/></div>';									
										$checked = '';
										
										
										if($posttype->title == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->title . '">Title</label>';
										echo '<input type="checkbox" name="' . $posttype->title . '" id="posttype-title"' . $checked . '/><br/></div>';									
										$checked = '';
										
										if($posttype->editor == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->editor . '">Editor</label>';
										echo '<input type="checkbox" name="' . $posttype->editor . '" id="posttype-editor"' . $checked . '/><br/></div>';
										$checked = '';
										
										if($posttype->author == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->author . '">Author</label>';
										echo '<input type="checkbox" name="' . $posttype->author . '" id="posttype-author"' . $checked . '/><br/></div>';
										$checked = '';
										
										if($posttype->thumbnail == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->thumbnail . '">Thumbnail</label>';
										echo '<input type="checkbox" name="' . $posttype->thumbnail . '" id="posttype-thumbnail"' . $checked . '/><br/></div>';
										$checked = '';
										
										if($posttype->excerpt == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->excerpt . '">Excerpt</label>';
										echo '<input type="checkbox" name="' . $posttype->excerpt . '" id="posttype-excerpt"' . $checked . '/><br/></div>';
										$checked = '';										
										
									?>
									</div>
									<div class="ecpt-atts">
									<?php
										
										if($posttype->fields == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->fields . '">Custom Fields</label>';
										echo '<input type="checkbox" name="' . $posttype->fields . '" id="posttype-fields"' . $checked . '/><br/>';
										$checked = '';
										
										if($posttype->comments == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->comments . '">Comments</label>';
										echo '<input type="checkbox" name="' . $posttype->comments . '" id="posttype-comments"' . $checked . '/><br/></div>';
										$checked = '';
										
										if($posttype->revisions == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->revisions . '">Revisions</label>';
										echo '<input type="checkbox" name="' . $posttype->revisions . '" id="posttype-revisions"' . $checked . '/><br/></div>';
										$checked = '';
										
										if($posttype->hierarchical == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->hierarchical . '">Hierarchical</label>';
										echo '<input type="checkbox" name="' . $posttype->hierarchical . '" id="posttype-hierarchical"' . $checked . '/><br/></div>';
										$checked = '';
										
										if($posttype->post_formats == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->post_formats . '">Post Formats</label>';
										echo '<input type="checkbox" name="' . $posttype->post_formats . '" id="posttype-post_formats"' . $checked . '/><br/></div>';
										$checked = '';
										
										if($posttype->exclude_from_search == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->exclude_from_search . '">Exclude From Search</label>';
										echo '<input type="checkbox" name="' . $posttype->exclude_from_search . '" id="posttype-exclude_from_search"' . $checked . '/><br/></div>';									
										$checked = '';
										
										if($posttype->show_in_nav_menus == 1) { $checked = 'checked="checked"'; }
										echo '<div><label for="' . $posttype->show_in_nav_menus . '">Show in Nav Menus</label>';
										echo '<input type="checkbox" name="' . $posttype->show_in_nav_menus . '" id="posttype-show_in_nav_menus"' . $checked . '/><br/></div>';									
										$checked = '';										
									?>
									</div>
								</td>
								<td>
									<?php if ($posttype->menu_icon != 'undefined' && $posttype->menu_icon != '') { ?>
									<img src="<?php echo $posttype->menu_icon; ?>" class="ecpt_menu_icon"/>
									<?php } else { ?>
									<img src="<?php echo $ecpt_base_dir; ?>/includes/images/icon.png" class="ecpt_menu_icon" />
									<?php } ?>
									<input type="text" name="posttype-menu-icon" class="ecpt_upload_image posttype-menu-icon" id="upload_image_<?php echo $posttype->id; ?>" class="posttype-menu-icon" value="<?php if ($posttype->menu_icon != 'undefined' && $posttype->menu_icon != '') { echo $posttype->menu_icon; } ?>" />
									<input id="upload_image_button_<?php echo $posttype->id; ?>" class="upload_image_button edit_posttype_upload button-primary" value="Choose Image" type="button" />
								</td>
								<td>
									<input type="submit" name="posttype-update" id="<?php echo $_GET['posttype-edit'];?>" class="button-primary posttype-update" value="Update"/>
									<img src="<?php echo $ecpt_base_dir; ?>includes/images/ajax-loader.gif" id="ajax-loader" style="display: none;"/>
								</td>
							</tr>
						<?php
						$i++;
					}
				// displaying list of post types	
				else :
				
					foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_name . ";") as $key => $type)
					{			
						?>			
							<tr <?php if(ecpt_is_odd($i)) { echo 'class="alternate"'; } ?> id="ecpt-posttype-<?php echo $type->id; ?>">
								<td><?php echo $type->name; ?></td>
								<td><?php echo 'Single: ' . $type->singular_name . '<br/>Plural: ' . $type->plural_name; ?></td>
								<td><?php echo $type->slug; ?></td>
								<td>
									<div id="posttype-atts">
									<?php 
										$atts = array();
										
										// disabled at this time due to a bug in WP core
										if($type->hierarchical == 1)	{ $atts[] = 'hierarchical'; }
										if($type->post_formats == 1)	{ $atts[] = 'post-formats'; }
										if($type->page_attributes == 1)	{ $atts[] = 'page_attributes'; }
										if($type->has_archive == 1) 	{ $atts[] = 'has_archive'; }
										if($type->title == 1) 			{ $atts[] = 'title'; }
										if($type->editor == 1) 			{ $atts[] = 'editor'; }
										if($type->author == 1) 			{ $atts[] = 'author'; }
										if($type->thumbnail == 1) 		{ $atts[] = 'thumbnail'; }
										if($type->excerpt == 1)			{ $atts[] = 'excerpt'; }
										if($type->fields == 1) 			{ $atts[] = 'custom fields'; }
										if($type->comments == 1) 		{ $atts[] = 'comments'; }
										if($type->revisions == 1) 		{ $atts[] = 'revisions'; }
										foreach($atts as $att) { echo '<span class="att">' . $att . '<em>,</em> </span>'; }
									?>
									</div>
								</td>
								<td>
									<?php if ($type->menu_icon != 'undefined' && $type->menu_icon != '') { ?>
									<img src="<?php echo $type->menu_icon; ?>" class="ecpt_menu_icon"/>
									<?php } else { ?>
									<img src="<?php echo $ecpt_base_dir; ?>/includes/images/icon.png" class="ecpt_menu_icon"/>
									<?php } ?>
								</td>
								<td>
									<?php if($type->has_archive == 1) { ?>
									Archives: <em>archive-<?php echo $type->name;?>.php</em><br/>
									<?php } ?>
									Single: <em>single-<?php echo $type->name; ?>.php</em>
								</td>
								<td>
									<a href="admin.php?page=easy-content-types/easy-content-types.php?posttypes&posttype-edit=<?php echo $type->id; ?>" title="Edit" class="ecpt-edit" id="ecpt-edit-<?php echo $type->id; ?>">Edit</a> |
									<a href="edit.php?post_type=<?php echo $type->name; ?>" title="Edit" class="ecpt-edit" id="ecpt-edit-<?php echo $type->id; ?>">View <?php echo $type->plural_name; ?></a> |
									<a href="post-new.php?post_type=<?php echo $type->name; ?>" title="Edit" class="ecpt-edit" id="ecpt-edit-<?php echo $type->id; ?>">Add New <?php echo $type->singular_name; ?></a> |
									<a href="admin.php?page=easy-content-types/easy-content-types.php?posttypes" title="Delete" class="ecpt-delete" id="ecpt-delete-<?php echo $type->id; ?>">Delete</a>
									<img src="<?php echo $ecpt_base_dir; ?>includes/images/ajax-loader.gif" class="ajax-loader" style="display: none;"/>
								</td>
							</tr>
						<?php
						$i++;
					}
				endif;
				?>	
			</tbody>
		</table>
		<?php if(!$_GET['posttype-edit']) : ?>
			<!--custom post type creation form-->
			<h3>Create New Custom Post Type</h3>
			<form method="post" action="" id="ecpt-settings">
				<fieldset>
					<legend>Post Type General</legend><br/>
					
					<label for="post-type-name">Post Type Name<span class="required">*</span></label>
					<input type="text" name="post-type-name" id="ecpt-post-type-name" class="ecpt-text"  tabindex="1"/>
					<p class="ecpt-description">This is the name that you will use to query the custom post type. <strong>Note:</strong> names should be no longer than 10 letters<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This is the name referenced by the database, if you don't know what that means, don't worry about it, you don't need it.</span></a></p><br/>
										
				</fieldset><br/>
				<fieldset>
					<legend>Labels</legend><br/>
					
					<label for="label-single">Singular Label</label>
					<input type="text" name="label-single" class="ecpt-text"  tabindex="2"/>
					<p class="ecpt-description">The label used for single post type items, such as "Book"<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">If you leave this blank, the name field above will be used</span></a></p><br/>

					<label for="label-plural">Plural Label</label>
					<input type="text" name="label-plural" class="ecpt-text"  tabindex="3"/>
					<p class="ecpt-description">The label used for plural post type items, such as "Books"<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">If you leave this blank, the name field above will be used</span></a></p><br/>
					
				</fieldset><br/>
				
				<fieldset>
					<legend>Post Type Options</legend><br/>
					
					<label for="options-hierarchial">Hierarchical?</label>
					<input type="checkbox" name="options-hierarchial" class="ecpt-checkbox" tabindex="4"/>
					<p class="ecpt-description">Enabling this means that items can have parents and child items<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">Hierarchical post types work the same way that the regular Pages work.</span></a></p><br/>
					
					<label for="options-archives">Enable Archives?</label>
					<input type="checkbox" name="options-archives" class="ecpt-checkbox" checked="checked" tabindex="5"/>
					<p class="ecpt-description">This will enable archives, such as monthly and yearly, for this post type<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">Enabling this option will create archives for your post type, so that you can display a list of all items filed under a particular month or taxonomy</span></a></p><br/>				
													
					<label for="options-post_formats">Post Formats?</label>
					<input type="checkbox" name="options-post_formats" class="ecpt-checkbox" checked="checked" tabindex="6"/>
					<p class="ecpt-description">This will enable post formats, gallery, aside, default<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will allow the new 3.1 feature for post formats to be used for this post type</span></a></p><br/>				
										
					<label for="options-archives">Exclude from Search?</label>
					<input type="checkbox" name="options-search" class="ecpt-checkbox" checked="checked" tabindex="7"/>
					<p class="ecpt-description">Checking this option will prevent this post type from showing up in search results<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">If you don't want this post type to be searchable, enable this option</span></a></p><br/>				
					
					<label for="options-nav">Show in Nav Menus?</label>
					<input type="checkbox" name="options-nav" class="ecpt-checkbox" checked="checked" tabindex="8"/>
					<p class="ecpt-description">Checking this will cause this post type to show up in the custom nav menu interface<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will allow you to add items from this post type to custom navigation menus</span></a></p><br/>
					
					<label for="options-icon">Menu Icon</label>
					<input type="text" name="options-icon" id="upload_image_1" class="posttype-menu-icon ecpt-text" tabindex="9" />
					<input id="upload_image_button_1" class="upload_image_button button-primary" value="Choose Image" type="button" />
					<p class="ecpt-description ecpt-upload-desc">Enter the URL to your menu icon, or click Choose Image to upload an icon. Optimal size: 16x16 px<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This is the icon that appears to the left of the post type name in the left navigation menu</span></a></p><br/>
					
					
				</fieldset><br/>
				
				<fieldset>
					<legend>Post Type Supports</legend><br/>

					<label for="options-title">Title</label>
					<input type="checkbox" name="options-title" class="ecpt-checkbox" checked="checked" tabindex="10"/>
					<p class="ecpt-description">Enable titles for this post type?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will enabled the title field for the post type</span></a></p><br/>				
					
					<label for="options-editor">Editor</label>
					<input type="checkbox" name="options-editor" class="ecpt-checkbox" checked="checked" tabindex="11"/>
					<p class="ecpt-description">Enable the main content editor for this post type?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will enable the main content editor, including upload media and formating buttons</span></a></p><br/>

					<label for="options-author">Author</label>
					<input type="checkbox" name="options-author" class="ecpt-checkbox" checked="checked" tabindex="12"/>
					<p class="ecpt-description">Enable author selection for this post type?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will enable the drop down author selection</span></a></p><br/>
					
					<label for="options-thumbnail">Thumbnail</label>
					<input type="checkbox" name="options-thumbnail" class="ecpt-checkbox" checked="checked" tabindex="13"/>
					<p class="ecpt-description">Enable the featured post image for this post type?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will enable the featured post thumbnail option</span></a></p><br/>
					
					<label for="options-excerpt">Excerpt</label>
					<input type="checkbox" name="options-excerpt" class="ecpt-checkbox" checked="checked" tabindex="14"/>
					<p class="ecpt-description">Enable the custom crafted excerpt for this post type?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will enable the hand crafted excerpt box that can be used in place of the auto-generated excerpt</span></a></p><br/>

					<label for="options-custom-fields">Custom Fields</label>
					<input type="checkbox" name="options-custom-fields" class="ecpt-checkbox" checked="checked" tabindex="15"/>
					<p class="ecpt-description">Enable custom fields for this post type?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will enable the custom fields for this post type</span></a></p><br/>

					<label for="options-comments">Comments</label>
					<input type="checkbox" name="options-comments" class="ecpt-checkbox" checked="checked" tabindex="16"/>
					<p class="ecpt-description">Enable comments for this post type?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will enable the option to turn on or off comments for this post type</span></a></p><br/>

					<label for="options-revisions">Revisions</label>
					<input type="checkbox" name="options-revisions" class="ecpt-checkbox" checked="checked" tabindex="17"/>
					<p class="ecpt-description">Enable revisions for this post type?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will enable automatic revision control, allowing you to revert back to previous item versions</span></a></p><br/>
					
				</fieldset><br/>

				<fieldset>
					<legend>Advanced</legend><br/>
					
					<label for="advanced-position">Menu Position</label>
					<input type="text" name="advanced-position" class="ecpt-text" tabindex="18" />
					<p class="ecpt-description">
						Enter the menu position for the post type. Click the help icon for a list of options
						<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">
						5 - below Posts<br/>
						10 - below Media<br/>
						15 - below Links<br/>
						20 - below Pages<br/>
						25 - below Comments<br/>
						60 - below first separator<br/>
						65 - below Plugins<br/>
						70 - below Users<br/>
						75 - below Tools<br/>
						80 - below Settings<br/>
						100 - below second separator<br/>
						<br/></span></a>
					</p><br/>
					
					<label for="advanced-slug">Post Type Slug</label>
					<input type="text" name="advanced-slug" class="ecpt-text" tabindex="19" />
					<p class="ecpt-description">
						Enter the URL friendly slug to use for your post type. Only use this if you know what you're doing
						<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">The slug is the URL friendly name of your post type. It will be displayed in the URL when viewing a post type entry or archive. Only advanced users should enter anything here<br/></span></a>
					</p><br/>
										
				</fieldset><br/>
				
				<input type="submit" name="ecpt-submit" id="ecpt-submit" class="button-primary" value="Add Post Type"  tabindex="20"/>
			</form>
		<?php endif; ?>
	</div>
</div>
<?php 
}
?>