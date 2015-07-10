/**
 Allows media uploader to be used in the backend.

 @since 1.0.0
 @package TechnicalDocs
 */
(function ($) {
    'use strict';

    $(function () {

        var $uploaders = $('.technicaldocs-media-uploader');

        if ($uploaders.length) {
            $uploaders.each(function () {

                // Instantiates the variable that holds the media library frame.
                var meta_image_frame,
                    button_text = $(this).data('button-text') || 'Use Image',
                    title_text = $(this).data('title-text') || 'Select an Image';

                // Runs when the image button is clicked.
                $(this).find('.upload').click(function (e) {

                    var $button = $(this);

                    // Prevents the default action from occurring.
                    e.preventDefault();

                    // If the frame already exists, re-open it.
                    if (meta_image_frame) {
                        meta_image_frame.open();
                        return;
                    }

                    // Sets up the media library frame
                    meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
                        title: title_text,
                        button: {text: button_text},
                        library: {type: 'application/pdf'}
                    });

                    // Runs when an image is selected.
                    meta_image_frame.on('select', function () {

                        // Grabs the attachment selection and creates a JSON representation of the model.
                        var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

                        // Sends the attachment URL to our custom image input field.
                        $button.siblings('.image-id').val(media_attachment.id);
                        $button.siblings('.image-preview').attr('src', media_attachment.url);
                        $button.siblings('.url-preview').html(media_attachment.url);
                    });

                    // Opens the media library frame.
                    meta_image_frame.open();
                });
            });
        }
    });
})(jQuery);