<?php
/*  Copyright 2008-2009  Blog Traffic Exchange (email : kevin@blogtrafficexchange.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
require_once('BlogCopyright.php');
require_once('BTE_BC_core.php');
function bte_bc_head_admin() {
	wp_enqueue_script('jquery-ui-tabs');
	$home = get_settings('siteurl');
	$base = '/'.end(explode('/', str_replace(array('\\','/BTE_BC_admin.php'),array('/',''),__FILE__)));
	$stylesheet = $home.'/wp-content/plugins' . $base . '/css/blogcopyright.css';
	echo('<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />');
}

function bte_bc_options() {		
	$message = null;
	$message_updated = __("BlogCopyright Options Updated.", 'bte_bc');
	if (!empty($_POST['bte_bc_action'])) {
		$message = $message_updated;
		if (isset($_POST['bte_bc_header'])) {
			update_option('bte_bc_header',$_POST['bte_bc_header']);
		}
		if (isset($_POST['bte_bc_footer'])) {
			update_option('bte_bc_footer',$_POST['bte_bc_footer']);
		}
		if (isset($_POST['bte_bc_start'])) {
			update_option('bte_bc_start',$_POST['bte_bc_start']);
		}
		if (isset($_POST['bte_bc_rights'])) {
			update_option('bte_bc_rights',$_POST['bte_bc_rights']);
		}
		if (isset($_POST['bte_bc_org_header'])) {
			update_option('bte_bc_org_header',$_POST['bte_bc_org_header']);
		}
		if (isset($_POST['bte_bc_org'])) {
			update_option('bte_bc_org',$_POST['bte_bc_org']);
		}
		if (isset($_POST['bte_bc_org_footer'])) {
			update_option('bte_bc_org_footer',$_POST['bte_bc_org_footer']);
		}
		if (isset($_POST['bte_bc_url'])) {
			update_option('bte_bc_url',$_POST['bte_bc_url']);
		}
		if (isset($_POST['bte_bc_add'])) {
			update_option('bte_bc_add',$_POST['bte_bc_add']);
		}
		if (isset($_POST['bte_bc_link'])) {
			update_option('bte_bc_link',$_POST['bte_bc_link']);
		}
		
		print('
			<div id="message" class="updated fade">
				<p>'.__('BlogCopyright Options Updated.', 'bte_bc').'</p>
			</div>');
	}
	$bte_bc_header = get_option('bte_bc_header');
	if (!isset($bte_bc_header)) {
		$bte_bc_header = BTE_BC_HEADER;
	}
	$bte_bc_footer = get_option('bte_bc_footer');
	if (!isset($bte_bc_header)) {
		$bte_bc_footer = BTE_BC_FOOTER;
	}
	$bte_bc_start = get_option('bte_bc_start');
	if (!isset($bte_bc_start)) {
		$bte_bc_start = BTE_BC_START;
	}
	$bte_bc_rights = get_option('bte_bc_rights');
	if (!isset($bte_bc_header)) {
		$bte_bc_rights = BTE_BC_RIGHTS;
	}
	$bte_bc_org_header = get_option('bte_bc_org_header');
	if (!isset($bte_bc_org_header)) {
		$bte_bc_org_header = '<strong>';
	}
	$bte_bc_org = get_option('bte_bc_org');
	if (!isset($bte_bc_org)) {
		$bte_bc_org = get_bloginfo('name');
	}
	$bte_bc_org_footer = get_option('bte_bc_org_footer');
	if (!isset($bte_bc_org_footer)) {
		$bte_bc_org_footer = '</strong>';
	}
	$bte_bc_url = get_option('bte_bc_url');
	if (!isset($bte_bc_url)) {
		$bte_bc_url = get_bloginfo('url');
	}
	$bte_bc_add = get_option('bte_bc_add');
	if (!isset($bte_bc_add)) {
		$bte_bc_add = true;
	}
	$bte_bc_link = get_option('bte_bc_link');
	if (!isset($bte_bc_link)) {
		$bte_bc_link = true;
	}
	
	print('
			<div class="wrap">
				<h2>'.__('Blog Copyright by', 'BlogCopyright').' <a href="http://www.blogtrafficexchange.com">Blog Traffic Exchange</a></h2>
				<form id="bte_bc" name="bte_blogcopyright" action="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?page=BTE_BC_admin.php" method="post">
					<input type="hidden" name="bte_bc_action" value="bte_bc_update_settings" />
					<fieldset class="options">
						<div class="option">
							<label for="bte_bc_header">'.__('Copyright Header: ', 'bte_bc').'</label>
							<input size="80" name="bte_bc_header" type="text" value="'.htmlspecialchars(stripslashes($bte_bc_header)).'" /><br/>
							<label for="bte_bc_start">'.__('Starting Year: ', 'bte_bc').'</label>
							<input size="80" name="bte_bc_start" type="text" value="'.htmlspecialchars(stripslashes($bte_bc_start)).'" /><br/>
							<label for="bte_bc_org_header">'.__('Org Header: ', 'bte_bc').'</label>
							<input size="80" name="bte_bc_org_header" type="text" value="'.htmlspecialchars(stripslashes($bte_bc_org_header)).'" /><br/>
							<label for="bte_bc_org">'.__('Organizaion: ', 'bte_bc').'</label>
							<input size="80" name="bte_bc_org" type="text" value="'.htmlspecialchars(stripslashes($bte_bc_org)).'" /><br/>
							<label for="bte_bc_org_footer">'.__('Org Footer: ', 'bte_bc').'</label>
							<input size="80" name="bte_bc_org_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_bc_org_footer)).'" /><br/>
							<label for="bte_bc_url">'.__('Org URL: ', 'bte_bc').'</label>
							<input size="80" name="bte_bc_url" type="text" value="'.htmlspecialchars(stripslashes($bte_bc_url)).'" /><br/>
							<label for="bte_bc_rights">'.__('Rights Reserved: ', 'bte_bc').'</label>
							<input size="80" name="bte_bc_rights" type="text" value="'.htmlspecialchars(stripslashes($bte_bc_rights)).'" /><br/>
							<label for="bte_bc_footer">'.__('Copyright Footer: ', 'bte_bc').'</label>
							<input size="80" name="bte_bc_footer" type="text" value="'.htmlspecialchars(stripslashes($bte_bc_footer)).'" /><br/>
						</div>
						<div class="option">
							<p>Copyright statement will look like this...</p>'.bte_bc_get_html().'
						</div>
						<div class="option">
							<label for="bte_bc_add">'.__('Automaticaly add to footer (your theme must have a call to "wp_footer()" otherwise you will have to place manually): ', 'bte_bc').'</label>
							<select name="bte_bc_add" id="bte_rs_add">
									<option value="0" '.bte_bc_optionselected(0,$bte_bc_add).'>'.__('No', 'bte_bc').'</option>
									<option value="1" '.bte_bc_optionselected(1,$bte_bc_add).'>'.__('Yes', 'bte_bc').'</option>
							</select>
						</div>
						<div class="option">
							<p>To manually place the copyright notice place this code within your theme (typically in the footer).</p>
							<p><strong><code>&lt;?php if (function_exists(\'bte_bc_tag\')) { bte_bc_tag(); } ?&gt;</code></strong></p>
						</div>
						<div class="option">
							<label for="bte_bc_link">'.__('Give BlogCopyright credit with link?: ', 'bte_bc').'</label>
							<select name="bte_bc_link" id="bte_bc_link">
									<option value="0" '.bte_bc_optionselected(0,$bte_bc_link).'>'.__('No', 'bte_bc').'</option>
									<option value="1" '.bte_bc_optionselected(1,$bte_bc_link).'>'.__('Yes', 'bte_bc').'</option>
							</select>
						</div>
					</fieldset>
					<p class="submit">
						<input type="submit" name="submit" value="'.__('Update Blog Copyright Options', 'bte_bc').'" />
					</p>
						<div class="option">
							<h4>Other Blog Traffic Exchange <a href="http://www.blogtrafficexchange.com/wordpress-plugins/">Wordpress Plugins</a></h4>
							<ul>
							<li><a href="http://www.blogtrafficexchange.com/wordpress-backup/">Wordpress Backup</a></li>
							<li><a href="http://www.blogtrafficexchange.com/blog-copyright/">Blog Copyright</a></li>
							<li><a href="http://www.blogtrafficexchange.com/old-post-promoter/">Old Post Promoter</a></li>
							<li><a href="http://www.blogtrafficexchange.com/related-websites/">Related Websites</a></li>
							<li><a href="http://www.blogtrafficexchange.com/related-posts/">Related Posts</a></li>
							</ul>
						</div>
				</form>' );

}

function bte_bc_optionselected($opValue, $value) {
	if($opValue==$value) {
		return 'selected="selected"';
	}
	return '';
}

function bte_bc_options_setup() {	
	add_options_page('BlogCopyright', 'Blog Copyright', 10, basename(__FILE__), 'bte_bc_options');
}

?>