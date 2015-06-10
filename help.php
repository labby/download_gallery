<?php
/* 
 * Copyright and more information see file info.php
 */

// STEP 1:	Initialize
if (file_exists('../../config.php')) {
	require('../../config.php');   // called from within page settings
} else {
	require('../../../config.php');	// called from within module info
}

// Include WB admin wrapper script
require(LEPTON_PATH.'/modules/admin.php');

// STEP 2:	Display the help page.
?>

<div class="download_gallery_help">
	<h2>Help about the Download Gallery Module</h2>
	
	<p>This file contains help about the different option for the downloads module. The Downloads module provides you the possibility to display a list of downloads within your Website Baker website. As can be read down below this module is very flexible and the output can be maintained via the settings.</p>
	
	<p>The help is divided in 3 sections: <a href="#main_settings">Main Settings</a>, <a href="#layout_settings">Layout Settings</a> and the <a href="#changelog">changelog</a>. All this information is described down below.</p>
	
	<a name="main_settings"></a><h3>Main Settings</h3>
	<p>This section contains the required fields that are neccesary to get this module to work.</p>
	<ul>
		<li><code>Files per page</code> - When this field will be set the previous/next should also be shown on a page.</li>
		<li><code>Roundup file size</code> - If checked the file size will be rounded to whole numbers</li>
		<li><code>Display decimals</code> - With this option you can specify how many decimals you would like to show for the file size</li>
		<li><code>File type extensions</code> - For the listed file types images are available (<i>as you can see down below</i>. With this section you can modify which extensions you would like to have for a specific file type</li>
	</ul>
		
	<a name="layout_settings"></a><h3>Layout Settings</h3>
	<p>With the options down below you can modify the layout of the different sections for the download module.</p>
	<ul>
		<li><code>Header</code> - This layout section will be shown as first on the output.
		It can e.g. be used for opening a division (<code>&lt;div&gt;</code>).
			<ul>
				<li><code>[SEARCH]</code> - Display the Search Layout</li>
			</ul>
		</li>
		<li><code>Footer</code> - This field will be shown at the end of the layout output of this module. For the 'Header' or the 'Footer' field the following codes can be used:
			<ul>
				<li><code>[NEXT_PAGE_LINK]</code> - Link to show the next section of downloads (<i>based upon the 'files per page' setting'</i>)</li>
				<li><code>[NEXT_LINK]</code> - Link to show the next download gallery file</li>
				<li><code>[PREVIOUS_PAGE_LINK]</code> - Link to show the previous section of downloads (<i>based upon the 'files per page' setting'</i>)</li>
				<li><code>[PREVIOUS_LINK]</code> - Link to show the previous download gallery file</li>
				<li><code>[OUT_OF]</code> - Gives back a string what is currently being shown on the page.</li>
				<li><code>[OF]</code> - Total number of files that are available for downloading on all the pages.</li>
				<li><code>[DISPLAY_PREVIOUS_NEXT_LINKS]</code> - stylesheet option whether the option to display the previous/next section is enabled</li>
				<li><code>[SEARCH]</code> - Display the Search Layout</li>
			</ul>
		</li>
		<li><code>File header</code> - This field will be displayed at the beginning of the download file list.
			<ul>
				<li><code>[THTITLE]</code> - The title button of the list with sort function</li>
				<li><code>[THCHANGED]</code> - The date button of the list with sort function</li>
				<li><code>[THSIZE]</code> - The size button of the list with sort function</li>
				<li><code>[THCOUNT]</code> - The download counter button of the list with sort function</li>
			</ul>
		</li>
		<li><code>Files loop</code> - This section will be repeated each time a download is found. Within this field one can use the following codes:
			<ul>
				<li><code>[TITLE]</code> - The title of the download file.</li>
				<li><code>[DESCRIPTION]</code> - The description for this download file.</li>
				<li><code>[LINK]</code> - The link to the file so people can download it.</li>
				<li><code>[EXT]</code> - The extension of the file that can be downloaded.</li>
				<li><code>[FTIMAGE]</code> - This returns the path to an image based upon the extension. The following images are used:
					<ul>
						<li><img src="images/image.gif" alt="" title="Icon: Image" /> - Image file</li>
						<li><img src="images/music.gif" alt="" title="Icon: Music" /> - Music file</li>
						<li><img src="images/movie.gif" alt="" title="Icon: Movie" /> - Movie file</li>
						<li><img src="images/text.gif" alt="" title="Icon: Text" /> - Text file</li>
						<li><img src="images/pdf.gif" alt="" title="Icon: PDF" /> - PDF file</li>
						<li><img src="images/document.gif" alt="" title="Icon: Document" /> - Document file</li>
						<li><img src="images/spreadsheet.gif" alt="" title="Icon: Spreadsheet" /> - Spreadsheet file</li>
						<li><img src="images/presentation.gif" alt="" title="Icon: Presentation" /> - Presentation file</li>
						<li><img src="images/unknown.gif" alt="" title="Icon: Unknown" /> - Unknown file</li>
					</ul>
				</li>
				<li><code>[SIZE]</code> - The size of the download file.</li>
				<li><code>[DATE]</code> - The date on which the download file has been last modified.</li>
				<li><code>[TIME]</code> - The time on which the download file has been last modified.</li>
				<li><code>[USER_ID]</code> - The userid of the person who has modified the download file.</li>
				<li><code>[USERNAME]</code> - The username of the person who has modified the download file.</li>
				<li><code>[DISPLAY_NAME]</code> - The display name of the person who has modified the download file.</li>
				<li><code>[EMAIL]</code> - The e-mail address of the person who has modifief the download file.</li>
				<li><code>[DL]</code> - The number of times the file has been downloaded</li>
				<li><code>[FID]</code> - will create an anchor called "A"+file_id (this will not work when the list goes over multiple pages, so use with care!)</li>
			</ul>
		</li>
		<li><code>File Footer</code> - This section will be repeated at the end of the download file list.</li>
		<li><code>Group Header</code> - This section will be repeated above the group title listing</li>
		<li><code>Group Loop</code> - This section will be repeated at the end of the download file list.
			<ul>
				<li><code>[GROUPTITLE]</code> - The title of the group being displayed next</li>
			</ul>
		</li>
		<li><code>Group Footer</code> - This section will be repeated after the group title.</li>
		<li><code>Search</code> - This section will be used within the header and/or footer to display the search/filter box for this gallery.
			<ul>
				<li><code>[SEARCHBOX]</code> - Display the text box for the search.</li>
				<li><code>[SEARCHSUBMIT]</code> - Display the submit button for the search</li>
				<li><code>[SEARCHRESULT]</code> - Display the result summary for the search.</li>
			</ul>
		</li>
	</ul>
	
	<a name="changelog"></a><h3>Changelog</h3>

	<strong>Version 2.22 modified (12-14-2010) by Frank Heyne</strong>
		<ul>
			<li><strong>Fixed</strong> backend security</li>
		</ul>
	<hr />
	
	<strong>Version 2.21 modified (09-10-2009) by Frank Heyne</strong>
		<ul>
			<li><strong>Fixed</strong> backend security, thanks to doc!</li>
		</ul>
	<hr />
	
	<strong>Version 2.20 modified (09-05-2009) by Frank Heyne</strong>
		<ul>
			<li><strong>Fixed</strong> problem with wrong mime types</li>
			<li><strong>Fixed</strong> some bugs, partly security related</li>
		</ul>
	<hr />
	
	<strong>Version 2.13 modified (08-26-2009) by Frank Heyne</strong>
		<ul>
			<li><strong>Added</strong> ability to add links to files on Windows network shares (they do only work with IE!)</li>
			<li><strong>Fixed</strong> download links now are no longer guessable</li>
		</ul>
	<hr />
	
	<strong>Version 2.12 modified (08-20-2009) by Frank Heyne</strong>
		<ul>
			<li><strong>Fixed</strong> install.php, thanks aldus!</li>
		</ul>
	<hr />
	
	<strong>Version 2.10 modified (06-14-2009) by Frank Heyne</strong>
		<ul>
			<li><strong>Added</strong> to user upload: possibility to add file group</li>
			<li><strong>Added</strong> to user upload: made captcha optional</li>
			<li><strong>Fixed</strong> in user upload: sorted php, html and css code into different files</li>
			<li><strong>Added</strong> new place holder for anchor: [FID] will create an anchor called "A"+file_id
				(this will not work when the list goes over multiple pages, so use with care!)</li>
			<li><strong>Fixed</strong> in the backend: html validation errors (as far as module code is concerned)</li>
		</ul>
	<hr />
	
	<strong>Version 2.05 modified (04-27-2009) by Frank Heyne</strong>
		<ul>
			<li><strong>Merged</strong> dlc.php and dl.php, removed dl.php</li>
			<li><strong>Fixed</strong> bug which did disallow downloads from restricted pages with multiple groups (except for admins)</li>
		</ul>
	<hr />
	
	<strong>Version 2.00 modified (12-23-2008) by Frank Heyne</strong>
		<ul>
			<li><strong>Added:</strong> possibility to add remote links to the list</li>
			<li><strong>Added:</strong> possibility to sort list by title, date, size and downloads<br />
				(currently sort buttons must be placed into $setting_file_header)<br />
				place holders for sort buttons are: [THTITLE], [THCHANGED], [THSIZE], [THCOUNT]</li>
			<li><strong>Changed:</strong> modified_when from time stamp of the entry to the last write date of the file</li>
			<li><strong>Fixed:</strong> warnings which happened when run with PHP error level E_ALL&amp;E_STRICT</li>
			<li><strong>Fixed:</strong> moved frontend css into separate file</li>
			<li><strong>Changed:</strong> minor improvements</li>
		</ul>
	<hr />

	<strong>Version 1.91 modified (12-06-2008) by Christian Sommer</strong>
		<ul>
			<li><strong>Added:</strong> some access restrictions and .htaccess file to: /media/download_gallery/</li>
		</ul>
	<hr />

	<strong>Version 1.8 modified (11-30-2006) by RSmith</strong>
		<ul>
			<li><strong>Added:</strong> Searchfilter</li>
			<li><strong>Fixed:</strong> Upgrade script only upgrades new fields</li>
		</ul>
	<hr />
	<strong>Version 1.7 modified (11-19-2006) by RSmith</strong>
		<ul>
			<li><strong>Added:</strong> Download Gallery Groups added</li>
		</ul>
	<hr />
	<strong>Version 1.6 modified (10-16-2006) by Rsmith</strong>
		<ul>
			<li><strong>Added:</strong> Addititonal Upload Settings - none, publc, registered</li>
			<li><strong>Added:</strong> Download rights based on page settings, Public or registered</li>
			<li><strong>Added:</strong> File URL hidden so not easily found and linkable</li>
		</ul>
	<hr />
	<strong>Version 1.5 modified (09-12-2006) by Ruebenwurzel</strong>
		<ul>
			<li><strong>Fixed:</strong> install script now supports mysql5 strict mode</li>
		</ul>
	<hr />
	<strong>Version 1.4 modified (5-15-2006) by RSmith</strong>
		<ul>
			<li><strong>Fixed:</strong> File deleting only deletes the file when the file is no longer used in any download gallery section</li>
			<li><strong>Fixed:</strong> Section deleting only deletes the file when it is no longer used in any section</li>
			<li><strong>Added:</strong> Ability to select any file from the MEDIA_DIRECTORY &amp; subdirectorys</li>
			<li><strong>Added:</strong> Can only delete files that have been uploaded via, download gallery</li>
			<li><strong>Added:</strong> Automatic delete of empty/unsaved records</li>
			<li><strong>Added:</strong> Sort records ascending / descending</li>
			<li><strong>Added:</strong> Sort records manualy or by title</li>
			<li><strong>Added:</strong> Sort records by extension, ascending or descending</li>
			<li><strong>Added:</strong> User based uploading files now enabled/disabled via admin</li>
			<li><strong>Added:</strong> Overwrite - If a file exists in any download gallery section it will not be overwritten unless the Overwrite checkbox is checked</li>
			<li><strong>Added:</strong> Download Counter for the number of times the file has been downloaded</li>
			<li><strong>Added:</strong> Added additional Lanugage File entries</li>
			<li><strong>Added:</strong> Hides extension sort setting when Ordered Manualy in the setting admin</li>
		</ul>
	<hr />
	<strong>Version 1.3 modified (3-10-2006) by Ruebenwurzel</strong>
		<ul>
			<li><strong>Added:</strong> WYSIWYG for the Description Field</li>
		</ul>
	<hr />
	<strong>Version 1.1 modified (12-28-2005) by Ruebenwurzel</strong>
		<ul>
			<li><strong>Added:</strong> Language support to modify extension windows</li>
			<li><strong>Fixed:</strong> Bug in modify extension window</li>
			<li><strong>Changed:</strong> Minor layout changes in admin Interface</li>
			<li><strong>Fixed:</strong> Code cleaning and all files stored in UNIX</li>
		</ul>
	<hr />
	<strong>Version 1.0 modified (12-01-2005) by Woudloper</strong>
		<ul>
			<li><strong>Initial Release:</strong> all the available features</li>
		</ul>

<?php
if ($section_id > 1) {
?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">	
		<tr>
			<td>
				<input type="button" value="<?php echo $TEXT['BACK']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
			</td>
		</tr>	
	</table>
</div>
<?php
}
$admin->print_footer();
?>