<?php
/*
 Website Baker Project <http://www.websitebaker.org/>
 Copyright (C) 2004-2006, Ryan Djurovich

 Website Baker is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Website Baker is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Website Baker; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die(header('Location: index.php'));

// obtain module directory
$mod_dir = basename(dirname(__FILE__));

// include the module language file depending on the backend language of the current user
@include_once(WB_PATH . '/framework/module.functions.php');
if (!@include(get_module_language_file($mod_dir))) return;

// STEP 1:	get the Settings for this Section
$section_id = SECTION_ID;
$page_id = PAGE_ID;
$query_settings = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_settings WHERE section_id = '$section_id'");
$settings = $query_settings->fetchRow();
$use_captcha = $settings['use_captcha']; 

if($settings['userupload'] == 0 || ($settings['userupload'] == 2 && (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ID'] == ""))) {
	exit(header('Location: ../index.php'));
}

// include template parser class and set template
require_once(WB_PATH . '/include/phplib/template.inc');
$tpl = new Template(dirname(__FILE__) . '/');
// define how to handle unknown variables (default:='remove', during development use 'keep' or 'comment')
$tpl->set_unknowns('keep');

// define debug mode (default:=0 (disabled), 1:=variable assignments, 2:=calls to get variable, 4:=show internals)
$tpl->debug = 0;

$tpl->set_file('page', 'userupload.htt');
$tpl->set_block('page', 'main_block', 'main');

// list possible group types
$tpl->set_block('main_block', 'group_block', 'group_loop');
$query = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_groups WHERE section_id = '$section_id' ORDER BY position ASC");
if($query->numRows() > 0) {
	// Loop through groups
	while($group = $query->fetchRow()) {
		$tpl->set_var(
			array(
				'GROUP_VAL' 	=> $group['group_id'],
				'GROUP_TITLE' 	=> $group['title']
			)
		);
		$tpl->parse('group_loop', 'group_block', true);
	}
}

//$tpl->set_block('page', 'main_block', 'main');

$tpl->set_var(
	array(
		// variables from Website Baker framework
		'PAGE_ID'		=> (int) $page_id,
		'SECTION_ID'	=> (int) $section_id,
		'WB_URL'		=> WB_URL,
		'TXT_FILE'		=> $TEXT['FILE'],
		'TXT_TITLE'		=> $TEXT['TITLE'],
		'TXT_GROUP'		=> $TEXT['GROUP'],
		'TXT_NONE'		=> $TEXT['NONE'],
		'TXT_DESCR'		=> $TEXT['DESCRIPTION'],
		'TXT_RESET'		=> $TEXT['RESET'],

		// module settings
		'MODULE_DIR'    => $mod_dir,
		'TXT_UPLOAD'	=> $DGTEXT['UPLOADFILE']
	)
);

if($use_captcha) {
	$_SESSION['captcha'] = '';
	for($i = 0; $i < 5; $i++) {
		$_SESSION['captcha'] .= rand(0,9);
	}
	$tpl->set_var('TXT_CAPTCHA1', $TEXT['VERIFICATION'].":");
	$tpl->set_var('TXT_CAPTCHA2', '<img src="' . WB_URL. '/include/captcha.php?' . time(). '" alt="Captcha" /> <input class="captcha" type="text" name="captcha" maxlength="5" />');
} else {
	$tpl->set_var('TXT_CAPTCHA1', '&nbsp;');
	$tpl->set_var('TXT_CAPTCHA2', '&nbsp;');
}

// Parse template objects output
$tpl->parse('main', 'main_block', false);
$tpl->pparse('output', 'page', false, false);

?>