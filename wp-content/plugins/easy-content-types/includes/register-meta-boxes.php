<?php

add_filter('tiny_mce_before_init', 'my_tiny_mce_before_init');
function my_tiny_mce_before_init($initArray)
{
	$initArray['plugins'] = str_replace( array('wpfullscreen',',,') , array('', ',') , $initArray['plugins'] );
	return $initArray;
}

add_action('admin_menu', 'ecpt_add_box');
function ecpt_add_box() {

    global $wpdb;
	global $ecpt_prefix;
    global $ecpt_db_meta_name;
    global $ecpt_db_meta_fields_name;
	
	foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_name . ";") as $key => $metabox) 
	{
		$fields_array = array();
		foreach($wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_fields_name . " WHERE parent = '" . $metabox->name ."' ORDER BY list_order;") as $key => $meta_field) 
		{
			$options == '';
			$options = explode(',', $meta_field->options);

			$fields = array(
				'name' 			=> $meta_field->nicename,
				'desc' 			=> $meta_field->description,
				'id' 			=> $ecpt_prefix . $meta_field->name,
				'class' 		=> $ecpt_prefix . $meta_field->name,
				'type' 			=> $meta_field->type,
				'rich_editor' 	=> $meta_field->rich_editor,
				'options' 		=> $options,
				'max' 			=> $meta_field->max
			);
			$fields_array[] = $fields;
		}
	
		$meta_box_fields = array(
			'context' => 'normal',
			'priority' => 'high',
			'fields' => $fields_array
			
		);
		add_meta_box('ecpt_metabox_' . $metabox->id, $metabox->nicename, 'ecpt_show_box', $metabox->page, $metabox->context, $metabox->priority, $meta_box_fields);
		
	}	
}

function ecpt_show_box($post, $metabox)	{
    global $post;
    global $ecpt_prefix;
	
    // Use nonce for verification
    echo '<input type="hidden" name="ecpt_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    
	
	
    echo '<table class="form-table">';

    foreach ($metabox['args']['fields'] as $field) {
        // get current post meta data

        $meta = get_post_meta($post->ID, $field['id'], true);
        
        echo '<tr>',
                '<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
                '<td>';
        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" /><br/>', '
', $field['desc'];
                break;
			case 'date':
                echo '<input type="text" class="ecpt_datepicker" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />', '
', $field['desc'];
                break;
			case 'upload':
                echo '<input type="text" class="ecpt_upload_field" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:80%" /><input class="upload_image_button" type="button" value="Upload Image" /><br/>', '
', $field['desc'];
                break;
            case 'textarea':
				if($field['rich_editor'] == 1) {
					// this is the old method of enabling the RTE. Now it only needs the class name.
					//wp_tiny_mce(true, array('editor_selector' => $field['class'], 'remove_linebreaks' => false) );
					echo '<div style="width: 97%; border: 1px solid #DFDFDF;"><textarea name="', $field['id'], '" class="theEditor ', $field['class'], '" id="', $field['id'], '" cols="60" rows="8" style="width:97%">', $meta ? $meta : $field['std'], '</textarea></div>', '
', $field['desc'];
				} else {
					echo '<div style="width: 100%;"><textarea name="', $field['id'], '" class="', $field['class'], '" id="', $field['id'], '" cols="60" rows="8" style="width:97%">', $meta ? $meta : $field['std'], '</textarea></div>', '
', $field['desc'];				
				}
                break;
            case 'select':
                echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                foreach ($field['options'] as $option) {
					echo '<option value="' . $option . '"', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                echo '</select>', '
', $field['desc'];
                break;
            case 'radio':
                foreach ($field['options'] as $option) {
                    echo '<input type="radio" name="', $field['id'], '" value="', $option, '"', $meta == $option ? ' checked="checked"' : '', ' />&nbsp;', $option;
                }
				echo '<br/>' . $field['desc'];
                break;
            case 'checkbox':
                echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />&nbsp;';
				echo $field['desc'];
                break;
			case 'slider':
				echo '<input type="text" rel="' . $field['max'] . '" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="1" style="float: left; margin-right: 5px" />';
				echo '<div class="ecpt-slider" rel="' . $field['id'] . '" style="float: left; width: 60%; margin: 5px 0 0 0;"></div>';		
				echo '<div style="width: 100%; clear: both;">' . $field['desc'] . '</div>';
				break;
        }
        echo     '<td>',
            '</tr>';
    }
    
    echo '</table>';
}

// Save data from meta box
add_action('save_post', 'ecpt_save_data');
function ecpt_save_data($post_id) {

    global $wpdb;
	global $ecpt_prefix;
  	global $ecpt_db_meta_name;
  	global $ecpt_db_meta_fields_name;  
	
    // verify nonce
    if (!wp_verify_nonce($_POST['ecpt_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    
	// first get the metaboxes associated with this post
    foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_name . " WHERE page='" . $_POST['post_type'] . "';") as $key => $metabox)
	{
	
		// now get all the fields associated with this metabox
		foreach( $wpdb->get_results("SELECT * FROM " . $ecpt_db_meta_fields_name . " WHERE parent='" . $metabox->name . "';") as $key => $field)
		{
			$old = get_post_meta($post_id, $ecpt_prefix . $field->name, true);
			$data = $_POST[$ecpt_prefix . $field->name];
			
			if($field->type == 'textarea' && $field->rich_editor == 1) {
				// preserve line breaks for rich textareas
				$new = wpautop($data);
			} else {
				$new = $data;
			}
			if ($new && $new != $old) {
				update_post_meta($post_id, $ecpt_prefix . $field->name, $new);
			} elseif ('' == $new && $old) {
				delete_post_meta($post_id, $ecpt_prefix . $field->name, $old);
			}
		}	
	}
}


?>