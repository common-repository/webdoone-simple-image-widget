jQuery(function ($){
    'use strict';
        $('body').on('click','.webdoone-si-custom-media-upload',function(e) {
            e.preventDefault();
            var clicked = $(this).closest('div');
            var custom_uploader = wp.media({
                title: 'Webdoone Simple Image',
                // button: {
                //     text: 'Custom Button Text',
                // },
                multiple: false  // Set this to true to allow multiple files to be selected
                })
            .on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                clicked.find('.webdoone-si-custom-media-img').attr('src', attachment.url);
                clicked.find('.webdoone-si-custom-media-url').val(attachment.url).trigger('change');
                clicked.find('.webdoone-si-custom-media-upload').hide();
                clicked.find('.webdoone-si-clear-field').show();
            }) 
            .open();
        });

        $('body').on('click','.webdoone-si-clear-field',function(e) {
            $(this).closest('div').find('.webdoone-si-custom-media-img').removeAttr('src');
            $(this).closest('div').find('.webdoone-si-custom-media-url').val('').trigger('change');
            $(this).closest('div').find('.webdoone-si-clear-field').hide();
            $(this).closest('div').find('.webdoone-si-custom-media-upload').show();
            return false;
        });
});
