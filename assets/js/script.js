jQuery(document).ready(function($){
    "use strict";
    // Disabling autoDiscover, otherwise Dropzone will try to attach twice.
    Dropzone.autoDiscover = false;
    
    jQuery("#media-uploader").dropzone({
        url: dropParam.upload,
        acceptedFiles: 'image/*',
        maxFilesize: 2,
        maxFiles: 4,
        dictDefaultMessage: "Ajouter une galerie d'images sur le produit",
        dictInvalidFileType: "Ce type de fichier est invalide",
        dictRemoveFile: "Supprimer",
        dictRemoveFileConfirmation: "Voulez-vous vraiement supprimer le fichier?",
        dictCancelUpload: "Annuler",
        dictUploadCanceled: "Televersement annule",
        dictCancelUploadConfirmation: "Voulez-vous vraiement annuler le televersement?",
        //previewsContainer: "#previews",
        //previewTemplate: document.getElementById('customTemplate').innerHTML,
        success: function (file, response) {
            console.log(file);
            console.log(response);
            file.previewElement.classList.add("dz-success");
            file['attachment_id'] = response; // push the id for future reference
            var ids = jQuery('#product_image_gallery').val() + ',' + response;
            jQuery('#product_image_gallery').val(ids);
        },
        error: function (file, response) {
            console.log(file);
            console.log(response);
            file.previewElement.classList.add("dz-error");
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
});