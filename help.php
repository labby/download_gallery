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

// Include admin wrapper script
require(LEPTON_PATH.'/modules/admin.php');

// Display the help page.
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

	<h4>This module is hosted and developed on GITHUB.</h4>
	<p>You can follow the changelog on <a href="https://github.com/labby/download_gallery/commits/master" target="_blank">the commit section on github.</a> if you like</p>
	<hr />

<?php
if ($section_id > 1) {
?>
	<table cellpadding="0" cellspacing="0" border="0" width="100%">	
		<tr>
			<td>
				<input class="ui button" type="button" value="<?php echo $TEXT['BACK']; ?>" onclick="javascript: window.location = '<?php echo ADMIN_URL; ?>/pages/modify.php?page_id=<?php echo $page_id; ?>';" style="width: 100px; margin-top: 5px;" />
			</td>
		</tr>	
	</table>
</div>
<?php
}
$admin->print_footer();
?>