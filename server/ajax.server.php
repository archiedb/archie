<?php
/* vim:set tabstop=8 softtabstop=8 shiftwidth=8 noexpandtab: */
/**
 * Ajax Server
 *
 *
 * LICENSE: GNU General Public License, version 2 (GPLv2)
 * Copyright (c) 2001 - 2011 Ampache.org All Rights Reserved
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License v2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @package	Ampache
 * @copyright	2001 - 2011 Ampache.org
 * @license	http://opensource.org/licenses/gpl-2.0 GPLv2
 * @link	http://www.ampache.org/
 */

/* Because this is accessed via Ajax we are going to allow the session_id
 * as part of the get request
 */

// Set that this is an ajax include
define('AJAX_INCLUDE','1');

require_once '../class/init.php';

/* Set the correct headers */
header("Content-type: text/xml; charset=UTF-8");
header("Content-Disposition: attachment; filename=ajax.xml");
header("Expires: Tuesday, 27 Mar 1984 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
	
$results = array(); 

switch ($_REQUEST['action']) {
	case 'show_class': 
		if ($_POST['material']) { 
			$classes = Classification::get_from_material($_POST['material']); 
		} 
		else { 
			$classes = Classification::get_all(); 
		} 
		ob_start(); 
		//FIXME: This should be done in a more sane matter
		require_once Config::get('prefix') . '/template/show_class.inc.php'; 
		$results['classification_select'] = ob_get_contents(); 
		ob_end_clean(); 
	break;
	default:
		$results['rfc3514'] = '0x1';
	break;
} // end switch action

// Go ahead and do the echo
echo xml_from_array($results);

?>
