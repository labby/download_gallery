<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2015 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
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

#$update_when_modified = true; // Tells script to update when this page was last updated
#require(LEPTON_PATH.'/modules/admin.php');

/**
 *	Language
 */
$lang = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($lang) ? (dirname(__FILE__))."/languages/EN.php" : $lang );

$section_id = intval($_POST['section_id']);
$page_id = intval($_POST['page_id']);

require(LEPTON_PATH.'/framework/summary.functions.php');

$fileext_id = '';
if (isset($_POST['fileext_id'])) {
	$fileext_id = (int) $_POST['fileext_id'];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo $DGTEXT['MOD_TITLE']; ?></title>
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
	</head>
	<body>
		<div class="modify_section">
			<h1><?php echo $DGTEXT['MOD_FILE_EXT']; ?></h1>
			<table>
				<tr>
					<td>
					<?php
					$checkOK	= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789$#+@, ";
					$checkStr	= $_POST['file_ext'];
					$allValid	= true;
					//Loop through string and see if illegal chars are used
					for ($i = 0;  $i < strlen($checkStr);  $i++) {
						$ch = substr($checkStr, $i, 1);
						if (strpos($checkOK, $ch)===FALSE) {
							$allValid = false;
							break;
						}
					}	
					// Check if string was valid
					if (!$allValid) {
						echo "Please enter only letter and numeric characters in the 'File Extensions' field. These extensions should be seperated with a comma.";
					}
					else {
						//Remove the spaces
						$checkStr = str_replace(" ","", $checkStr);
						//Update the database
						$database->query("UPDATE ".TABLE_PREFIX."mod_download_gallery_file_ext "
							. " SET extensions = '$checkStr' " 
							. " WHERE fileext_id = '$fileext_id' and page_id = '$page_id'");
						echo $DGTEXT['FILE_STORED'];
					}
					?>
					</td>
				</tr>
				<tr>
					<td style="text-align: center; height: 200px;">
					<form action="save_extsettings.php" method="post">
					<input type="button" value="<?php echo $TEXT['CLOSE']; ?>" onclick="window.close(); return false;" style="width: 120px; margin-top: 5px;" />
					</form>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>