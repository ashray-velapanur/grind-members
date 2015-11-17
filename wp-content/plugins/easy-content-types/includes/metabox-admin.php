<?php
function ecpt_metabox_manager() {

	global $wpdb;
	global $ecpt_db_meta_name;
	global $ecpt_db_meta_fields_name;
	global $field_types;
	global $metabox_pages;
	global $metabox_contexts;
	global $metabox_priorities;
	global $ecpt_base_dir;

	if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;
	?>
	<div class="wrap">
	<div id="ecpt-wrap">
	
	<?php if($_GET['metabox-added']) : ?>
		<div class="updated fade">
			<p>Meta Box added. Now you should add some fields to your meta box by clicking "Edit Fields"</p>
		</div>
	<?php endif; ?>		
	
	<!--edit specific field-->
	<?php if(($_GET['fields-edit'] && $_GET['edit-field'])): ?>
		<h2>
			Edit Field - <a href="javascript:history.go(-1)" title="Go Back">Go Back</a>
		</h2>
		<form id="edit-field" type="post">
			<table class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th>Name</th>
						<th>Type</th>
						<th>Description</th>
						<th>Options</th>
						<th>Update</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Name</th>
						<th>Type</th>
						<th>Description</th>
						<th>Options</th>
						<th>Update</th>
					</tr>
				</tfoot>
				<tbody>		
				<?php foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_fields_name . " WHERE id='" . $_GET['edit-field'] . "';") as $key => $field) { ?>		
					<tr <?php if(ecpt_is_odd($i)) { echo 'class="alternate"'; } ?> id="ecpt-field-<?php echo $field->id; ?>">
						<td>
							<input type="text" id="field-name" name="field-name" value="<?php echo $field->nicename; ?>"  class="ecpt-text no-float"/>
							<p>The field name is displayed next to the field and also used for displaying the field content.</p>
						</td>
						<td>
							<select name="field-type" id="field-type" class="ecpt-text no-float"/>
								<?php
								foreach ($field_types as $option) {
									echo '<option id="' . $option . '"', $field->type == $option ? ' selected="selected"' : '', '>', $option, '</option>';
								}
								?>
							</select>
							<p>The field type determines what kind of field is displayed</p>
						</td>						
						<td>
								<input type="text" id="field-desc" name="field-desc" value="<?php echo $field->description; ?>"  class="ecpt-text no-float"/>
								<p>The field description is display beneath the field in the metabox</p>
						</td>
						<td>
							<?php if($field->type == 'textarea') { ?>
								<?php if($field->rich_editor == 1) { $checked = 'checked="checked"'; } ?>
									<input type="checkbox" class="ecpt-checkbox" id="rich-editor" name="rich-editor" <?php if($checked) { echo $checked; } ?>/>
									<p>Enable the rich editor?</p>
								<?php $checked = ''; ?>
							<?php } else if ($field->type == 'select' || $field->type == 'radio') { ?>
								<input type="text" id="field-options" name="field-options" value="<?php echo $field->options; ?>" class="ecpt-text no-float"/>
								<p>Set the available field options here, each separated by a comma. Options are only for radio and select field types.</p>
							<?php } else if ($field->type == 'slider') { ?>
								<input type="text" id="field-max" name="field-max" value="<?php echo $field->max; ?>" class="ecpt-text no-float"/>
								<p>Enter the maximum value accepted here</p>
							<?php } ?>
						</td>
					
						<td>
							<input type="submit" name="field-update" id="<?php echo $_GET['edit-field'];?>" class="button-primary field-update" value="Update"/>
							<img src="<?php echo $ecpt_base_dir; ?>includes/images/ajax-loader.gif" id="ajax-loader" style="display: none;"/>
						</td>
					</tr>
				<?php $i++;
				} ?>
			</table>		
		</form>	
			
		<!--edit fields-->
	<?php elseif($_GET['fields-edit']): ?>
			<?php 
			foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_name . " WHERE id='" . $_GET['fields-edit'] . "';") as $key => $metabox) { ?>
			<h2>
				Edit Fields for <?php echo $metabox->name; ?> - 
				<a href="admin.php?page=easy-content-types/easy-content-types.php?metaboxes" title="Go Back">Go Back</a>
			</h2>
			<table class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th>Order</th>
						<th>Name</th>
						<th>Type</th>
						<th>Description</th>
						<th>Options</th>
						<th>Shortcode</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Order</th>
						<th>Name</th>
						<th>Type</th>
						<th>Description</th>
						<th>Options</th>
						<th>Shortcode</th>
						<th>Delete</th>
					</tr>
				</tfoot>
				<tbody>
				<?php $i = 1; ?>
				<?php foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_fields_name . " WHERE parent='" . $metabox->name . "' ORDER BY list_order;") as $key => $field) { 
						?>			
							<tr id="recordsArray_<?php echo $field->id; ?>" class="ecpt-field <?php if(ecpt_is_odd($i)) { echo 'alternate'; } ?>">
								<td><a href="#" class="dragHandle"></a></td>
								<td><?php echo $field->nicename; ?></td>
								<td><?php echo $field->type; ?></td>
								<td><?php echo $field->description; ?></td>
								<td>
									<?php if($field->type == 'textarea') { ?>
										<?php if($field->rich_editor == 1) { echo 'Rich Editor'; } else { echo 'Plain Text'; } ?>
									<?php } else if($field->type == 'select' ||$field->type == 'radio') { ?>
										<?php echo $field->options; ?>
									<?php } else if($field->type == 'slider') { ?>
										<?php echo 'Max: ' . $field->max; ?>
									<?php } ?>
								</td>
								<td>[ecpt_field id="<?php echo $field->name;?>"]</td>
								<td>
									<a href="admin.php?page=easy-content-types/easy-content-types.php?metaboxes&fields-edit=8&edit-field=<?php echo $field->id; ?>" title="Edit" class="ecpt-edit-field" id="ecpt-edit-field-<?php echo $field->id; ?>">Edit</a> |
									<a href="#" title="Delete" class="ecpt-delete-field" id="ecpt-delete-field-<?php echo $field->id; ?>">Delete</a>
									<img src="<?php echo $ecpt_base_dir; ?>includes/images/ajax-loader.gif" class="ajax-loader" style="display: none;"/>
								</td>
							</tr>
						<?php $i++;
				} ?>
			</table>
			<?php } ?>
		
			<!--custom metabox field creation form-->
			<h3>Create New Metabox Field</h3>
			<form method="post" id="ecpt-settings">
				<fieldset>
					<legend>Field Info</legend><br/>
					
					<?php 
					$counter = 0;
					foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_fields_name . " ORDER BY id DESC LIMIT 1;") as $key => $field) {
						// add all the list orders together
						$id = $field->id;
						$newID = $id + 1; // add 1 to the last ID
					} ?>
					<input type="hidden" value="<?php echo $metabox->id; ?>" name="current-field"/>
					<input type="hidden" value="<?php echo $metabox->name; ?>" name="field-parent"/>
					<input type="hidden" value="<?php echo $newID; // last ID + 1 ?>" name="field-order"/>
					
					<label for="field-name">Field Name<span class="required">*</span></label>
					<input type="text" name="field-name" id="ecpt-field-name" class="ecpt-text" tabindex="1"/>
					<p class="ecpt-description">This is the main name of the field<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This is the name that will be used to display field data on the site, and also will be display next to the field in the post editor</span></a></p><br/>
					
					<label for="field-desc">Field Description</label>
					<input type="text" name="field-desc" id="ecpt-field-desc" class="ecpt-text" tabindex="2"/>
					<p class="ecpt-description">The field description<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This is description of the field that will be shown beneath the field in the editor</span></a></p><br/>
									
					<label for="field-type">Type</label>
					<select name="field-type" id="ecpt-field-type" class="ecpt-text"  tabindex="3"/>
						<?php foreach($field_types as $field_type) {
							echo '<option id="' . $field_type . '">' . $field_type . '</option>';
						} ?>
					</select>
					<p class="ecpt-description">The type of field you would like to insert<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">If you choose "select" or "radio" a new input will become available for you to enter the options</span></a></p><br/>
					
					<div class="ecpt-disabled">
						<label for="field-options">Options</label>
						<input type="text" name="field-options" id="ecpt-field-options" class="ecpt-text"  tabindex="4"/>
						<p class="ecpt-description">Options for select and radio fields. Separate options with a comma<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">A sample set of options would look like this (without the quotes): "option 1, option 2, option 3"</span></a></p><br/>
					</div>
					<div class="ecpt-rich-editor-disabled">
						<label for="rich-editor">Rich Editor</label>
						<input type="checkbox" name="rich-editor" id="ecpt-rich-editor" class="ecpt-checkbox"  tabindex="5"/>
						<p class="ecpt-description">Enable the Rich Editor for this textarea?<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This option will enable "rich" formatting controls, such as italic, bold, alignment, link, etc</span></a></p><br/>
					</div>
					
					<div class="ecpt-max-disabled">
						<label for="field-max">Max Value</label>
						<input type="text" name="field-max" id="ecpt-field-max" class="ecpt-text"  tabindex="6"/>
						<p class="ecpt-description">Enter the maximum value for this slider<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">This is the highest value that the slider can "slide" too.</span></a></p><br/>
					</div>
					
				</fieldset><br/>
				
				<input type="submit" id="ecpt-submit" class="button-primary" value="Add Field"/>
			</form>			
		
	<!--metabox-->
	<?php else : ?>
		<?php if($_GET['metabox-edit']): ?>
		<h2>
			Edit Metabox - 
				<a href="admin.php?page=easy-content-types/easy-content-types.php?metaboxes" title="Go Back">Go Back</a>
		</h2>
		<?php else : ?>
		<h2>Easy Custom Meta Boxes</h2>
		<?php endif; ?>
			<table class="wp-list-table widefat fixed posts">
				<thead>
					<tr>
						<th>Name</th>
						<th>Page</th>
						<th>Context</th>
						<th>Priority</th>
						<?php if (!$_GET['metabox-edit']): ?>
						<th>Fields</th>
						<?php endif; ?>
						<th>Edit</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Name</th>
						<th>Page</th>
						<th>Context</th>
						<th>Priority</th>
						<?php if (!$_GET['metabox-edit']): ?>
						<th>Fields</th>
						<?php endif; ?>
						<th>Edit</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$i = 0;	
					
					// editing a meta box
					if ($_GET['metabox-edit']):
					
						foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_name . " WHERE id = '" . $_GET['metabox-edit'] . "';") as $key => $metabox)
						{
							$name = $metabox->name;
							?>			
								<tr <?php if(ecpt_is_odd($i)) { echo 'class="alternate"'; } ?>>
									<td>
										<input type="text" id="metabox-name" name="metabox-name" value="<?php echo $metabox->nicename; ?>"  class="ecpt-text no-float"/>
										<p>The meta box name is displayed at the top of the meta box on the editor screen</p>
									</td>
									<td>
										<select name="metabox-page" id="metabox-page" class="ecpt-text no-float"/>
											<?php
												$pages = get_post_types('', 'objects');
												$metabox_pages = array();
												foreach($pages as $page) { $metabox_pages[] = $page->name; }
												$metabox_pages[] = 'link';
												
												foreach ($metabox_pages as $metabox_page) {
													echo '<option', $metabox->page == $metabox_page ? ' selected="selected"' : '', '>', $metabox_page, '</option>';
												}
								
											?>
										</select>
										<p>The page is the post type that the meta box is displayed on. For example, if you choose "post", the meta box will be displayed on the regular post screen.</p>
									</td>
									<td>
										<select name="metabox-context" id="metabox-context" class="ecpt-text no-float"/>
											<?php
												foreach ($metabox_contexts as $metabox_context) {
													echo '<option', $metabox->context == $metabox_context ? ' selected="selected"' : '', '>', $metabox_context, '</option>';
												}
											?>
										</select>
										<p>The context is the location on the editor screen thatthe metabox will be displayed</p>
									</td>
									<td>
										<select name="metabox-priority" id="metabox-priority" class="ecpt-text no-float"/>
											<?php
												foreach ($metabox_priorities as $priority) {
													echo '<option', $metabox->priority == $priority ? ' selected="selected"' : '', '>', $priority, '</option>';
												}
											?>
										</select>
										<p>Priority determines how "high" on the editor screen the meta box appears</p>
									</td>
									<td>
										<input type="submit" name="metabox-update" id="<?php echo $_GET['metabox-edit'];?>" class="button-primary metabox-update" value="Update"/>
										<img src="<?php echo $ecpt_base_dir; ?>includes/images/ajax-loader.gif" id="ajax-loader" style="display: none;"/>
									</td>
								</tr>
							<?php $i++;
						}
					// viewing all metaboxes	
					else: 
					
						foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_name . ";") as $key => $metabox)
						{
							$name = $metabox->name;
							?>			
								<tr <?php if(ecpt_is_odd($i)) { echo 'class="alternate"'; } ?> id="ecpt-metabox-<?php echo $metabox->id; ?>">
									<td><?php echo $metabox->nicename; ?></td>
									<td><?php echo $metabox->page; ?></td>
									<td><?php echo $metabox->context; ?></td>
									<td><?php echo $metabox->priority; ?></td>
									<td>
										<?php
											foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_fields_name . " WHERE parent='" . $name . "';") as $key => $meta_field)	
											{
												echo $meta_field->nicename . ', ';
											}
										?>
									</td>
									<td>
										<a href="admin.php?page=easy-content-types/easy-content-types.php?metaboxes&metabox-edit=<?php echo $metabox->id; ?>" title="Edit Metabox" class="ecpt-edit">Edit</a> | 
										<a href="admin.php?page=easy-content-types/easy-content-types.php?metaboxes&fields-edit=<?php echo $metabox->id; ?>" title="Edit Fields" class="ecpt-edit">Edit Fields</a> | 
										<a href="#" title="Delete <?php echo $metabox->name; ?>" class="ecpt-delete-metabox" id="ecpt-delete-<?php echo $metabox->id; ?>">Delete</a>
										<img src="<?php echo $ecpt_base_dir; ?>includes/images/ajax-loader.gif" class="ajax-loader" style="display: none;"/>
									</td>
								</tr>
							<?php $i++;
						}
						
					endif;
					?>
				</tbody>
			</table>
			<?php if (!$_GET['metabox-edit']): ?>
			<p>Click the "Edit Fields" link in order to add fields to a meta box</p>
			<p>Need help displaying the content from your meta boxes? <a href="admin.php?page=easy-content-types/easy-content-types.php?help#field-info">Click here</a>.
			<?php endif; ?>
			<?php 
			// only show the add new box form if not editing
			if (!$_GET['metabox-edit']): ?>
			<!--custom metabox creation form-->
			<h3>Create New Custom Metabox</h3>
			<form method="post" action="<?php echo $ecpt_base_filse; ?>" id="ecpt-settings">
				<fieldset>
					<legend>Metabox General</legend><br/>
					
					<label for="metabox-name">Metabox Name<span class="required">*</span></label>
					<input type="text" name="metabox-name" id="ecpt-metabox-name" class="ecpt-text"/>
					<p class="ecpt-description">This is the main name of the metabox<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">The metabox name will show up in the header of the box on the editor screen</span></a></p><br/>
					
					<label for="metabox-page">Page</label>
					<select name="metabox-page" id="ecpt-metabox-page" class="ecpt-text"/>
						<?php 
						$pages = get_post_types('', 'objects');
						$metabox_pages = array();
						foreach($pages as $page) { $metabox_pages[] = $page->name; }
						$metabox_pages[] = 'link';
						foreach ($metabox_pages as $metabox_page) {
							echo '<option>' . $metabox_page . '</option>';
						}
						?>
					</select>
					<p class="ecpt-description">This is the post type that will use this metabox<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">If you want to put the metabox on the regular Posts screen, then choose "post"</span></a></p><br/>
					
					<label for="metabox-context">Context</label>
					<select name="metabox-context" id="ecpt-metabox-context" class="ecpt-text"/>
						<option>normal</option>
						<option>advanced</option>
						<option>side</option>
					</select>
					<p class="ecpt-description">The location on the editor screen to display the meta box.<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">Advanced / Normal = main column, with Advanced being above Normal. Side = right, narrow column.</span></a></p><br/>
					
					<label for="metabox-priority">Priority</label>
					<select name="metabox-priority" id="ecpt-metabox-priority" class="ecpt-text"/>
						<option>default</option>
						<option>high</option>
						<option>core</option>
						<option>low</option>
					</select>
					<p class="ecpt-description">The priority determines how "high" the meta box appears in the editor<a href="#" class="ecpt-help"><span class="tooltip center midnightblue">Metaboxes with 'high' priorites will appear above boxes with 'default' priority, for example</span></a></p><br/>
					
				</fieldset><br/>
				
				<input type="submit" id="ecpt-submit" class="button-primary" value="Add Meta Box"/>
			</form>
			<?php endif; ?>
	<?php endif; ?>
	</div><!--end #ecpt-wrap-->
	</div><!--end #wrap-->
<?php 
}

?>