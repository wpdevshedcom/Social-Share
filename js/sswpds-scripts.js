/* 
 * Licensed under the terms of the GNU GPL
 */
jQuery(document).ready(function($){
		
	/* Opens popup for social share buttons */
	
	$('.sswpds-social-wrap a').click(function(event) {
   		event.preventDefault();
    	window.open($(this).attr("href"), "popupWindow", "width=700,height=450,scrollbars=yes");
	});

});