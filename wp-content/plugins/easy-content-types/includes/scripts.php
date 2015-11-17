<?php

function ecpt_admin_styles() 
{
	global $ecpt_base_dir;
	wp_enqueue_style('thickbox');
	wp_enqueue_style('ecpt-admin', $ecpt_base_dir . 'includes/css/admin-styles.css');
	wp_enqueue_style('tooltip-css', $ecpt_base_dir . 'includes/css/thetooltip.css');
}

function ecpt_admin_scripts()
{
	wp_enqueue_script('media-upload'); 
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_style('ecpt-jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/base/jquery-ui.css');
	wp_enqueue_script('ecpt-jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/jquery-ui.min.js', array('jquery'));
}

function ecpt_echo_admin_scripts()
{
	global $ecpt_base_dir;
?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(function($){
		
			// show tool tips on click
			$('a.ecpt-help').click(function(e) {
				e.preventDefault();
				var showToolTip = {
					'text-decoration' : 'none',
					'visibility' : 'visible',
					'opacity' : '1',
					'-moz-transition' : 'all 0.2s linear',
					'-webkit-transition' : 'all 0.2s linear',
					'-o-transition' : 'all 0.2s linear',
					'transition' : 'all 0.2s linear'
				}
				var hideToolTip = {
					'visibility' : 'hidden',
					'opacity' : '0',
					'-moz-transition' : 'all 0.4s linear',
					'-webkit-transition' : 'all 0.4s linear',
					'-o-transition' : 'all 0.4s linear',
					'transition' : 'all 0.4s linear'
				}
				$(this).children().css(showToolTip);
				$(this).mouseout(function(){
					$(this).children().css(hideToolTip);
				});
			});		
		
		
			<?php if (isset($_GET['page']) && $_GET['page'] == 'easy-content-types/easy-content-types.php?posttypes') { ?>

			// upload / insert image function
			var txtBox_id = '';
			$('.upload_image_button').click(function() {
				txtBox_id = '#'+$(this).prop('id').replace('_button', '');
				image_id = '#image_'+$(this).prop('id').replace('upload_image_button_', '');
				formfield = $(txtBox_id);
				tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
				return false;
			});
			// send the selected image to the iamge url field
			window.send_to_editor = function(html) {
				imgurl = $('img',html).prop('src');
				$(txtBox_id).val(imgurl);
				$(image_id).prop('src', imgurl)
				tb_remove();
			}
			
			// delete post type function
			$('#ecpt-wrap .ecpt-delete').click(function(){
				var del_id = ''+$(this).prop('id').replace('ecpt-delete-', '');
				var info = 'delete_post_type=' + del_id;
				var row_id = '#ecpt-posttype-' + del_id;
				if(confirm("Do you you really want to delete this Post Type?"))
				{
				
					$(this).siblings('.ajax-loader').ajaxStart(function() {
					  $(this).show();				  
					});
					$(this).siblings('.ajax-loader').ajaxStop(function() {
						$(this).fadeOut();					
					});
				
					$.ajax({
						type: "POST",
						url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
						data: info,
						success: function() {
							$(row_id).fadeOut(200, function(){
								$(row_id).remove();			
							});
							window.location = '<?php echo get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?posttypes';?>';	
						}
					});
				}
				return false;
			});	
			
			// update posttype function
			$('#ecpt-wrap .posttype-update').click(function(){
				var update_id 		= $(this).prop('id');
				var name 			= $("input#posttype-name").val(); 
				var singular 		= $("input#posttype-singlular").val(); 
				var plural 			= $("input#posttype-plural").val(); 
				var icon 			= $("input.posttype-menu-icon").val(); 
				var position		= $("input#posttype-position").val(); 
				var slug			= $("input#posttype-slug").val(); 
				
				if(slug == '') { alert('The post type slug cannot be empty'); return false;}
				
				var title = 0; var editor = 0; var author = 0; var thumbnail = 0;
				var excerpt = 0; var fields = 0; var comments = 0; var revisions = 0; var has_archive = 0;
				var show_in_nav_menus = 0; var exclude_from_search = 0;
				
				if ($("input#posttype-title").prop('checked') == true) { var title = 1; }
				if ($("input#posttype-editor").prop('checked') == true) { var editor = 1; }
				if ($("input#posttype-author").prop('checked') == true) { var author = 1; }
				if ($("input#posttype-thumbnail").prop('checked') == true) { var thumbnail = 1; }
				if ($("input#posttype-excerpt").prop('checked') == true) { var excerpt = 1; }
				if ($("input#posttype-fields").prop('checked') == true) { var fields = 1; }
				if ($("input#posttype-comments").prop('checked') == true) { var comments = 1; }
				if ($("input#posttype-revisions").prop('checked') == true) { var revisions = 1; }
				if ($("input#posttype-hierarchical").prop('checked') == true) { var hierarchical = 1; }
				if ($("input#posttype-page_attributes").prop('checked') == true) { var page_attributes = 1; }
				if ($("input#posttype-post_formats").prop('checked') == true) { var post_formats = 1; }
				if ($("input#posttype-has_archive").prop('checked') == true) { var has_archive = 1; }
				if ($("input#posttype-show_in_nav_menus").prop('checked') == true) { var show_in_nav_menus = 1; } 
				if ($("input#posttype-exclude_from_search").prop('checked') == true) { var exclude_from_search = 1; } 
				
				var info = 'update_posttype=' + update_id + '&posttype-name=' + name + 
							'&posttype-singular=' + singular + 
							'&posttype-plural=' + plural + 
							'&posttype-menu-icon=' + icon + 
							'&posttype-title=' + title + 
							'&posttype-editor=' + editor + 
							'&posttype-author=' + author + 
							'&posttype-thumbnail=' + thumbnail + 
							'&posttype-excerpt=' + excerpt + 
							'&posttype-fields=' + fields + 
							'&posttype-comments=' + comments + 
							'&posttype-revisions=' + revisions + 
							'&posttype-hierarchical=' + hierarchical + 
							'&posttype-has_archive=' + has_archive + 
							'&posttype-page_attributes=' + page_attributes + 
							'&posttype-post_formats=' + post_formats + 
							'&posttype-show_in_nav_menus=' + show_in_nav_menus + 
							'&posttype-position=' + position + 
							'&posttype-slug=' + slug + 
							'&posttype-exclude_from_search=' + exclude_from_search;
				
				$('#ajax-loader').ajaxStart(function() {
				  $(this).show();				  
				});
				$('#ajax-loader').ajaxStop(function() {
				    $(this).fadeOut();					
				});
				
				$.ajax({
					type: "POST",
					url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
					data: info,
					success: function() {
						window.location = 'admin.php?page=easy-content-types/easy-content-types.php?posttypes';	
					}
				});				
				return false;
			});	
			// check for posttype name on submit
			$('#ecpt-submit').click(function() {
				if($('#ecpt-post-type-name').val() == '') {
					alert('You must enter a post type name');
					return false;
				}
			});

			<?php } if (isset($_GET['page']) && $_GET['page'] == 'easy-content-types/easy-content-types.php?taxonomies') { ?>
			// delete taxonomy function
			$('#ecpt-wrap .ecpt-delete-taxonomy').click(function(){
				var del_id = ''+$(this).prop('id').replace('ecpt-delete-', '');
				var info = 'delete_taxonomy=' + del_id;
				var row_id = '#ecpt-tax-' + del_id;
				if(confirm("Do you you really want to delete this Taxonomy?"))
				{
					$(this).siblings('.ajax-loader').ajaxStart(function() {
					  $(this).show();				  
					});
					$(this).siblings('.ajax-loader').ajaxStop(function() {
						$(this).fadeOut();					
					});
				
					$.ajax({
						type: "POST",
						url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
						data: info,
						success: function() {
							$(row_id).fadeOut(200, function(){
								$(row_id).remove();			
							});
							window.location = 'admin.php?page=easy-content-types/easy-content-types.php?taxonomies';	
						}
					});
				}
				return false;
			});
			// check for taxonomy name on submit
			$('#ecpt-submit').click(function() {
				
				if($('#ecpt-taxonomy-name').val() == '') {
					alert('You must enter a taxonomy name');
					return false;
				}
				if(!$('#ecpt-taxonomy-object option:selected').length) {
					alert('You must select at least on taxonomy object');
					return false;
				}
			});
			
			<?php } if (isset($_GET['page']) && $_GET['page'] == 'easy-content-types/easy-content-types.php?metaboxes') { ?>
			// delete metabox function
			$('#ecpt-wrap .ecpt-delete-metabox').click(function(){
				var del_id = ''+$(this).prop('id').replace('ecpt-delete-', '');
				var fields_id = ''+$(this).prop('title').replace('Delete ', '');
				var info = 'delete_metabox=' + del_id + '&delete_meta_fields=' + fields_id;
				var row_id = '#ecpt-metabox-' + del_id;
				if(confirm("Do you you really want to delete this Metabox?"))
				{
					$(this).siblings('.ajax-loader').ajaxStart(function() {
					  $(this).show();				  
					});
					$(this).siblings('.ajax-loader').ajaxStop(function() {
						$(this).fadeOut();					
					});
				
					$.ajax({
						type: "POST",
						url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
						data: info,
						success: function() {
							$(row_id).fadeOut(200, function(){
								$(row_id).remove();			
							});
							window.location = 'admin.php?page=easy-content-types/easy-content-types.php?metaboxes';	
						}
					});
				}
				return false;
			});
			// delete field function
			$('#ecpt-wrap .ecpt-delete-field').click(function(){
				var del_id = ''+$(this).prop('id').replace('ecpt-delete-field-', '');
				var info = 'delete_field=' + del_id;
				var row_id = '#recordsArray_' + del_id;
				if(confirm("Do you you really want to delete this Field?"))
				{				
					$(this).siblings('.ajax-loader').ajaxStart(function() {
					  $(this).show();				  
					});
					$(this).siblings('.ajax-loader').ajaxStop(function() {
						$(this).fadeOut();					
					});
						
					$.ajax({
						type: "POST",
						url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
						data: info,
						success: function() {
							$(row_id).fadeOut(200, function(){
								$(row_id).remove();			
							});
						}
					});
				}
				return false;
			});
			// check for meatbox name on submit
			$('#ecpt-submit').click(function() {
				if($('#ecpt-metabox-name').val() == '') {
					alert('You must enter a meatbox name');
					return false;
				}
			});
			
			// make fields sortable via drag and drop
			$(function() {	
				$(".wp-list-table tbody").sortable({
					handle: '.dragHandle', items: '.ecpt-field', opacity: 0.6, cursor: 'move', axis: 'y', update: function() {
						var order = $(this).sortable("serialize") + '&action=updateRecordsListings';
						$.post("<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php", order, function(theResponse){
							
						});
					}
				});			
			});
			
			<?php } if ($_GET['fields-edit']) { ?>
			// update field function
						
			$('#ecpt-wrap .field-update').click(function(){
				var update_id 	= $(this).prop('id');
				var name 		= $("input#field-name").val(); 
				var type 		= $("select#field-type option:selected").val();
				var description	= $("input#field-desc").val(); 
				var options 	= $("input#field-options").val();  
				var max 		= $("input#field-max").val();  
				var rich_editor;
				if ($("input#rich-editor").prop('checked') == true) { rich_editor = 1; } else { rich_editor = 0; }
				
				var info = 
					'update_field=' + update_id + 
					'&field-name=' + name + 
					'&field-type=' + type + 
					'&field-desc=' + description +
					'&rich-editor=' + rich_editor + 
					'&field-max=' + max + 
					'&field-options=' + options;
					
				$('#ajax-loader').ajaxStart(function() {
				  $(this).show();				  
				});
				$('#ajax-loader').ajaxStop(function() {
				    $(this).fadeOut();					
				});	
					
				$.ajax({
					type: "POST",
					url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
					data: info,
					success: function() {
						window.location = 'admin.php?page=easy-content-types/easy-content-types.php?metaboxes';	
					}
				});				
				return false;
			});	
			
			// check for field name on submit
			$('#ecpt-submit').click(function() {
				if($('#ecpt-field-name').val() == '') {
					alert('You must enter a field name');
					return false;
				}
			});
			
			// disable options field unless SELECT or RADIO type is chosen
			$('#ecpt-field-type').change(function(){
				var id = $('option:selected', this).prop("id");
				var hiddenFields = '.ecpt-disabled';
				var richEditor = '.ecpt-rich-editor-disabled';
				var max = '.ecpt-max-disabled';
				if(id == 'select' || id == 'radio') {
					$(hiddenFields).fadeIn();
					$(richEditor).fadeOut();
					$(max).fadeOut();
				} else if(id == 'textarea') {
					$(richEditor).fadeIn();
					$(hiddenFields).fadeOut();
					$(max).fadeOut();
				} else if(id == 'slider') {
					$(max).fadeIn();
					$(hiddenFields).fadeOut();
					$(richEditor).fadeOut();
				} else {
					$(hiddenFields).fadeOut();
					$(richEditor).fadeOut();
					$(max).fadeOut();
				}
			});
				<?php if($_GET['edit-field']) { ?>
					// disable options field unless SELECT or RADIO type is chosen
					$('#field-type').change(function(){
						
						var field_id = $('option:selected', this).prop("id");
						if(field_id == 'select' || field_id == 'radio') {
							$('#field-options').fadeTo('slow', 0.5);
							$('#field-options').prop('disabled', 'true');
						} else {
							$('#field-options').fadeTo('fast', 100);
							$('#field-options').prop('disabled', '');
						}
					});				
				<?php } ?>			
			<?php } ?>
			
			<?php if ($_GET['metabox-edit']) { ?>
			// update metabox function
			$('#ecpt-wrap .metabox-update').click(function(){
				var update_id 	= $(this).prop('id');
				var name 		= $("input#metabox-name").val(); 
				var page 		= $("select#metabox-page option:selected").val();
				var context		= $("select#metabox-context option:selected").val();
				var priority	= $("select#metabox-priority option:selected").val();
				var info 		= 'update_metabox=' + update_id + '&metabox-new-name=' + name + '&metabox-page=' + page + '&metabox-context=' + context + '&metabox-priority=' + priority;
				
				$('#ajax-loader').ajaxStart(function() {
				  $(this).show();				  
				});
				$('#ajax-loader').ajaxStop(function() {
				    $(this).fadeOut();					
				});
				
				$.ajax({
					type: "POST",
					url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
					data: info,
					success: function() {
						window.location = 'admin.php?page=easy-content-types/easy-content-types.php?metaboxes';	
					}
				});				
				return false;
			});
			<?php } ?>
			
			<?php if ($_GET['edit-tax']) { ?>
			// update taxonomy function
			
			$('#ecpt-wrap .tax-update').click(function(){
				var update_id 		= $(this).prop('id');
				var name 			= $("input#tax-name").val(); 
				var singular 		= $("input#tax-singlular").val(); 
				var plural 			= $("input#tax-plural").val();
				var slug 			= $("input#tax-slug").val();
				
				if(slug == '') { alert('The taxonomy slug cannot be empty'); return false;}
				
				var pages = "";
				$("select#tax-page option:selected").each(function () {
					pages += $(this).val() + ",";
				});
				
				var hierarchical = 0; var tagcloud = 0; var show_in_nav = 0;
				
				if ($("input#tax-hierarchical").prop('checked') == true) { var hierarchical = 1; }
				if ($("input#tax-tagcloud").prop('checked') == true) { var tagcloud = 1; }
				if ($("input#tax-show-in-nav").prop('checked') == true) { var show_in_nav = 1; } 
				var info = 'update_tax=' + update_id + '&tax-name=' + name + '&tax-singular=' + singular + '&tax-plural=' + plural + '&tax-page=' + pages + '&tax-hierarchical=' + hierarchical + '&tax-tagcloud=' + tagcloud + '&tax-show-in-nav=' + show_in_nav + '&tax-slug=' + slug;
				
				$('#ajax-loader').ajaxStart(function() {
				  $(this).show();				  
				});
				$('#ajax-loader').ajaxStop(function() {
				    $(this).fadeOut();					
				});
				
				$.ajax({
					type: "POST",
					url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
					data: info,
					success: function() {
						window.location = 'admin.php?page=easy-content-types/easy-content-types.php?taxonomies';	
					}
				});				
				return false;
			});	
			<?php } ?>

		}); // end jquery(function($))
		//]]> 
	</script>
<?php
}  

function ecpt_echo_old_admin_scripts()
{
	global $ecpt_base_dir;
?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(function($){
		
			// show tool tips on click
			$('a.ecpt-help').click(function(e) {
				e.preventDefault();
				var showToolTip = {
					'text-decoration' : 'none',
					'visibility' : 'visible',
					'opacity' : '1',
					'-moz-transition' : 'all 0.2s linear',
					'-webkit-transition' : 'all 0.2s linear',
					'-o-transition' : 'all 0.2s linear',
					'transition' : 'all 0.2s linear'
				}
				var hideToolTip = {
					'visibility' : 'hidden',
					'opacity' : '0',
					'-moz-transition' : 'all 0.4s linear',
					'-webkit-transition' : 'all 0.4s linear',
					'-o-transition' : 'all 0.4s linear',
					'transition' : 'all 0.4s linear'
				}
				$(this).children().css(showToolTip);
				$(this).mouseout(function(){
					$(this).children().css(hideToolTip);
				});
			});		
		
		
			<?php if (isset($_GET['page']) && $_GET['page'] == 'easy-content-types/easy-content-types.php?posttypes') { ?>

			// upload / insert image function
			var txtBox_id = '';
			$('.upload_image_button').click(function() {
				txtBox_id = '#'+$(this).attr('id').replace('_button', '');
				image_id = '#image_'+$(this).attr('id').replace('upload_image_button_', '');
				formfield = $(txtBox_id);
				tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
				return false;
			});
			// send the selected image to the iamge url field
			window.send_to_editor = function(html) {
				imgurl = $('img',html).attr('src');
				$(txtBox_id).val(imgurl);
				$(image_id).attr('src', imgurl)
				tb_remove();
			}
			
			// delete post type function
			$('#ecpt-wrap .ecpt-delete').click(function(){
				var del_id = ''+$(this).attr('id').replace('ecpt-delete-', '');
				var info = 'delete_post_type=' + del_id;
				var row_id = '#ecpt-posttype-' + del_id;
				if(confirm("Do you you really want to delete this Post Type?"))
				{
				
					$(this).siblings('.ajax-loader').ajaxStart(function() {
					  $(this).show();				  
					});
					$(this).siblings('.ajax-loader').ajaxStop(function() {
						$(this).fadeOut();					
					});
				
					$.ajax({
						type: "POST",
						url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
						data: info,
						success: function() {
							$(row_id).fadeOut(200, function(){
								$(row_id).remove();			
							});
							window.location = '<?php echo get_bloginfo('wpurl') . '/wp-admin/admin.php?page=easy-content-types/easy-content-types.php?posttypes';?>';	
						}
					});
				}
				return false;
			});	
			
			// update posttype function
			$('#ecpt-wrap .posttype-update').click(function(){
				var update_id 		= $(this).attr('id');
				var name 			= $("input#posttype-name").val(); 
				var singular 		= $("input#posttype-singlular").val(); 
				var plural 			= $("input#posttype-plural").val(); 
				var icon 			= $("input.posttype-menu-icon").val(); 
				var position		= $("input#posttype-position").val(); 
				var slug			= $("input#posttype-slug").val(); 
				
				var title = 0; var editor = 0; var author = 0; var thumbnail = 0;
				var excerpt = 0; var fields = 0; var comments = 0; var revisions = 0; var has_archive = 0;
				var show_in_nav_menus = 0; var exclude_from_search = 0;
				
				if ($("input#posttype-title").is(':checked')) { var title = 1; }
				if ($("input#posttype-editor").is(':checked')) { var editor = 1; }
				if ($("input#posttype-author").is(':checked')) { var author = 1; }
				if ($("input#posttype-thumbnail").is(':checked')) { var thumbnail = 1; }
				if ($("input#posttype-excerpt").is(':checked')) { var excerpt = 1; }
				if ($("input#posttype-fields").is(':checked')) { var fields = 1; }
				if ($("input#posttype-comments").is(':checked')) { var comments = 1; }
				if ($("input#posttype-revisions").is(':checked')) { var revisions = 1; }
				if ($("input#posttype-hierarchical").is(':checked')) { var hierarchical = 1; }
				if ($("input#posttype-page_attributes").is(':checked')) { var page_attributes = 1; }
				if ($("input#posttype-post_formats").is(':checked')) { var post_formats = 1; }
				if ($("input#posttype-has_archive").is(':checked')) { var has_archive = 1; }
				if ($("input#posttype-show_in_nav_menus").is(':checked')) { var show_in_nav_menus = 1; } 
				if ($("input#posttype-exclude_from_search").is(':checked')) { var exclude_from_search = 1; } 
				
				var info = 'update_posttype=' + update_id + '&posttype-name=' + name + 
							'&posttype-singular=' + singular + 
							'&posttype-plural=' + plural + 
							'&posttype-menu-icon=' + icon + 
							'&posttype-title=' + title + 
							'&posttype-editor=' + editor + 
							'&posttype-author=' + author + 
							'&posttype-thumbnail=' + thumbnail + 
							'&posttype-excerpt=' + excerpt + 
							'&posttype-fields=' + fields + 
							'&posttype-comments=' + comments + 
							'&posttype-revisions=' + revisions + 
							'&posttype-hierarchical=' + hierarchical + 
							'&posttype-has_archive=' + has_archive + 
							'&posttype-page_attributes=' + page_attributes + 
							'&posttype-post_formats=' + post_formats + 
							'&posttype-show_in_nav_menus=' + show_in_nav_menus + 
							'&posttype-position=' + position + 
							'&posttype-slug=' + slug + 
							'&posttype-exclude_from_search=' + exclude_from_search;
				
				
				$('#ajax-loader').ajaxStart(function() {
				  $(this).show();				  
				});
				$('#ajax-loader').ajaxStop(function() {
				    $(this).fadeOut();					
				});	
				
				
				$.ajax({
					type: "POST",
					url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
					data: info,
					success: function() {
						window.location = 'admin.php?page=easy-content-types/easy-content-types.php?posttypes';	
					}
				});				
				return false;
			});	
			// check for posttype name on submit
			$('#ecpt-submit').click(function() {
				if($('#ecpt-post-type-name').val() == '') {
					alert('You must enter a post type name');
					return false;
				}
			});

			<?php } if (isset($_GET['page']) && $_GET['page'] == 'easy-content-types/easy-content-types.php?taxonomies') { ?>
			// delete taxonomy function
			$('#ecpt-wrap .ecpt-delete-taxonomy').click(function(){
				var del_id = ''+$(this).attr('id').replace('ecpt-delete-', '');
				var info = 'delete_taxonomy=' + del_id;
				var row_id = '#ecpt-tax-' + del_id;
				if(confirm("Do you you really want to delete this Taxonomy?"))
				{
					
					$(this).siblings('.ajax-loader').ajaxStart(function() {
					  $(this).show();				  
					});
					$(this).siblings('.ajax-loader').ajaxStop(function() {
						$(this).fadeOut();					
					});
				
					$.ajax({
						type: "POST",
						url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
						data: info,
						success: function() {
							$(row_id).fadeOut(200, function(){
								$(row_id).remove();			
							});
							window.location = 'admin.php?page=easy-content-types/easy-content-types.php?taxonomies';	
						}
					});
				}
				return false;
			});
			// check for taxonomy name on submit
			$('#ecpt-submit').click(function() {
				
				if($('#ecpt-taxonomy-name').val() == '') {
					alert('You must enter a taxonomy name');
					return false;
				}
				if(!$('#ecpt-taxonomy-object option:selected').length) {
					alert('You must select at least on taxonomy object');
					return false;
				}
			});
			
			<?php } if (isset($_GET['page']) && $_GET['page'] == 'easy-content-types/easy-content-types.php?metaboxes') { ?>
			// delete metabox function
			$('#ecpt-wrap .ecpt-delete-metabox').click(function(){
				var del_id = ''+$(this).attr('id').replace('ecpt-delete-', '');
				var fields_id = ''+$(this).attr('title').replace('Delete ', '');
				var info = 'delete_metabox=' + del_id + '&delete_meta_fields=' + fields_id;
				var row_id = '#ecpt-metabox-' + del_id;
				if(confirm("Do you you really want to delete this Metabox?"))
				{
				
					$(this).siblings('.ajax-loader').ajaxStart(function() {
					  $(this).show();				  
					});
					$(this).siblings('.ajax-loader').ajaxStop(function() {
						$(this).fadeOut();					
					});
				
					$.ajax({
						type: "POST",
						url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
						data: info,
						success: function() {
							$(row_id).fadeOut(200, function(){
								$(row_id).remove();			
							});
							window.location = 'admin.php?page=easy-content-types/easy-content-types.php?metaboxes';	
						}
					});
				}
				return false;
			});
			// delete field function
			$('#ecpt-wrap .ecpt-delete-field').click(function(){
				var del_id = ''+$(this).attr('id').replace('ecpt-delete-field-', '');
				var info = 'delete_field=' + del_id;
				var row_id = '#recordsArray_' + del_id;
				if(confirm("Do you you really want to delete this Field?"))
				{
				
					$(this).siblings('.ajax-loader').ajaxStart(function() {
					  $(this).show();				  
					});
					$(this).siblings('.ajax-loader').ajaxStop(function() {
						$(this).fadeOut();					
					});
				
					$.ajax({
						type: "POST",
						url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
						data: info,
						success: function() {
							$(row_id).fadeOut(200, function(){
								$(row_id).remove();			
							});
						}
					});
				}
				return false;
			});
			// check for meatbox name on submit
			$('#ecpt-submit').click(function() {
				if($('#ecpt-metabox-name').val() == '') {
					alert('You must enter a meatbox name');
					return false;
				}
			});
			
			// make fields sortable via drag and drop
			$(function() {	
				$(".wp-list-table tbody").sortable({
					handle: '.dragHandle', items: '.ecpt-field', opacity: 0.6, cursor: 'move', axis: 'y', update: function() {
						var order = $(this).sortable("serialize") + '&action=updateRecordsListings';
						$.post("<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php", order, function(theResponse){
							
						});
					}
				});			
			});
			
			<?php } if ($_GET['fields-edit']) { ?>
			// update field function
						
			$('#ecpt-wrap .field-update').click(function(){
				var update_id 	= $(this).attr('id');
				var name 		= $("input#field-name").val(); 
				var type 		= $("select#field-type option:selected").val();
				var description	= $("input#field-desc").val(); 
				var options 	= $("input#field-options").val();  
				var max 		= $("input#field-max").val();  
				var rich_editor;
				if ($("input#rich-editor").is(':checked')) { rich_editor = 1; } else { rich_editor = 0; }
				
				var info = 
					'update_field=' + update_id + 
					'&field-name=' + name + 
					'&field-type=' + type + 
					'&field-desc=' + description +
					'&rich-editor=' + rich_editor + 
					'&field-max=' + max + 
					'&field-options=' + options;
					
				$('#ajax-loader').ajaxStart(function() {
				  $(this).show();				  
				});
				$('#ajax-loader').ajaxStop(function() {
				    $(this).fadeOut();					
				});	
				
				$.ajax({
					type: "POST",
					url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
					data: info,
					success: function() {
						window.location = 'admin.php?page=easy-content-types/easy-content-types.php?metaboxes';	
					}
				});				
				return false;
			});	
			
			// check for field name on submit
			$('#ecpt-submit').click(function() {
				if($('#ecpt-field-name').val() == '') {
					alert('You must enter a field name');
					return false;
				}
			});
			
			// disable options field unless SELECT or RADIO type is chosen
			$('#ecpt-field-type').change(function(){
				var id = $('option:selected', this).attr("id");
				var hiddenFields = '.ecpt-disabled';
				var richEditor = '.ecpt-rich-editor-disabled';
				var max = '.ecpt-max-disabled';
				if(id == 'select' || id == 'radio') {
					$(hiddenFields).fadeIn();
					$(richEditor).fadeOut();
					$(max).fadeOut();
				} else if(id == 'textarea') {
					$(richEditor).fadeIn();
					$(hiddenFields).fadeOut();
					$(max).fadeOut();
				} else if(id == 'slider') {
					$(max).fadeIn();
					$(hiddenFields).fadeOut();
					$(richEditor).fadeOut();
				} else {
					$(hiddenFields).fadeOut();
					$(richEditor).fadeOut();
					$(max).fadeOut();
				}
			});
				<?php if($_GET['edit-field']) { ?>
					// disable options field unless SELECT or RADIO type is chosen
					$('#field-type').change(function(){
						
						var field_id = $('option:selected', this).attr("id");
						if(field_id == 'select' || field_id == 'radio') {
							$('#field-options').fadeTo('slow', 0.5);
							$('#field-options').attr('disabled', 'true');
						} else {
							$('#field-options').fadeTo('fast', 100);
							$('#field-options').attr('disabled', '');
						}
					});				
				<?php } ?>			
			<?php } ?>
			
			<?php if ($_GET['metabox-edit']) { ?>
			// update metabox function
			$('#ecpt-wrap .metabox-update').click(function(){
				var update_id 	= $(this).attr('id');
				var name 		= $("input#metabox-name").val(); 
				var page 		= $("select#metabox-page option:selected").val();
				var context		= $("select#metabox-context option:selected").val();
				var priority	= $("select#metabox-priority option:selected").val();
				var info 		= 'update_metabox=' + update_id + '&metabox-new-name=' + name + '&metabox-page=' + page + '&metabox-context=' + context + '&metabox-priority=' + priority;
				
				$('#ajax-loader').ajaxStart(function() {
				  $(this).show();				  
				});
				$('#ajax-loader').ajaxStop(function() {
				    $(this).fadeOut();					
				});	
				
				$.ajax({
					type: "POST",
					url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
					data: info,
					success: function() {
						window.location = 'admin.php?page=easy-content-types/easy-content-types.php?metaboxes';	
					}
				});				
				return false;
			});
			<?php } ?>
			
			<?php if ($_GET['edit-tax']) { ?>
			// update taxonomy function
			
			$('#ecpt-wrap .tax-update').click(function(){
				var update_id 		= $(this).attr('id');
				var name 			= $("input#tax-name").val(); 
				var singular 		= $("input#tax-singlular").val(); 
				var plural 			= $("input#tax-plural").val();
				var slug 			= $("input#tax-slug").val();
				
				var pages = "";
				$("select#tax-page option:selected").each(function () {
					pages += $(this).val() + ",";
				});
				
				var hierarchical = 0; var tagcloud = 0; var show_in_nav = 0;
				
				if ($("input#tax-hierarchical").is(':checked')) { var hierarchical = 1; }
				if ($("input#tax-tagcloud").is(':checked')) { var tagcloud = 1; }
				if ($("input#tax-show-in-nav").is(':checked')) { var show_in_nav = 1; } 
				var info = 'update_tax=' + update_id + '&tax-name=' + name + '&tax-singular=' + singular + '&tax-plural=' + plural + '&tax-page=' + pages + '&tax-hierarchical=' + hierarchical + '&tax-tagcloud=' + tagcloud + '&tax-show-in-nav=' + show_in_nav + '&tax-slug=' + slug;
				
				$('#ajax-loader').ajaxStart(function() {
				  $(this).show();				  
				});
				$('#ajax-loader').ajaxStop(function() {
				    $(this).fadeOut();					
				});	
				
				$.ajax({
					type: "POST",
					url: "<?php echo $ecpt_base_dir;?>includes/process-ajax-data.php",
					data: info,
					success: function() {
						window.location = 'admin.php?page=easy-content-types/easy-content-types.php?taxonomies';	
					}
				});				
				return false;
			});	
			<?php } ?>

		}); // end jquery(function($))
		//]]> 
	</script>
<?php
}  

// load all the scripts
if (isset($_GET['page']) && 
		(
			$_GET['page'] == 'easy-content-types/easy-content-types.php?posttypes' || 
			$_GET['page'] == 'easy-content-types/easy-content-types.php?taxonomies' ||
			$_GET['page'] == 'easy-content-types/easy-content-types.php?metaboxes' ||
			$_GET['page'] == 'easy-content-types/easy-content-types.php?export' ||
			$_GET['page'] == 'easy-content-types/easy-content-types.php?help' ||
			$_GET['page'] == 'easy-content-types/easy-content-types.php'
		)
	) 
{ 
	add_action('admin_enqueue_scripts', 'ecpt_admin_styles');
	add_action('admin_enqueue_scripts', 'ecpt_admin_scripts');
	if ( get_bloginfo('version') < 3.2 ) {
		add_action('admin_head', 'ecpt_echo_old_admin_scripts');
	} else {
		add_action('admin_head', 'ecpt_echo_admin_scripts');
	}
}

function ecpt_ui_scripts() {

	global $ecpt_options;
?> 
<script type="text/javascript">
	//<![CDATA[
	    jQuery(document).ready(function()
	    {
			
			if(jQuery('.form-table .ecpt_upload_field').length > 0 ) {
				// Media Uploader
				window.formfield = '';

				jQuery('.upload_image_button').live('click', function() {
					window.formfield = jQuery('.ecpt_upload_field',jQuery(this).parent());
					tb_show('', 'media-upload.php?type=image&TB_iframe=true');
					return false;
				});

				window.original_send_to_editor = window.send_to_editor;
				window.send_to_editor = function(html) {
					if (window.formfield) {
						imgurl = jQuery('img',html).<?php if ( get_bloginfo('version') < 3.2 ) { echo "attr('src');"; } else { echo "prop('src');"; } ?>
						window.formfield.val(imgurl);
						tb_remove();
					}
					else {
						window.original_send_to_editor(html);
					}
					window.formfield = '';
					window.imagefield = false;
				}
			}
			if(jQuery('.form-table .ecpt-slider').length > 0 ) {
				jQuery('.ecpt-slider').each(function(){
					var $this = jQuery(this);
					var id = $this.attr('rel');
					var val = jQuery('#' + id).val();
					var max = jQuery('#' + id).attr('rel');
					max = parseInt(max);
					//var step = $('#' + id).closest('input').attr('rel');
					$this.slider({
						value: val,
						max: max,
						step: 1,
						slide: function(event, ui) {
							jQuery('#' + id).val(ui.value);
						}
					});
				});
			}
			
			if(jQuery('.form-table .ecpt_datepicker').length > 0 ) {
				<?php if(!empty($ecpt_options['date_format'])) { ?>
				var dateFormat = '<?php echo $ecpt_options['date_format']; ?>';
				<?php } else { ?>
				var dateFormat = 'mm-dd-yy';
				<?php } ?>				
				jQuery('.ecpt_datepicker').datepicker({dateFormat: dateFormat});
			}
	    });
	//]]>   
  </script>
<?php
}

function ecpt_datepicker_ui_scripts() {
	global $ecpt_base_dir;
	wp_enqueue_script('jquery-ui.min', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', false, '1.8', 'all');		
}
function ecpt_datepicker_ui_styles() {
	global $ecpt_base_dir;
	wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css', false, '1.8', 'all');
}


// these are for newest versions of WP
add_action('admin_print_scripts-post.php', 'ecpt_datepicker_ui_scripts');
add_action('admin_print_scripts-edit.php', 'ecpt_datepicker_ui_scripts');
add_action('admin_print_scripts-post-new.php', 'ecpt_datepicker_ui_scripts');
add_action('admin_print_styles-post.php', 'ecpt_datepicker_ui_styles');
add_action('admin_print_styles-edit.php', 'ecpt_datepicker_ui_styles');
add_action('admin_print_styles-post-new.php', 'ecpt_datepicker_ui_styles');

if ((isset($_GET['post']) && (isset($_GET['action']) && $_GET['action'] == 'edit') ) || (strstr($_SERVER['REQUEST_URI'], 'wp-admin/post-new.php')))
{
	add_action('admin_head', 'ecpt_ui_scripts');
}
?>