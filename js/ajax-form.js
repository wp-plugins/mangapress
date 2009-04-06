jQuery(function() {
	jQuery.ajax({
	type: "POST",
	url: "mangapress-uninstall.php",
	success: function(msg) {
		jQuery('#confirm_message').append(msg)
		.hide()
		.fadeIn(1500);	
		}
	});
});