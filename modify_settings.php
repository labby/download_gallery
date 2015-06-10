<?php
/* 
 * Copyright and more information see file info.php
 */

require('../../config.php');
require(LEPTON_PATH.'/modules/admin.php');	

// Load Language file
if(LANGUAGE_LOADED) {
	if(!file_exists(LEPTON_PATH.'/modules/download_gallery/languages/'.LANGUAGE.'.php')) {
		require_once(LEPTON_PATH.'/modules/download_gallery/languages/EN.php');
	} else {
		require_once(LEPTON_PATH.'/modules/download_gallery/languages/'.LANGUAGE.'.php');
	}
}

// Get General Settings
$query_content = $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_settings WHERE section_id = '$section_id' and page_id = '$page_id'");
$fetch_content = $query_content->fetchRow();

// List Extension types
$query_fileext 	= $database->query("SELECT * FROM ".TABLE_PREFIX."mod_download_gallery_file_ext WHERE section_id = '$section_id' and page_id = '$page_id'");

?>

<script type="text/javascript">
//<![CDATA[
function showpopup(URL, w, h) {
	day = new Date();
	id = day.getTime();
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width='+w+',height='+h+',left='+winl+',top='+wint);");
}

function process(element){
	switch(element.value){
		case "0":
			document.getElementById('extorder').style.display = "none";
			break;
		case "1":
			document.getElementById('extorder').style.display = "";
			break;
	}
}
//]]>
</script>

<div class="download_gallery" style="border:none;"><?php
	// include core functions of WB 2.7 to edit the optional module CSS files (frontend.css, backend.css)
	@include_once(LEPTON_PATH .'/framework/summary.module_edit_css.php');
 	if(function_exists('edit_module_css')) edit_module_css('download_gallery'); 
?></div>
<form name="modify" action="<?php echo LEPTON_URL; ?>/modules/download_gallery/save_settings.php" method="post" style="margin: 0;">

	<input type="hidden" name="section_id" value="<?php echo $section_id; ?>" />
	<input type="hidden" name="page_id" value="<?php echo $page_id; ?>" />

	<table class="settings_table" cellpadding="2" cellspacing="0" border="0" width="100%">
		<caption><?php echo $DGTEXT['GSETTINGS']; ?></caption>
		<tr>
			<th><?php echo $DGTEXT['FILES_PER_PAGE']; ?>:</th>
			<td valign="top"><input type="text" name="files_per_page" value="<?php echo $fetch_content['files_per_page']; ?>" style="width: 30px" /> 0 = <?php echo $TEXT['UNLIMITED']; ?></td>
		</tr>
		<tr>
			<th><?php echo $DGTEXT['FILE_ROUNDUP']; ?>:</th>
			<td valign="top">
		        <?php
		        if ($fetch_content['file_size_roundup'] == '1') {
		            $checked = 'checked="checked"';
		        } else {
		            $checked = '';
		        }
		        ?>
		        <input type="checkbox" value="1" name="file_size_round" <?php echo $checked; ?> />
		    </td>
		</tr>
		<tr>
			<th><?php echo $DGTEXT['SEARCHFILTER']; ?>:</th>
			<td valign="top">
		        <?php
		        if ($fetch_content['search_filter'] == '1') {
		            $checked = 'checked="checked"';
		        } else {
		            $checked = '';
		        }
		        ?>
		        <input type="checkbox" value="1" name="search_filter" <?php echo $checked; ?> />
		    </td>
		</tr>
		<tr>
			<th><?php echo $DGTEXT['FILE_DECIMALS']; ?>:</th>
			<td valign="top">
				<?php $decicount = stripslashes($fetch_content['file_size_decimals']); ?>
				<?php if ($decicount == "") { $decicount = 0; } ?>
				<select name="file_size_decimals" style="width: 50px">
					<option value ="0" <?php if ($decicount == 0) { echo "selected='selected'"; } ?> >0</option>
					<option value ="1" <?php if ($decicount == 1) { echo "selected='selected'"; } ?> >1</option>
					<option value ="2" <?php if ($decicount == 2) { echo "selected='selected'"; } ?> >2</option>
					<option value ="3" <?php if ($decicount == 3) { echo "selected='selected'"; } ?> >3</option>
					<option value ="4" <?php if ($decicount == 4) { echo "selected='selected'"; } ?> >4</option>
				</select>
		    </td>
		</tr>		
		<?php
		/*
		['ordering']
		0 - ascending position
		1 - descending position
		2 - ascending title
		3 - descending title
		orderby:
		position=0
		title=1
		none=9

		['extordering']
		0 - extension ascending
		1 - extension descending
		9 - extension no order
		*/
		?>
		<tr>
			<th><?php echo $DGTEXT['ORDERING']; ?>:</th>
			<td>
				<select name="ordering" style="width: 200px">
					<?php
					if (
					$fetch_content['ordering'] == '0' or $fetch_content['ordering'] == '2' ) {
						$selected_asc = 'selected="selected"';
						$selected_desc = '';
					} else {
						$selected_asc = '';
						$selected_desc = 'selected="selected"';
					}
					?>
					<option value="0" <?php echo $selected_asc; ?>><?php echo $DGTEXT['ASCENDING']; ?></option>
					<option value="1" <?php echo $selected_desc; ?>><?php echo $DGTEXT['DESCENDING']; ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th><?php echo $DGTEXT['ORDERBY']; ?>:</th>
			<td>
				<select name="orderby" style="width: 200px" onchange="process(this);">
	            <?php
	    		if ($fetch_content['ordering'] == '0' or $fetch_content['ordering'] == '1')	{
	                $selected_position = 'selected="selected"';
	                $selected_title = '';
	                $visible='none';
				} else {
	                $selected_position = '';
	                $selected_title = 'selected="selected"';
  	                $visible='';
				}
	            ?>
	            <option value="0" <?php echo $selected_position; ?>><?php echo $DGTEXT['POSITION']; ?></option>
	            <option value="1" <?php echo $selected_title; ?>><?php echo $DGTEXT['TITLE']; ?></option>
				</select>
			</td>
		</tr>
		<tr id="extorder" style="display:<?php echo $visible; ?>;">
			<th><?php echo $DGTEXT['EXTORDERING']; ?>:</th>
			<td>
				<select name="extordering" style="width: 200px">
	            <?php
	            if ( $fetch_content['extordering'] == '0' or $fetch_content['extordering'] == '' ) {
	                $extselected_asc = 'selected="selected"';
	                $extselected_desc = '';
	                $extselected_none = '';
	            } elseif ($fetch_content['extordering'] == '1' ) {
	                $extselected_asc = '';
	                $extselected_desc = 'selected="selected"';
	                $extselected_none = '';
	            } else {
   	                $extselected_asc = '';
	                $extselected_desc = '';
	                $extselected_none = 'selected="selected"';
				}
	            ?>
	            <option value="9" <?php echo $extselected_none; ?>><?php echo $DGTEXT['NOSORT']; ?></option>
	            <option value="0" <?php echo $extselected_asc; ?>><?php echo $DGTEXT['ASCENDING']; ?></option>
	            <option value="1" <?php echo $extselected_desc; ?>><?php echo $DGTEXT['DESCENDING']; ?></option>
				</select> <?php echo $DGTEXT['EXTINFO']; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo $DGTEXT['USERUPLOAD']; ?>:</th>
			<td>
				<?php
					$uploadpub="";
					$uploadreg="";
					$uploadno="";
				if ($fetch_content['userupload'] == '1') {
					$uploadpub="checked='checked'";
				} elseif ($fetch_content['userupload'] == '2') {
					$uploadreg="checked='checked'";
				}else {
					$uploadno="checked='checked'";
				}
				?>
				<input type="radio" name="userupload" class="userupload" value="" <?php echo $uploadno; ?>/><?php echo $TEXT['NONE']; ?>
				<input type="radio" name="userupload" class="userupload" value="1" <?php echo $uploadpub; ?>/><?php echo $TEXT['PUBLIC']; ?>
				<input type="radio" name="userupload" class="userupload" value="2" <?php echo $uploadreg; ?>/><?php echo $TEXT['REGISTERED']; ?>
			</td>
		</tr>
		<tr>
			<th><?php echo $TEXT['CAPTCHA_VERIFICATION']; ?>:</th>
			<td>
				<?php
					$use_captcha_true_checked = '';
					$use_captcha_false_checked = '';
					if ($fetch_content['use_captcha'] == '1') {
						$use_captcha_true_checked = "checked='checked'";
					} else {
						$use_captcha_false_checked = "checked='checked'";
					}
				?>
				<input type="radio" name="use_captcha" id="use_captcha_true" value="1" <?php echo $use_captcha_true_checked;  ?> />
				<label for="use_captcha_true"><?php echo $TEXT['ENABLED']; ?></label>
				<input type="radio" name="use_captcha" id="use_captcha_false" value="0" <?php echo $use_captcha_false_checked;  ?> />
				<label for="use_captcha_false"><?php echo $TEXT['DISABLED']; ?></label>
			</td>
		</tr>
	</table>
	<table class="settings_table" cellpadding="2" cellspacing="0" border="0" width="100%">
	<caption><?php echo $DGTEXT['FILE_TYPE_EXT']; ?></caption>

			<thead>
		
		
		<tr>
					<td><b>Type</b></td>
					<td><b>Extensions</b></td>
					<td><b><?php echo $TEXT['MODIFY']?>?</b></td>
				</tr></thead>
				<?php
				if($query_fileext->numRows() > 0) {
					while($fileext = $query_fileext->fetchRow()) {
					
					
					$unknown_icon = '<img src="images/unknown.gif" alt="[unknown.gif]" />';	
					if($ext_icon = $fileext['file_image']){								
						$ext_icon = str_replace('unknown.gif', $ext_icon, $unknown_icon);
					}else{
						$ext_icon = $unknown_icon;	
					} 
					?>
					<tr>
						<td><nobr><?php echo strtolower($ext_icon); ?> <b><?php echo $fileext['file_type']; ?></b></nobr></td>
						<td><?php
							$temp = (strlen($fileext['extensions']) > 55) ? "..." : "";
							echo substr($fileext['extensions'], 0, 55) . $temp;?>
						</td>
						<td width="20" style="padding-left: 5px;">
							<a href="javascript:showpopup('<?php echo LEPTON_URL; ?>/modules/download_gallery/modify_extensions.php?page_id=<?php echo $page_id; ?>&amp;section_id=<?php echo $section_id; ?>&amp;fileext_id=<?php echo $fileext['fileext_id']; ?>',800,400)" title="<?php echo $TEXT['MODIFY']; ?>">
								<img src="images/rename_16.png" border="0" alt="[<?php echo $TEXT['MODIFY']?>]" />
							</a>
						</td>
					</tr>
					<?php
					}
				}
				?>
	</table>

	<table class="settings_table" cellpadding="2" cellspacing="0" border="0" width="100%" style="margin-top: 5px;">
		<caption><?php echo $DGTEXT['LSETTINGS']; ?></caption>		
		<tr>
			<th><?php echo $TEXT['HEADER']; ?>:</th>
			<td valign="top"><textarea cols="50" rows="5" name="header" style="width: 98%; height: 80px;"><?php echo htmlspecialchars($fetch_content['header']); ?></textarea></td>
		</tr>
		<tr>
			<th><?php echo $TEXT['FILE'].' '.$TEXT['HEADER']; ?>:</th>
			<td class="newsection" valign="top"><textarea cols="50" rows="5" name="file_header" style="width: 98%; height: 60px;"><?php echo htmlspecialchars($fetch_content['file_header']); ?></textarea></td>
		</tr>
		<tr>
			<th><?php echo $DGTEXT['GPHEADER']; ?></th>
			<td class="newsection" valign="top"><textarea cols="50" rows="5" name="gheader" style="width:98%; height: 60px;"><?php echo htmlspecialchars($fetch_content['gheader']); ?></textarea></td>
		</tr>	
		<tr>
			<th><?php echo $DGTEXT['GPLOOP']; ?></th>
			<td valign="top"><textarea cols="50" rows="5" name="gloop" style="width:98%; height: 60px;"><?php echo htmlspecialchars($fetch_content['gloop']); ?></textarea></td>
		</tr>
		<tr>
			<th><?php echo $DGTEXT['GPFOOTER']; ?></th>
			<td valign="top"><textarea cols="50" rows="5" name="gfooter" style="width:98%; height: 60px;"><?php echo htmlspecialchars($fetch_content['gfooter']); ?></textarea></td>
		</tr>
		<tr>
			<th><?php echo $TEXT['FILE'].' '.$TEXT['LOOP']; ?>:</th>
			<td valign="top"><textarea cols="50" rows="5" name="files_loop" style="width: 98%; height: 60px;"><?php echo htmlspecialchars($fetch_content['files_loop']); ?></textarea></td>
		</tr>
		<tr>
			<th><?php echo $TEXT['FILE'].' '.$TEXT['FOOTER']; ?>:</th>
			<td valign="top"><textarea cols="50" rows="5" name="file_footer" style="width: 98%; height: 60px;"><?php echo htmlspecialchars($fetch_content['file_footer']); ?></textarea></td>
		</tr>
		<tr>
			<th><?php echo $TEXT['FOOTER']; ?>:</th>
			<td valign="top"><textarea cols="50" rows="5" name="footer" style="width: 98%; height: 80px;"><?php echo htmlspecialchars($fetch_content['footer']); ?></textarea></td>
		</tr>
		<tr class="row_separator">		
			<td colspan="2"></td>
		</tr>
		<tr>
			<th><?php echo $DGTEXT['SEARCHLAYOUT']; ?></th>
			<td class="newsection" valign="top"><textarea cols="50" rows="5" name="search_layout" style="width:98%; height: 60px;"><?php echo htmlspecialchars($fetch_content['search_layout']); ?></textarea></td>
		</tr>

	</table>

	<table cellpadding="0" cellspacing="0" border="0" width="100%">
		<tr>
			<td align="left">
				<input name="save" type="submit" value="<?php echo $TEXT['SAVE']; ?>" style="width: 100px; margin-top: 5px;" />
			</td>
			<td align="center">
				<input name="reset_table" type="submit" value="<?php echo $DGTEXT['RESET_TABLE']; ?>" style="margin-top: 5px;" />
			</td>
			<td align="right">
				<input type="button" value="<?php echo $TEXT['CANCEL']; ?>" onclick="javascript:window.location='<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
			</td>
		</tr>
	</table>
</form>
<?php
// Print admin footer
$admin->print_footer();
?>