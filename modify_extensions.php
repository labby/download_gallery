<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, erpe
 *  @copyright		2010-2015 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, erpe
 *  @license		GNU General Public License
 *  @license terms	see info.php of this module
 *  @platform		see info.php of this module
 *
 */

// include class.secure.php to protect this file and the whole CMS!
if ( defined( 'LEPTON_PATH' ) )
{
    include( LEPTON_PATH . '/framework/class.secure.php' );
} 
else
{
    $oneback = "../";
    $root    = $oneback;
    $level   = 1;
    while ( ( $level < 10 ) && ( !file_exists( $root . '/framework/class.secure.php' ) ) )
    {
        $root .= $oneback;
        $level += 1;
    } 
    if ( file_exists( $root . '/framework/class.secure.php' ) )
    {
        include( $root . '/framework/class.secure.php' );
    } 
    else
    {
        trigger_error( sprintf( "[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER[ 'SCRIPT_NAME' ] ), E_USER_ERROR );
    }
}
// end include class.secure.php

// check if this file was invoked by the expected module file
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if ($referer && strpos($referer, LEPTON_URL . '/modules/download_gallery/modify_settings.php') === false) {
	die(header('Location: ../../index.php'));
}

// include the admin wrapper script
$update_when_modified = true; // Tells script to update when this page was last updated
require(LEPTON_PATH . '/modules/admin.php');
$admin = new admin('Pages', '', false, false);

// Load Language file
if(LANGUAGE_LOADED) {
	if(!file_exists(LEPTON_PATH.'/modules/download_gallery/languages/'.LANGUAGE.'.php')) {
		require_once(LEPTON_PATH.'/modules/download_gallery/languages/EN.php');
	} else {
		require_once(LEPTON_PATH.'/modules/download_gallery/languages/'.LANGUAGE.'.php');
	}
}

require(LEPTON_PATH.'/framework/summary.functions.php');

if (isset($_GET['fileext_id'])) {
	$fileext_id = (int) $_GET['fileext_id'];
}

// Query the file extension
$query_fileext 	= $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_file_ext WHERE fileext_id = '$fileext_id' AND section_id = '$section_id' AND page_id = '$page_id'");
$extdetails 	= $query_fileext->fetchRow();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo $DGTEXT['MOD_TITLE']; ?></title>
		<link href="<?php echo LEPTON_URL; ?>/admin/interface/stylesheet.css" rel="stylesheet" type="text/css" />
		<style type="text/css">
		.modify_section {
			margin-left	: 10px;
		}
		.modify_section h1 {
			text-transform	: none;
			text-align		: left;	
			color			: white;
		}
		</style>
		<script language="JavaScript"  type="text/javascript">
		function validateForm(theForm) {
			
		var checkOK		= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!?$#+@, <>";
		var checkStr	= theForm.file_ext.value;
		var allValid	= true;
		
		for (i = 0;  i < checkStr.length;  i++) {
			ch = checkStr.charAt(i);
			for (j = 0;  j < checkOK.length;  j++)
				if (ch == checkOK.charAt(j))
				break;
				if (j == checkOK.length) {
					allValid = false;
					break;
				}
			}
		
			if (!allValid) {
				alert("Please enter only letter and numeric characters in the \"File Extensions\" field.\n\nThese extensions should be seperated with a comma.");
				theForm.file_ext.focus();
				return false;
			}
		}
		</script>
	</head>
	
	<body>
		<div class="modify_section">
			<h1><?php echo $DGTEXT['MOD_FILE_EXT']; ?></h1>
			<p><?php echo $DGTEXT['MOD_TXT']; ?></p>
			
			<form name="modify_file_ext" method="post" action="<?php echo LEPTON_URL; ?>/modules/download_gallery/save_extsettings.php" onsubmit="return validateForm(this);" >
				<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
				<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
				<input type="hidden" name="fileext_id" value="<?php echo $extdetails['fileext_id']; ?>" />
				<table cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td width="150"><?php echo $DGTEXT['FILE_TYPE']; ?>:</td>
						<td><strong><?php echo $extdetails['file_type']; ?></strong></td>
					</tr>
					<tr>
						<td><?php echo $DGTEXT['FILE_TYPE_EXT']; ?>:</td>
					</tr>
					<tr>
						<td colspan="2">
								<textarea name="file_ext" style="width: 96%; height: 100px;"><?php echo str_replace(",",", ", $extdetails['extensions']); ?></textarea>
						</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" width="100%">
					<tr>
						<td align="center">
							<input name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 120px; margin-top: 5px;" /> &nbsp;
							<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="window.close(); return false;" style="width: 120px; margin-top: 5px;" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>