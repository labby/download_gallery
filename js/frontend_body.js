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

// use jquery.filtertable.min, see options on http://sunnywalker.github.io/jQuery.FilterTable/

    $(document).ready(function() {
        $('table').filterTable({ // apply filterTable to all tables on this page
            inputSelector: '#input-filter', // use the existing input instead of creating a new one
			//minRows: 0		// start filtering if rows are minRows
        });
    });
	
