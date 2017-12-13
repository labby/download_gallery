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

// include_file("file-name", "filetype"); // JS/CSS
function include_file(filename, filetype) {

	if(!filetype) 
		var filetype = 'js'; //js default filetype
	
	var th = document.getElementsByTagName('head')[0];
	var s = document.createElement((filetype == "js") ? 'script' : 'link');
	
	s.setAttribute('type',(filetype == "js") ? 'text/javascript' : 'text/css');
	
	if (filetype == "css") 
		s.setAttribute('rel','stylesheet');
		
	s.setAttribute((filetype == "js") ? 'src' : 'href', filename);
	th.appendChild(s);
	
}


// use jquery ui sortable
  $( function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
  } );
