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
function bte_bc_footer() {
	$bte_bc_add = get_option('bte_bc_add');
	if (!isset($bte_bc_add)) {
		$bte_bc_add = true;
	}
	if ($bte_bc_add) {
		echo bte_bc_get_html();
	}
}


function bte_bc_get_html() {
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
	$bte_bc_rights = get_option('bte_bc_rights');
	if (!isset($bte_bc_header)) {
		$bte_bc_rights = BTE_BC_RIGHTS;
	}
	$years = $bte_bc_start.'-'.date("Y");
	if ($bte_bc_start>=date("Y")) {
		$years = date("Y");		
	}
	$bte_bc_link = get_option('bte_bc_link');
	if (!isset($bte_bc_link)) {
		$bte_bc_link = true;
	}
	$link = '';
	if ($bte_bc_link) {
		$link = ' <small>-- Copyright notice by <a href="http://www.blogtrafficexchange.com/blog-copyright">Blog Copyright</a></small>';
	}
	
	return stripslashes($bte_bc_header).'&copy; '.stripslashes($years).' <a href="'.stripslashes($bte_bc_url).'">'.stripslashes($bte_bc_org_header.$bte_bc_org.$bte_bc_org_footer).'</a> '.stripslashes($bte_bc_rights.$link.$bte_bc_footer);
}
?>