<?php
function ecpt_tax_manager() {

	global $wpdb;
	global $ecpt_db_tax_name;
	global $tax_objects;
	global $tax_atts;
	global $ecpt_base_dir;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;
	?>
	<div class="wrap">
	<div id="ecpt-wrap">
	
	<?php if($_GET['taxonomy-added']) : ?>
		<div class="updated fade">
			<p>Taxonomy added. Don't forget that you can customize your taxonomy archive layouts with template files.</p>
		</div>
	<?php endif; ?>		
	
	<?php if ($_GET['edit-tax']) : ?>
	<h2>Edit Taxonomy - <a href="admin.php?page=easy-content-types/easy-content-types.php?taxonomies" title="Go Back">Go Back</a></h2>
	<?php else : ?>
	<h2>Easy Custom Taxonomies</h2>
	<?php endif; ?>
	
		<!-- begin FORM -->
		
		<table class="wp-list-table widefat fixed posts">
			<thead>
				<tr>
					<th>Name</th>
					<th>Labels</th>
					<th>Object(s)</th>
					<th>Slug</th>
					<th>Attributes</th>
					<?php if (!$_GET['edit-tax']) : ?>
					<th>Template File</th>
					<?php endif; if ($_GET['edit-tax']) : ?>
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
					<th>Objects)</th>
					<th>Slug</th>
					<th>Attributes</th>
					<?php if (!$_GET['edit-tax']) : ?>
					<th>Template File</th>
					<?php endif; if ($_GET['edit-tax']) : ?>
					<th>Update</th>
					<?php else : ?>
					<th>Edit</th>
					<?php endif; ?>
				</tr>
			</tfoot>
			<tbody>
				<?php
				$i = 0;
				
				// editing a taxonomy
				if ($_GET['edit-tax']) : 
				
				foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_tax_name . " WHERE id ='" . $_GET['edit-tax'] . "';") as $key => $tax)
				{
					?>			
						<tr <?php if(ecpt_is_odd($i)) { echo 'class="alternate"'; } ?>>
							<td>
								<input type="text" name="tax-name" class="ecpt-text no-float" id="tax-name" value="<?php echo $tax->name; ?>" />
								<p>The name is the variable used to query the taxonomy from the database.</p>
							</td>
							<td>
								<span><strong>Single</strong></span><input type="text" name="tax-singlular" class="ecpt-text no-float" id="tax-singlular" value="<?php echo $tax->singular_name; ?>" /><br/>
								<p>The single label is used to refer to single taxonomy items, such as "View Genre".</p>
								
								<span><strong>Plural</strong></span><input type="text" name="tax-plural" class="ecpt-text no-float" id="tax-plural" value="<?php echo $tax->plural_name; ?>" />
								<p>The plural name is used to refer to plural taxonomy items, such as "Search Genres".</p>
							</td>
							<td>
								<select MULTIPLE name="tax-page" id="tax-page" class="ecpt-text no-float ecpt-multi-select"/>
									<?php
										$tax_objects = get_post_types('', 'objects');
										$pages = explode(',', $tax->page);
										foreach ($tax_objects as $object) {
											echo '<option', in_array($object->name, $pages) ? ' selected="selected"' : '', '>', $object->name, '</option>';
										}
									?>
								</select><br/>
								<p>The object is the post type that the taxonomy will be applied to. For example, if you choose "post", then this taxonomy will be available to the regular post object.</p>
							</td>
							<td>
								<input type="text" name="tax-slug" class="ecpt-text no-float" id="tax-slug" value="<?php echo $tax->slug; ?>" />
								<p>The slug is the name you will use in the URL to display your taxonomy archive.</p>
							</td>
							<td>
								<div class="ecpt-atts">
								<?php 

									if($tax->hierarchical == 1) { $checked = 'checked="checked"'; }
									echo '<div><label for="' . $tax->hierarchical . '">Hierarchical</label>';
									echo '<input type="checkbox" name="' . $tax->hierarchical . '" id="tax-hierarchical"' . $checked . '/><br/></div>';									
									$checked = '';
									
									if($tax->show_tagcloud == 1) { $checked = 'checked="checked"'; }
									echo '<div><label for="' . $tax->show_tagcloud . '">Show Tag Cloud</label>';
									echo '<input type="checkbox" name="' . $tax->show_tagcloud . '" id="tax-tagcloud"' . $checked . '/><br/></div>';									
									$checked = '';
									
									if($tax->show_in_nav_menus == 1) { $checked = 'checked="checked"'; }
									echo '<div><label for="' . $tax->show_in_nav_menus . '">Show in Nav Menu</label>';
									echo '<input type="checkbox" name="' . $tax->show_in_nav_menus . '" id="tax-show-in-nav"' . $checked . '/><br/></div>';
									$checked = '';
								?>
								</div>
							</td>
							<td>
								<input type="submit" name="tax-update" id="<?php echo $_GET['edit-tax'];?>" class="button-primary tax-update" value="Update"/>
								<img src="<?php echo $ecpt_base_dir; ?>includes/images/ajax-loader.gif" id="ajax-loader" style="display: none;"/>
							</td>
						</tr>
					<?php $i++;
				}
				
				// displaying list of all taxonomies
				else : 
				
				foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_tax_name . ";") as $key => $tax)
				{
					?>			
						<tr <?php if(ecpt_is_odd($i)) { echo 'class="alternate"'; } ?> id="ecpt-tax-<?php echo $tax->id; ?>">
							<td><?php echo $tax->name; ?></td>
							<td><?php echo 'Single: ' . $tax->singular_name . '<br/>Plural: ' . $tax->plural_name; ?></td>
							<td>
								<ul>
								<?php
									$pages = explode(',', $tax->page);
									foreach ($pages as $page) {
										echo '<li>' . $page . '</li>';
									}
								?>
								</ul>
							</td>
							<td>
								<?php echo $tax->slug; ?>
							</td>
							<td>
								<?php 
									$atts = array();
									if($tax->hierarchical == 1){ $atts[] = 'hierarchical'; }
									if($tax->show_tagcloud == 1){ $atts[] = 'tagcloud'; }
									if($tax->show_in_nav_menus == 1){ $atts[] = 'show in nav menus'; }

									foreach($atts as $att) { echo $att . ', '; }
								?>
							</td>
							
							<td>
								Specific Term: <em>taxonomy-<?php echo $tax->name;?>-{term}.php</em><br/>
								Archive: <em>taxonomy-<?php echo $tax->name; ?>.php</em>
							</td>
							<td>
								<a href="admin.php?page=easy-content-types/easy-content-types.php?taxonomies&edit-tax=<?php echo $tax->id; ?>" title="edit" class="ecpt-edit-taxonomy" id="ecpt-edit-<?php echo $tax->id; ?>">Edit</a> |
								<a href="edit-tags.php?taxonomy=<?php echo str_replace(' ', '_', strtolower($tax->name)); ?>&post_type=<?php echo $tax->page; ?>" title="edit" class="ecpt-edit-taxonomy" id="ecpt-edit-<?php echo $tax->id; ?>">View</a> |
								<a href="admin.php?page=easy-content-types/easy-content-types.php?taxonomies" title="Delete" class="ecpt-delete-taxonomy" id="ecpt-delete-<?php echo $tax->id; ?>">Delete</a>
								<img src="<?php echo $ecpt_base_dir; ?>includes/images/ajax-loader.gif" class="ajax-loader" style="display: none;"/>
							</td>
						</tr>
					<?php $i++;
				}
				
				endif;
				?>
			</tbody>
		</table>
		
		<?php if (!$_GET['edit-tax']) :  ?>
		
			<!--custom taxonomy creation form-->
			<h3>Create New Custom Taxonomy</h3>
			<form method="post" action="" id="ecpt-settings">
				<fieldset>
					<legend>Taxonomy General</legend><br/>
					
					<label for="taxonomy-name">Taxonomy Name<span class="required">*</span></label>
					<input type="text" name="taxonomy-name" id="ecpt-taxonomy-name" class="ecpt-text" tabindex="1"/>
					<p class="ecpt-description">This is the main name of the taxonomy<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This is the name referenced by the database, if you don't know what that means, don't worry about it. Just enter a name that makes sense to you</span></a></p><br/>
					
					<label for="taxonomy-object">Object<span class="required">*</span></label>
					<select name="taxonomy-object[]" MULTIPLE id="ecpt-taxonomy-object" class="ecpt-text ecpt-multi-select" tabindex="2"/>
						<?php 
						$custom_post_type_objs = get_post_types('', 'objects');
						foreach ($custom_post_type_objs as $custom_post_type_obj) {
							echo '<option>' . $custom_post_type_obj->name . '</option>';
						}
						?>
					</select>
					<p class="ecpt-description">This is the post type that will use this taxonomy.<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">You may give this taxonomy to more than one post type by holding the Contral (Command on a Mac) and selecting more than one</span></a></p><br/>
				</fieldset><br/>
				<fieldset>
					<legend>Labels</legend><br/>
					
					<label for="label-single">Singular Label</label>
					<input type="text" name="label-single" class="ecpt-text" tabindex="3"/>
					<p class="ecpt-description">The label used for single taxonomy items, such as "Genre"<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This is the name that will be used to refer to single items of this taxonomy. If you leave this blank, the name field above will be used</span></a></p><br/>

					<label for="label-plural">Plural Label</label>
					<input type="text" name="label-plural" class="ecpt-text" tabindex="4"/>
					<p class="ecpt-description">The label used for plural taxonomy items, such as "Genres"<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This is the name that will be used to refer to plural items. If you leave it blank, the name field above will be used</span></a></p><br/>
					
				</fieldset><br/>
				
				<fieldset>
					<legend>Taxonomy Options</legend><br/>
					
					<label for="options-hierarchial">Hierarchical?</label>
					<input type="checkbox" name="options-hierarchial" class="ecpt-checkbox" tabindex="5"/>
					<p class="ecpt-description">Enabling this means that items can have parent and child items<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This means that you will be able to create tiers with this taxonomy, just like the default post categories. Great for catalogs</span></a></p><br/>

					<label for="options-hierarchial">Show Tag Cloud?</label>
					<input type="checkbox" name="options-tagcloud" class="ecpt-checkbox" tabindex="6"/>
					<p class="ecpt-description">Enabling this means that this taxonomy can be displayed as a tag cloud<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will allow the terms within this taxonomy to be displayed as a tag cloud, allowing your visitors to view posts by taxonomy term, and also which terms are the most popular. Just like post tags.</span></a></p><br/>
					
					
					<label for="options-nav">Show in Nav Menus?</label>
					<input type="checkbox" name="options-nav" class="ecpt-checkbox" checked="checked" tabindex="7"/>
					<p class="ecpt-description">Checking this will cause this taxonomy to show up in the custom nav menu interface<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This will allow you to add individual taxonomies and taxonomy terms to the WP nav menus</span></a></p><br/>
					
					<label for="options-slug">Slug</label>
					<input type="text" name="options-slug" class="ecpt-text" tabindex="8"/>
					<p class="ecpt-description">The URL friendly "nice name" of your taxonomy<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">The slug will be displayed in the URL when accessing your taxonomy archives. Example, if you give your Genres taxonomy a slug of "song-types", your archive URL will be http://yoursite.com/song-types, or similar depending on your permalink structure.<br/> The slug is useful when you have a taxonomy and post type (or regular page) that are named the same thing.<br/>This is an advanced option and should be left blank by most</span></a></p><br/>
					
				</fieldset><br/>
				
				<input type="submit" id="ecpt-submit" class="button-primary" value="Add Taxonomy" tabindex="9"/>
			</form>
		<?php endif; ?>
	</div>
</div>
<?php 
}
?>