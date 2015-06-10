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

// include CSS in <head>
include_file('../../modules/download_gallery/drag_n_drop.css', 'css'); 


if(!jQuery().sortable){ 
	// load_jQ_UI;			
	include_file('../../include/jquery/jquery-ui-min.js', 'js'); 
}

// start window load function
$(window).load(function(){
	
	// load jQuery UI if sortable not loaded

	if(jQuery().sortable){
				
	 	
	 	/** 
			Drag&Drop 
			=========
			sortable | http://jqueryui.com/demos/sortable/
		*/	
		$(function() { 
			$('.dragdrop_download_gallery').addClass('dragdrop_handle'); // this class="dragdrop_download_gallery" will result in class="dragdrop_download_gallery dragdrop_handle"
			$("#DownloadGalleryFiles .move_position a").remove(); // remove up/down icons (we have dragNdrop therefore we don't need them)
		
			
			$("#DownloadGalleryFiles tbody").sortable({ 
				appendTo: 	'body',
				handle:  	'.dragdrop_handle',
				opacity: 	0.8, 
				cursor: 	'move', 
				delay: 		100, 
				items: 		'tr.frow',
				dropOnEmpty: false,
				update: function() { 
					var order = $(this).sortable("serialize") + '&action=updatePosition&group='+used_gID; 
					$.post("../../modules/download_gallery/move_dragdrop.php", order, function(acknowledgement){
						$("#downloadGalleryResult").html(acknowledgement).fadeIn("slow");	
						$("#downloadGalleryResult").fadeOut(5500);						
					}); 	
				}				
			})
		}); 
		
	}//endif
	
	/*
		hover effect for item rows 	
	*/
	$("tr.frow")
		.mouseover(function(){
			$(this).addClass("frow_hover");
		})
		.mouseout(function(){
			$(this).removeClass("frow_hover");
		});
	
	
}); //window.load
