jQuery(document).ready(function($){
    "use strict";
    // Disabling autoDiscover, otherwise Dropzone will try to attach twice.
    Dropzone.autoDiscover = false;
    
    var maxFiles = jQuery("#product_images_container").data('gallery_max_upload');
    var galeryCount = 0;
    jQuery('#product_images_container ul li.wcv-gallery-image').css('cursor','default').each(function() {
        galeryCount++;
    });
    
    var poDropzone = new Dropzone("#media-uploader", {
        url: dropParam.upload,
        acceptedFiles: 'image/*',
        maxFilesize: 2, //Mo
        maxFiles: maxFiles-galeryCount,
        dictFileTooBig: dropParam.dictFileTooBig,
        dictMaxFilesExceeded: dropParam.dictMaxFilesExceeded,
        dictDefaultMessage: dropParam.dictDefaultMessage,
        dictInvalidFileType: dropParam.dictInvalidFileType,
        dictRemoveFile: dropParam.dictRemoveFile,
        dictRemoveFileConfirmation: dropParam.dictRemoveFileConfirmation,
        dictCancelUpload: dropParam.dictCancelUpload,
        dictUploadCanceled: dropParam.dictUploadCanceled,
        dictCancelUploadConfirmation: dropParam.dictCancelUploadConfirmation,
        success: function (file, response) {
            try{
                var data = JSON.parse(response);
                if(data.status==1){
                    jQuery('.wcv_gallery_msg').text('');
                    file.previewElement.classList.add("dz-success");
                    file['attachment_id'] = data.attachment_id; // push the id for future reference

                    jQuery('ul.product_images').append('<li class="wcv-gallery-image" data-attachment_id="'+data.attachment_id+'"><img width="150" height="150" src="'+file.dataURL+'" class="attachment-150x150 size-150x150" alt=""><ul class="actions"><li><a href="#" class="po_delete" title="delete"><i class="fa fa-times"></i></a></li></ul></li>');

                    var $featured_image_id = jQuery('#_featured_image_id');
                    var $image_gallery_ids = jQuery('#product_image_gallery');

                    var featured_image_id = '';
                    var attachment_ids = '';

                    jQuery('#product_images_container ul li.wcv-gallery-image').css('cursor','default').each(function() {
                        var attachment_id = jQuery(this).attr( 'data-attachment_id' );
                        if(featured_image_id===''){
                            featured_image_id = attachment_id;
                        }else{
                            attachment_ids = attachment_ids + attachment_id + ',';
                        }
                    });

                    $featured_image_id.val( featured_image_id );
                    $image_gallery_ids.val( attachment_ids );
                }else{
                    file.previewElement.classList.add("dz-error");
                    jQuery('.wcv_gallery_msg').text(data.message);
                    poDropzone.options.maxFiles = poDropzone.options.maxFiles + 1;
                }
            }catch(e){
                file.previewElement.classList.add("dz-error");
                jQuery('.wcv_gallery_msg').text(dropParam.jsonError);
                poDropzone.options.maxFiles = poDropzone.options.maxFiles + 1;
            }
            
        },
        error: function (file, response) {
            file.previewElement.classList.add("dz-error");
            jQuery('.wcv_gallery_msg').text(response);
        },
        // update the following section is for removing image from library
        addRemoveLinks: true,
        removedfile: function(file) {
            var attachment_id = file.attachment_id;        
            jQuery.ajax({
                type: 'POST',
                url: dropParam.delete,
                data: {
                    media_id : attachment_id
                }
            });
            var _ref;
            return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
        }
    });
    
	// Remove images
	$('#product_images_container').on('click', 'a.po_delete', function(e) {
        poDropzone.options.maxFiles = poDropzone.options.maxFiles + 1;
        jQuery('.wcv_gallery_msg').text('');
		var $featured_image_id = $('#_featured_image_id');
		var $image_gallery_ids = $('#product_image_gallery');
		e.preventDefault();

        $(this).closest('li.wcv-gallery-image').remove();

		var featured_image_id = '';
		var attachment_ids = '';
        
		$('#product_images_container ul li.wcv-gallery-image').css('cursor','default').each(function() {
			var attachment_id = jQuery(this).attr( 'data-attachment_id' );
            if(featured_image_id===''){
                featured_image_id = attachment_id;
            }else{
                attachment_ids = attachment_ids + attachment_id + ',';
            }
		});

		$featured_image_id.val( featured_image_id );
		$image_gallery_ids.val( attachment_ids );

		// remove any lingering tooltips
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );

		return false;
	});
});