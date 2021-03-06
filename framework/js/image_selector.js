jQuery(document).ready(function(){
	jQuery("#image_folder").change(function(){
		var sendThis = {
			'action': 'get_files',
			'image_folder': jQuery(this).val()
		}
		
		var returnData = so_call_me_maybe( sendThis );
		jQuery("#image-file").val("");
		jQuery("#file-wrap").html("<h3>Fetching images. Please wait...</h3>");
		
		returnData.success(function( data ){
			jQuery("#file-wrap").html(data).fadeIn('slow');
		});
	});
	
	jQuery(function(){
		/*jQuery("body").on("change", "#files", function(){
			var file = jQuery(this).val();
			var folder = jQuery("#image_folder").val();
			jQuery("#show-preview img").attr('src', URL.siteurl + '/wp-content/uploads/'+jQuery("#image-folder").val()+'/'+ folder +'/' + file);
		});*/
		
		jQuery("#images .img-thumbnail img").click(function(){
			jQuery(this).parent().addClass("img-selected");
		});
		
		jQuery("body").on("click", "#images .img-thumbnail img", function(){
			jQuery(".img-thumbnail").removeClass("img-selected");
			jQuery(this).parent().addClass("img-selected");
			jQuery("#image-file").val( jQuery(this).data('filename') );
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