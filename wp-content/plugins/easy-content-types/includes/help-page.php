<?php

function ecpt_help_page() {

	?>
	<div class="wrap">
		<div id="ecpt-wrap" class="ecpt-help-page">
			<h2>Easy Custom Content Types Help</h2>
			<p>Index</p>
			<ul>
				<li><a href="#posttypes">Post Types</a>
					<ul>
						<li><a href="#creating-post-types">Creating Post Types</a></li>
						<li><a href="#post-type-labels">Post Type Labels</a></li>
						<li><a href="#post-type-options">Post Type Options</a></li>
						<li><a href="#post-type-support-options">Post Type Support Options</a></li>
						<li><a href="#post-type-advanced">Post Type Advanced Options</a></li>
						<li><a href="#editing-post-types">Editing Post Types</a></li>
						<li><a href="#theme-template-files">Theme Template Files</a></li>
						<li><a href="#template-hierarchy">Template Hierarchy</a></li>
						<li><a href="#showing-posttypes">Querying Post Types</a></li>
					</ul>				
				</li>
				<li><a href="#taxonomies">Taxonomies</a>
					<ul>
						<li><a href="#creating-taxonomies">Creating Taxonomies</a></li>
						<li><a href="#taxonomy-objects">Taxonomy Objects</a></li>
						<li><a href="#taxonomy-options">Taxonomy Options</a></li>
						<li><a href="#editing-taxonomies">Editing Taxonomies</a></li>
						<li><a href="#taxonomy-info">Displaying Taxonomy Info</a></li>
						<li><a href="#taxonomy-posts">Displaying Posts with a Taxonomy</a></li>
						<li><a href="#taxonomy-defaults">Adding Default Category and Post Tags to Custom Post Types</a></li>
					</ul>				
				</li>
				<li><a href="#metaboxes">Meta Boxes</a>
					<ul>
						<li><a href="#creating-metaboxes">Creating Meta Boxes</a></li>
						<li><a href="#metabox-locations">Selecting Meta Box Location</a></li>
						<li><a href="#editing-metaboxes">Editing Meta Boxes</a></li>
						<li><a href="#metabox-fields">Adding / Editing Fields to Meta Box</a></li>
						<li><a href="#field-types">Field Types</a></li>
						<li><a href="#field-info">Displaying Field Info in Your template</a></li>
					</ul>
				</li>
				<li><a href="#access">User Access</a>
			</ul>
			
			<!------------------------
			end Index
			------------------------->
			
			<h3 id="posttypes">Post Types</h3>
				<div class="ecpt_section">
				<p id="creating-post-types" class="ecpt_title"><strong>Creating Post Types</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
					<p>To create a new custom post type, first click on "Post Types" from the "Content Types" menu. You will now be presented with a screen displaying a list of all of the currently registered custom post types (through this plugin only), as well as a form to create a new post type.</p>
					
					<p>Fill in each of the fields, and select the options you want for your post type, then click "Add Post Type". The screen will refresh and you will see your newly added post type in the list, as well as a menu item in the left WordPress navigation.</p>
					
					<p>The only required field is "Post Type Name". Whatever you enter in this field will be used as the main post type name, meaning that this is the name that will use to query the post type from the database. This field will also be used for the Singular and Plural labels, if these fields are left empty.</p>
					
					<p><em>Note</em>, whatever you enter into the Post Type Name field is automatically lowercased and all spaces removed. So, if you enter "My Post Type", it would be saved as "myposttype", and this is the value you'd use to query the database.</p>
					
					<p><em>TIP:</em> Every field has a blue question mark to the right of it. Click these for hints if you are confused.</p>
				</div>
				
				<p id="post-type-labels" class="ecpt_title"><strong>Post Type Labels</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>The post type labels are the names for the post type that are displayed through the interface to the user. The Singular Name refers to single items of the post type, and, obviously, the Plural Name refers to plural items of the post type. So, for example, if you have a post type called Books, then the singular label would be displayed as "Book", and the plural label would be "Books". <em>Note</em>, the plurality is not automatically determined from the Post Type Name field. The values you enter in these two fields are what will be used.</p>
					
					<p>This two fields both accept spaces and capitalization.</p>
				</div>
				
				<p id="post-type-options" class="ecpt_title"><strong>Post Type Options</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p><em>Hierarchical</em>: This option controls whether or not a post type is hierarchical, meaning that parent and child items can be specified. This is how the default WordPress Pages behave.</p>
					<p><em>Enable Arhcives</em>: This option controls whether post type archives, monthly, yearly, categorical, can be displayed. If this option is disabled, you will be unable view archives of this post type.</p>
					<p><em>Post Formats</em>: This option controls whether <a href="http://codex.wordpress.org/Post_Formats">Post Formats</a> are enabled for this post type.</p>
					<p><em>Exclude From Search</em>: This option, if enabled, will prevent a post type from being searchable, through your websites search form. Items are still searchable in the admin.</p>
					<p><em>Show in Nav Menus</em>: This option, when enabled, when cause a post type, and all of it's published items, to show up in the WP navigation menu interface, meaning that you can add individual items to your navigation menus.</p>
					<p><em>Menu Icon</em>: This is the image displayed next to your post type name in the main WordPress admin menu. If no image is specified, the default thumb tack image will be used.</p>
				</div>
				
				<p id="post-type-support-options" class="ecpt_title"><strong>Post Type Support Options</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p><em>Title</em>: This option will make the Title field available for a post type in the main post editor.</p>
					<p><em>Editor</em>: This option will enable the main content editor for a post type, including the Visual / HTML editors, all media upload buttons, and all formatting options.</p>
					<p><em>Author</em>: This option will enable the drop-down author selection for a post type.</p>
					<p><em>Thumbnail</em>: This option will allow the Featured Post Thumbnail for a post type.</p>
					<p><em>Excerpt</em>: This option will enable the hand-crafted, custom excerpt box for a post type.</p>
					<p><em>Custom Fields</em>: This option will enable the use of custom fields for a post type.</p>
					<p><em>Comments</em>: This option will enable the Comments ON / OFF feature for a post type, allowing you to specify whether comments should be allowed for a particular item.</p>
					<p><em>Revisions</em>: This option will enable automatic item revisions, allowing you to revert back to previous verions of an item if need be.</p>
				</div>
				
				<p id="post-type-advanced" class="ecpt_title"><strong>Post Type Advanced Options</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p><em>Menu Position</em>: This is the position in the left menu bar that your post type page will be placed. Choose from a number below and enter it in the field. </p>
						<div style="padding-left: 30px;">
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
							100 - below second separator
						</div>
						
					<p><em>Slug</em>: The post type slug allows you to define a custom permalink for your post type. The permalink is what shows up in the URL before the post type entry name. For example, you have a post type named Books and you are viewing the Books entry named My Great Book; the url to this book will look something like this: http://yousite.com/books/my-great-book. Note that by default, ECPT will use a lowercased (and spaces removed) version of the post type name as the slug.</p>

					<p>If you wanted to change the /books/ part of the url above, you'd enter your desired value in the Slug field. So, for example, if you entered "timeless-books", the url to My Great Book would be http://yoursite.com/timless-books/my-great-book.</p>
					
					<p>The slug should be URL friendly, and so no spaces or capitalization is allowed. If you try to enter "Timeless Books" as your slug, it will be saved as "timeless-books".</p>
					
				</div>
				
				<p id="editing-post-types" class="ecpt_title"><strong>Editing Post Types</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>Once a post type is created, it may be edited at any time by first clicking "Post Types" in the "Content Types" menu, then clicking "Edit" on the post type you wish to modify. The Edit link in towards the right side in the "Edit" column.</p>
					
					<p>When you click "Edit", you will be taken to a new screen displaying only the information pertaining to the particular post type you are editing. Once you have made your changes, click Update and you will be redirected back to the main Post Types screen.</p>
					
					<p>If you wish to cancel editing a post type, without saving, you can click the "Go Back" link at the top, and you will be returned to the main Post Types screen without making any changes to your post type.</p>
				</div>
				
				<p id="theme-template-files" class="ecpt_title"><strong>Theme Template Files</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>In the "Settings" page, there is an option to automatically create theme template files. If these options are enabled, template files specific to the post type will be created when post types are created. For example, if you create a post type called "Books", then a template file called <em>single-books.php</em> will be created in your theme directory. Another template file called <em>archive-books.php</em> will also be created, and it will be used for archive displays of this post type.</p>
				</div>
				
				<p id="template-hierarchy" class="ecpt_title"><strong>Template Hierarchy</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>The template hierarchy refers to which theme file is used in certain circumstances, and which files act as backups if those files do not exist.</p>
					
					<p>If the automatic template creation options are enabled, then those created files will always be used when displaying content from the post types.</p>
					
					<p>For Single post type items, the first template that WordPress will try to use is <em>single-{post-type-name}.php</em>. If that file does not exist, then WordPress will use <em>single.php</em>. And if that doesn't exist, then WordPress will use <em>index.php</em>.</p>
					
					<p>For Archives, the first template that WordPress will try to use is <em>archive-{post-type-name}.php</em>. If that file does not exist, then WordPress will use <em>archive.php</em>. And if that doesn't exist, then WordPress will use <em>index.php</em>.</p>
				</div>
				
				<p id="showing-posttypes" class="ecpt_title"><strong>Querying Post Types</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
					<div class="ecpt_section">
						<p>If you want to display published items from a certain post type, you can use query_posts() like this:
						
						<pre>query_posts( array( 'post_type' => 'post_type_name', 'showposts' => 10 ) ); </pre>
						</p>
					</div>
			<h3 id="taxonomies">Taxonomies</h3>
				<p id="creating-taxonomies" class="ecpt_title"><strong>Creating Taxonomies</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>First, click "Taxonomies" in the "Content Types" menu, then fill in all of the form fields and click "Add Taxonomy". The screen will refresh and your new taxonomy will be display in the custom taxonomy table at the top of the screen.</p>
					
					<p>There are two required files: Taxonomy Name and Object.</p>
					
					<p>The value that you enter in Taxonomy Name will be used as the main name of the taxonomy, meaning that this is what will be used to query the taxonomy from the database. Any value you enter here, will be lowercased and all spaces removed. So, for example, if you enter "Music Genres", then the name will be converte to "musicgenres".</p>
					
					<p><em>Labels</em>: The Singular Label is the name that will be used to refer to single taxonomy terms. The Plural Label is the name that will be used to refer to plural taxonomy terms. So, for example, if you have a taxonomy name "Genres", then the singular label might be "Genre", and the plural label might be "Genres".</p>
				</div>
				
				<p id="taxonomy-objects" class="ecpt_title"><strong>Taxonomy Objects</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>The Taxonomy Objects are the post types that will receive the custom taxonomy. So, for example, if you want to apply your taxonomy to the regular WordPress Page and also a custom post type called "Books", then you'd choose "page" and "books" when creating your taxonomy.</p>
					
					<p>You may choose as many objects as you wish, but must choose at least one.</p>
				</div>
				
				<p id="taxonomy-options" class="ecpt_title"><strong>Taxonomy Options</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p><em>Hierarchical</em> - This option will enable archives for a custom taxonomy, allowing you to place your taxonomies into tiers with child and parent terms.</p>
					
					<p><em>Show Tag Cloud</em> - This option will allow the terms (similar to post tags) of your taxonomy to be displayed in a "tag cloud" format, meaning that you can display links to the term archives and also display the most popular terms.</p>
					
					<p><em>Show in Nav Menus</em> - This option will allow terms of this taxonomy to appear in the custom WP nav menus.</p>
				</div>
				
				<p id="editing-taxonomies" class="ecpt_title"><strong>Editing Taxonomies</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>Once you have created a custom taxonomy, you can edit the taxonomy at any time. To do this, first click on the "Taxonomies" link from the "Content Types" menu, then click "Edit" on the taxonomy you wish to edit. The edit link is towards the right side of the screen on the Taxonomies screen.</p>
					
					<p>When you have clicked "Edit", the information for the particular taxonomy you are editing will be displayed. Once you have modified all of the necessary information, click Update and your changes will be saved. Then you will be redirected back to the main taxonomy page.</p>
					
					<p>If you wish to exit from the edit screen without saving any changes, click the "Go Back" link at the top of the screen.</p>
				</div>
				
				<p id="taxonomy-info" class="ecpt_title"><strong>Displaying Taxonomy Info</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p><em>List of Terms Attached to Current Post</em> - If you want to display a list of all the terms of a custom taxonomy attached to the currently view post (or custom post type), you can use this:
						<pre>echo get_the_term_list( $post->ID, $taxonomy, $before, $sep, $after );</pre><br/>
						<ul>
							<li><em>$post->ID</em> - the ID of the post you wish to display. Use $post-> for current post</li>
							<li><em>$taxonomy</em> - the name of the taxonomy you wish to show terms from</li>
							<li><em>$before</em> - text to show before the list of terms</li>
							<li><em>$sep</em> - text or HTML tag to use to separate the terms inside the list</li>
							<li><em>$after</em> - text or show after the list of terms</li>
						</ul>
						So, for example, if your taxonomy was named "Genres", you would do something like this:
						<pre>echo get_the_term_list( $post->ID, 'genres', 'Genres: ', ' ', '' );</pre><br/>
						which would display somthing like this:<br/>
						<pre>Genres: Genre1, Genre2 . . . </pre>
						Each genre would be linked to an archive of all posts with the same genre.
						
					</p>
				</div>
				
				<p id="taxonomy-posts" class="ecpt_title"><strong>Displaying Posts with a Taxonomy</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
					<div class="ecpt_section">
						<p>If you want to display a list of posts that have a particular taxonomy, then you can use a query_posts() like this:
						
						<pre>query_posts( array( 'genres' => 'hard-rock', 'showposts' => 10 ) ); </pre>
						
						<p>The query above will just display posts that have the "hard-rock" term, but if you'd like to get more advanced and learn how to pull posts that meet several criteria, perhaps in the "hard-rock" genre, have a "year" of 1990, and a "country" of "America", you can use the WordPress 3.1 <em>tax_query</em> feature. This is advanced feature, so I will let you read my tutorial at <a href="http://www.wpmods.com/query-multiple-taxonomies-in-wp-3-1">WP Mods</a> if you are interested.
						
					</div>
				
				<p id="taxonomy-defaults" class="ecpt_title"><strong>Adding Default Category and Post Tags to Custom Post Types</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
					<div class="ecpt_section">
						<p>To enable the default Category and / or Post Tags on a custom post type all you need to do is create a new custom taxonomy with the same name as the default taxonomies.</p>
						
						<p>So, for example, if you wanted to add the default categories to your Books post type, you would first create a new custom taxonomy named "category", then attach it to the Books post type.</p>
						
						<p>If you wanted to add the default Post Tags to your Books post type, you would create a new custom taxonomy called "post_tag", then attach it to the Books post type.</p>
						
						<p>Note: the taxonomy labels can be anything you want, but the taxonomy names must be "category" and "post_tag". If you change either of the names, they will not work.</p>
						
						<p>Once you have created the new taxonomies, all already created categories and tags will be available for your custom post type.</p>
						
					</div>
				
			<h3 id="metaboxes">Meta Boxes</h3>
				<p id="creating-metaboxes" class="ecpt_title"><strong>Creating Meta Boxes</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>To create a custom metabox, first click on "Meta Boxes" in the main "Content Types" menu. All already created meta boxes will be displayed in the table at the top of the page.</p>
					
					<p>Adding a new metabox is simple, just fill out the "Create New Custom Metabox" form and click "Add Meta Box".</p>
					
					<p>The metabox name is the only required field and the value you enter here is what will be displayed in the header of the metabox, on the editor screen.</p>
				</div>
				
				<p id="metabox-locations" class="ecpt_title"><strong>Selecting Meta Box Location</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>Their are several locations where you can place your custom metabox on the editor screen. The first option is the "Page". This determines which post type page the metabox appears one. For example, if you choose "post", then your metabox appear on the regular WordPress Post editor screens. If you choose, say "Books", then your metabox will appear on the custom post type called "books".</p>
					
					<p>The second level of placement control is the "Context" option. This determines where on the screen the metabox appears.
						<ul>
							<li>normal - in the left, main column of the post editor</li>
							<li>advanced - in the left, main column of the post editor above any "normal" metaboxes</li>
							<li>side - in the right, side column of the post editor</li>
						</ul>
					</p>
					
					<p>The third level of placement control is the "Priority" option. This determines how "high" on the screen the metabox should appear. The priority hierarchy goes like this:
					<ol>
						<li>high</li>
						<li>core</li>
						<li>default</li>
						<li>low</li>
					</ol>
				
				<p id="editing-metaboxes" class="ecpt_title"><strong>Editing Meta Boxes</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				
					<p>To edit a metaox you have created, first click "Meta Boxes" from the main "Content Types" menu, then click "Edit" towards the right side of the screen for the particular meta box you wish to modify. Once you do this, you will be directed to a screen displaying all the options for this meta box. When you have made your changes, click "Update". You will now be redirected back to the main Meta Box screen and all your changes will be saved.</p>
					
					<p>If you wish to cancel editing a meta box without saving any changes, simply click the "Go Back" link at the top of the screen. You will be redirected to the main meta box screen without making any changes.</p>
				</div>
				
				<p id="metabox-fields" class="ecpt_title"><strong>Adding / Editing Fields to Meta Box</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p><em>Adding Fields</em> - Every meta box can have its own unique fields. To add fields to a meta box, click "Edit Fields" towards the right side of the screen. You will now be taken to a screen displaying all the fields this meta box currently contains, if any. To add a new field, fill in the "Field Name" input, select the type of field you'd like to add, and click "Add Field". Your screen will refresh and you will see the new field in the list of fields. You may add as many fields as you'd like.</p>
					
					<p>If you choose either "Radio" or "Select" as your field type, a new "Options" input will become available. Put all of the available options you'd like your field to have here, each separated by a comma.</p>
					
					<p><em>Editing Fields</em> - If you wish to change the name, type, or options of a field, simply click the "Edit" link towards the right side of the screen. You will now be able to make any changes you wish. Once complete, click "Update" to save your changes, or click "Go Back" to cancel.</p>
					
					<p>If you wish to change the order of your fields (the order they are displayed on your screen here is the order they will be displayed in the meta box), you may simply drag and drop your fields into the desireable order using the cross hair icon.</p>
					
					<p><em>Deleting Fields</em> - To delete a field, click the "Delete" link towards the right of the screen for the field you wish to remove. The field will disappear. There is no need to refresh.</p>
				</div>
				
				<p id="field-types" class="ecpt_title"><strong>Field Types</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>There are six available field types to choose from:
						<ol>
							<li><em>text</em> - a plain, single line text input</li>
							<li><em>textarea</em> - a large, multiline text input with formatting controls</li>
							<li><em>select</em> - a drop down options menu</li>
							<li><em>checkbox</em> - a single true / false checkbox</li>
							<li><em>radio</em> - a group of radio options</li>
							<li><em>date</em> - a plain, single line text input with drop down date picker</li>
							<li><em>upload</em> - a plain, single line text input with an upload button that allows you to insert an image or file url through the WordPress media uploader</li>
							<li><em>slider</em> - a jQuery UI slider that you can use to adjust the numerical value of a field with a nice draggable slider</li>
						</ol>
						<strong>NOTE: If your metabox contains a textarea, you should NOT give it a "side" context due to space constraints.</strong>
				</div>
				<p id="field-info" class="ecpt_title"><strong>Displaying Field Info in Your template</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>By default, WordPress will never automatically display any of the data entered into custom meta boxes. For that reason, I have provided several easy ways for your to display the information yourself.</p>
				
					<p><em>Using Shortcodes</em>:</p>
					
					<p>The information from any and all fields can be displayed in a post or page using a shortcode, like this:
						<pre>[ecpt_field id="fieldname"]</pre><br/>
					So, if your field name was "contact", you'd use:<br/>
						<pre>[ecpt_field id="contact"]</pre><br/>
					</p>
					
					<p>With version 1.3+, there are also two additional parameters for shortcodes:
						<ol>
						<li><em>image</em> - This allows you to choose whether to display the image (for upload fields only), or to simply return the url of the image. By setting <em>image=true</em>, WordPress will display the image.<br/>Default: false</li>
						<li><em>url</em> - This allows you to set a clickable url for the content of the field, either image or text. To define a url, use <em>url="http://yoursite.com"</em></li>
						</ol>
						So, for example, to display an image and make it clickable, your shortcode format will look like this:<br/>
						<pre>[ecpt_field id="image" url="http://google.com" image=true]</pre>
					</p>
					
					<p><em>Using Template Tags</em>:</p>
					
					<p>You can also display the information in your theme's template files using template tags, like this:
					
					<pre>echo get_post_meta($post->ID, 'ecpt_fieldname', true);</pre><br/>
					
					<em>fieldname</em> is replaced with the name of your field. So, if your field is named "Contact", you would use:
					<pre>echo get_post_meta($post->ID, 'ecpt_contact', true);</pre><br/></p>
			
					<p><em>Note</em>, names with more than one word or uppercased are automatically converted to single words and lowercased. For example, <em>Test Field</em> becomes <em>testfield</em></p>
				</div>
			<h3 id="access">Meta Boxes</h3>
				<p id="user-access" class="ecpt_title"><strong>User Level Access</strong> - <a href="#ecpt-wrap">Back to Top</a></p>
				<div class="ecpt_section">
					<p>
						Inside of the "Settings" page, from the main "Content Types" menu, you may control what user levels have access to the functions provided by this plugin.
					</p>
					
					<p>There are three different user levels:
						<ol>
							<li>Admin</li>
							<li>Editor</li>
							<li>Author</li>
						</ol>
						These user levels correspond to the default user roles assigned to registered WordPress users.</p>
						
					<p>By adjusting the user levels, you can control which user levels have the ability to:
						<ul>
							<li>Access any menu of the plugin</li>
							<li>Create custom Post Types</li>
							<li>Create custom Taxonomies</li>
							<li>Create custom Meta Boxes</li>
						</ul>
					</p>
					<p><em>Note:</em> - The user level given to the custom content menu is considered the "master" user level. Only users that have the same user level as set for the custom content menu will be able to access any of the lower menus.</p>
				</div>
		</div><!--end ecpt-wrap-->
	<div><!--end wrap-->
	<?php

}

?>