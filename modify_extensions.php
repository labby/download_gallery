<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2018 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
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


$DGTEXT = download_gallery::getInstance()->language;
$update_when_modified = true; // Tells script to update when this page was last updated

$section_id = intval($_GET['section_id']);
$page_id = intval($_GET['page_id']);



if (isset($_GET['fileext_id'])) {
	$fileext_id = (int) $_GET['fileext_id'];
}

// Query the file extension
$query_fileext 	= $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_file_ext WHERE fileext_id = '$fileext_id' AND section_id = '$section_id' AND page_id = '$page_id'");
$extdetails 	= $query_fileext->fetchRow();

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $DGTEXT['MOD_TITLE']; ?></title>
		<meta charset="UTF-8">
		<link href="<?php echo THEME_URL; ?>/theme.css" rel="stylesheet" type="text/css" />
		<style type="text/css">
		.modify_section {
			margin-left	: 10px;
			margin-top: 50px;
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
			<h2><?php echo $DGTEXT['MOD_FILE_EXT']; ?></h2>
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
							<input type="button" class="cancel" value="<?php echo $TEXT['CANCEL']; ?>" onclick="window.close(); return false;" style="width: 120px; margin-top: 5px;" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</body>
</html>