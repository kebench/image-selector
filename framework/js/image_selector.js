jQuery(document).ready(function(){
	jQuery("#image_folder").change(function(){
		jQuery(function(){
			
		});
	});
	
	/*jQuery(function(){
		jQuery("body").on("change", "#files", function(){
			var file = jQuery(this).val();
			var folder = jQuery("#image_folder").val();
			jQuery("#show-preview img").attr('src', URL.siteurl + '/wp-content/uploads/'+jQuery("#image-folder").val()+'/'+ folder +'/' + file);
		});
	});*/
	
	jQuery(function(){
		jQuery("#file-wrap").on("click","img",function(){
			jQuery(".img-thumbnail").removeClass("img-selected");
			jQuery(this).parent().addClass("img-selected");
		});
	});
});

function so_call_me_maybe( boom ){
	return jQuery.ajax({
		type: 'POST',
		url: URL.siteurl + '/wp-admin/admin-ajax.php',
		data: boom
	});
}