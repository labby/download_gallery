<?php

/**
 *  @module			Download Gallery
 *  @version		see info.php of this module
 *  @authors		Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
 *  @copyright		2010-2017 Hudge, Woudloper, M. Gallas, R. Smith, C. Sommer, F. Heyne, Aldus, erpe
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

// Get id
$file_id = '';
if(!isset($_GET['file_id']) || !is_numeric($_GET['file_id'])) 
	header("Location: ".ADMIN_URL."/pages/index.php");
else 
	$file_id = intval($_GET['file_id']);

$preselected_group = (isset($_GET['group_id']) && is_numeric($_GET['group_id'])) ? intval($_GET['group_id']) : 0;

// Include admin wrapper script
require(LEPTON_PATH.'/modules/admin.php');
require(LEPTON_PATH.'/framework/summary.functions.php');

if(file_exists(LEPTON_PATH.'/modules/download_gallery/languages/'.LANGUAGE.'.php')) {
	require_once(LEPTON_PATH.'/modules/download_gallery/languages/'.LANGUAGE.'.php');
} else {
	require_once(LEPTON_PATH.'/modules/download_gallery/languages/EN.php');
}

// Get header and footer
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_files WHERE file_id = '$file_id' and page_id = '$page_id'");
$fetch_content = $query_content->fetchRow();

// General File Information
$fname = $fetch_content['filename'];

if($fname == '') {
	$fname = 'dummy_file_name_wb.ext';
  $remotelink = '';
} elseif ((strpos($fname, ':/') > 1)) {
  $remotelink = $fname;
	$fname = 'dummy_file_name_wb.ext';
} else {
  $remotelink = '';
}

if (!defined('WYSIWYG_EDITOR') OR WYSIWYG_EDITOR=="none" OR !file_exists(LEPTON_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php')) {
	function show_wysiwyg_editor($name,$id,$content,$width,$height) {
		echo '<textarea name="'.$name.'" id="'.$id.'" style="width: '.$width.'; height: '.$height.';">'.$content.'</textarea>';
	}
} else {
	$id_list=array("content");
	require(LEPTON_PATH.'/modules/'.WYSIWYG_EDITOR.'/include.php');
}
?>

<form name="modify" action="<?php echo LEPTON_URL; ?>/modules/download_gallery/save_file.php" method="post" enctype="multipart/form-data" style="margin: 0;">

	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />
	<input type="hidden" name="file_id" value="<?php echo $file_id; ?>" />
	<input type="hidden" name="link" value="<?php echo $fetch_content['link']; ?>" />

	<table class="settings_table" cellpadding="2" cellspacing="0" border="0" width="100%">
		<caption class="be_lepsem"><?php echo $TEXT['MODIFY'].'/'.$TEXT['DELETE'].' '.$TEXT['FILE']; ?></caption>
		<tr>
			<th><?php echo $TEXT['ACTIVE']; ?>:</th>
			<td valign="top">				
				<input type="radio" name="active" id="active_true" value="1" <?php if($fetch_content['active'] == 1) echo ' checked="checked"'; ?> />
				<label for="active_true"><?php echo $TEXT['YES']; ?></label>			
				<input type="radio" name="active" id="active_false" value="0" <?php if($fetch_content['active'] == 0) echo ' checked="checked"'; ?> />
				<label for="active_false"><?php echo $TEXT['NO']; ?></label>
			</td>
		</tr>
		<tr>
			<th><?php echo $TEXT['TITLE']; ?>:</th>
			<td valign="top">
				<input type="text" id="title" name="title" value="<?php echo stripslashes($fetch_content['title']); ?>" style="width: 98%;font-size:12pt; font-weight:bold;" maxlength="255" />
			</td>
		</tr>

    <!-- local file: -->
		<tr>
			<th><?php echo $DGTEXT['LOKALFILE']; ?>:</th>
			<td valign="top">
				<?php
					if(file_exists(LEPTON_PATH.MEDIA_DIRECTORY.'/download_gallery/' .$fname )) :
				?>
					<b><?php echo $fname; ?></b>&nbsp;&nbsp;
						<input type="checkbox" name="delete_file" id="delete_file" value="true" /><?php echo $TEXT['DELETE']; ?>
				
				<?php elseif(trim($remotelink)!=""): ?>
						<input type="file" name="file" />
				
				<?php elseif(trim($fetch_content['filename'])!=""): ?>
						<b><input type="hidden" name="existingfile"  value="<?php echo $fetch_content['link'];?>"><?php echo $fetch_content['link'];?></b>
						<input type="checkbox" name="delete_file2" id="delete_file2" value="true" /><?php echo $TEXT['DELETE']; ?>
				<?php else : ?>
						<input type="file" name="file" />
				
				<?php endif; ?>
			</td>
		</tr>
		
		<?php if($fetch_content['filename']==""): ?>
			<tr>
				<th><?php echo $DGTEXT['EXISTINGFILE']; ?>:</th>
				<td valign="top">
					<select name="existingfile" style="width: 99%;">
					<option value=''>&nbsp;</option>
					<?php
					$folder_list=directory_list(LEPTON_PATH.MEDIA_DIRECTORY);
					array_push($folder_list,LEPTON_PATH.MEDIA_DIRECTORY);
					sort($folder_list);
					foreach($folder_list AS $name) {
						$file_list=file_list($name);
						sort($file_list);
						foreach($file_list AS $filename) {
							$thumb_count=substr_count($filename, '/thumbs/');
							if($thumb_count==0){
								echo "<option value='".LEPTON_URL.str_replace(LEPTON_PATH,'',$filename)."'>".str_replace(LEPTON_PATH.MEDIA_DIRECTORY,'',$filename)."</option>\n";
							}
							$thumb_count="";
						}
					}
					?>
					</select>
				</td>
		</tr>
		<?php endif; ?>

    <!-- alternativ: Remote Link (no Upload) -->
		<tr>
	  		<th><?php echo $DGTEXT['REMOTE_LINK']; ?>:</th>
			<td><input type="text" name="remote_link" value="<?php echo $remotelink; ?>" style="width: 98%;" maxlength="255" /></td>
		</tr>

		<tr>
	  		<th><?php echo $TEXT['GROUP']; ?>:</th>
			<td>
	  			<select name="group" style="width: 98%;">
				<option value="0"><?php echo $TEXT['NONE']; ?></option>
				<?php
				$query = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_groups WHERE section_id = '$section_id' ORDER BY position ASC");
				
				if($query->numRows() > 0) {
					
					// Loop through groups
					while($group = $query->fetchRow()) {
						?>
						
						<option value="<?php echo $group['group_id']; ?>"
						<?php 
							if($fetch_content['group_id'] == $group['group_id'] || $preselected_group == $group['group_id'] ) { 
								echo ' selected'; 
							} 
						?>>
						<?php echo $group['title']; ?>						
						</option>
						
						<?php
						
					}//endwhile
				}
				
				?>
				</select>
	  		</td>
		</tr>		
		<?php if($fetch_content['title']==""): ?>
		<tr>
			<th><?php echo $DGTEXT['OVERWRITE']; ?>:</th>
			<td valign="top">
				<input type="checkbox" name="overwrite" id="overwrite" value="yes" />
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td colspan="2"></td>
		</tr>
	
		<tr>
			<th><?php echo $TEXT['DESCRIPTION']; ?></th>
			<td>
				<?php
					show_wysiwyg_editor("description","description",htmlspecialchars($fetch_content['description']), "100%", "400");
				?>
			</td>
		</tr>
		
		<tfoot>
			<tr>
				<td style="text-align:center;">
				<input class="ui positive button" name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px; margin-top: 5px;" />
				</td>
				<td style="text-align:right;">
	                <input class="ui negative button" type="button" class="cancel" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript: window.location = '<?php
					echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
				</td>
			</tr>
		</tfoot>
	</table>
</form>
<?php
if (empty($fetch_content['title']))
	echo '<script type="text/javascript">document.getElementById("title").focus();</script>';
	
// Print admin footer
$admin->print_footer();

?>