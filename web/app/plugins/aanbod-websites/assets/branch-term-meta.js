jQuery(document).ready(function($) {
    var mediaUploader;

    // Upload image button
    $(document).on('click', '.branch-upload-image', function(e) {
        e.preventDefault();

        var button = $(this);
        var preview = button.siblings('.branch-image-preview');
        var input = button.siblings('#branch_image');
        var removeBtn = button.siblings('.branch-remove-image');

        // If the uploader object has already been created, reopen the dialog
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Extend the wp.media object
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Kies een afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });

        // When a file is selected, run a callback
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();

            // Set the image ID
            input.val(attachment.id);

            // Show preview
            var imgTag = '<img src="' + attachment.url + '" style="max-width: 200px; height: auto;" />';
            preview.html(imgTag);

            // Show remove button
            removeBtn.show();
        });

        // Open the uploader dialog
        mediaUploader.open();
    });

    // Remove image button
    $(document).on('click', '.branch-remove-image', function(e) {
        e.preventDefault();

        var button = $(this);
        var preview = button.siblings('.branch-image-preview');
        var input = button.siblings('#branch_image');

        // Clear the input
        input.val('');

        // Clear the preview
        preview.html('');

        // Hide remove button
        button.hide();
    });
});
