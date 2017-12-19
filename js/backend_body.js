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

 $("tbody").sortable({
    items: "> tr:not(:first)",
    appendTo: "parent",
    helper: "clone",
	update: function(event, ui) {
			var fileOrder = $(this).sortable('toArray').toString();
			var result = $.post( LEPTON_URL+'/modules/download_gallery/update_sort.php', {fileOrder:fileOrder}).success(function(data, statusText) {
                dg_ajax_return( data, statusText );
            });
	}	
}).disableSelection();

function dg_ajax_return( data, statusText ) {
    alert(data);
}