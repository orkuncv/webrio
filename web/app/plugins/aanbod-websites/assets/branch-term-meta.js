jQuery(document).ready(function($) {
    var mediaUploader;
    var featureIndex = $('.branch-feature-item').length || 0;

    // Upload main branch image button
    $(document).on('click', '.branch-upload-image', function(e) {
        e.preventDefault();

        var button = $(this);
        var preview = button.siblings('.branch-image-preview');
        var input = button.siblings('#branch_image');
        var removeBtn = button.siblings('.branch-remove-image');

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Kies een afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            input.val(attachment.id);
            var imgTag = '<img src="' + attachment.url + '" style="max-width: 200px; height: auto;" />';
            preview.html(imgTag);
            removeBtn.show();
        });

        mediaUploader.open();
    });

    // Remove main branch image button
    $(document).on('click', '.branch-remove-image', function(e) {
        e.preventDefault();
        var button = $(this);
        var preview = button.siblings('.branch-image-preview');
        var input = button.siblings('#branch_image');
        input.val('');
        preview.html('');
        button.hide();
    });

    // Add new feature
    $(document).on('click', '#add-branch-feature', function(e) {
        e.preventDefault();

        // Hide empty message
        $('.branch-features-empty').remove();

        var featureHTML = `
            <div class="branch-feature-item" data-index="${featureIndex}" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 4px; border: 1px solid #ddd;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <strong>Feature #${featureIndex + 1}</strong>
                    <button type="button" class="button button-small remove-branch-feature">Verwijder</button>
                </div>

                <p>
                    <label>Titel</label><br>
                    <input type="text" name="branch_features[${featureIndex}][title]" value="" class="regular-text" placeholder="Bijvoorbeeld: Responsive Design" />
                </p>

                <p>
                    <label>Afbeelding</label><br>
                    <input type="hidden" name="branch_features[${featureIndex}][image]" value="" class="branch-feature-image-id" />
                    <button type="button" class="button branch-feature-upload-image">Upload afbeelding</button>
                    <button type="button" class="button branch-feature-remove-image" style="display:none;">Verwijder afbeelding</button>
                    <div class="branch-feature-image-preview" style="margin-top: 10px;"></div>
                </p>
            </div>
        `;

        $('#branch-features-repeater').append(featureHTML);
        featureIndex++;
    });

    // Remove feature
    $(document).on('click', '.remove-branch-feature', function(e) {
        e.preventDefault();

        $(this).closest('.branch-feature-item').remove();

        // Show empty message if no features left
        if ($('.branch-feature-item').length === 0) {
            $('#branch-features-repeater').html('<div class="branch-features-empty" style="padding: 20px; background: #f0f0f1; border-radius: 4px; margin-bottom: 10px;"><p>Geen features toegevoegd. Klik op "Voeg Feature Toe" om te beginnen.</p></div>');
        } else {
            // Renumber features
            $('.branch-feature-item').each(function(index) {
                $(this).find('strong').first().text('Feature #' + (index + 1));
            });
        }
    });

    // Upload feature image
    $(document).on('click', '.branch-feature-upload-image', function(e) {
        e.preventDefault();

        var button = $(this);
        var container = button.closest('.branch-feature-item');
        var preview = button.siblings('.branch-feature-image-preview');
        var input = button.siblings('.branch-feature-image-id');
        var removeBtn = button.siblings('.branch-feature-remove-image');

        var featureUploader = wp.media({
            title: 'Kies een feature afbeelding',
            button: {
                text: 'Gebruik deze afbeelding'
            },
            multiple: false
        });

        featureUploader.on('select', function() {
            var attachment = featureUploader.state().get('selection').first().toJSON();
            input.val(attachment.id);
            var imgTag = '<img src="' + attachment.url + '" style="max-width: 150px; height: auto;" />';
            preview.html(imgTag);
            removeBtn.show();
        });

        featureUploader.open();
    });

    // Remove feature image
    $(document).on('click', '.branch-feature-remove-image', function(e) {
        e.preventDefault();

        var button = $(this);
        var preview = button.siblings('.branch-feature-image-preview');
        var input = button.siblings('.branch-feature-image-id');

        input.val('');
        preview.html('');
        button.hide();
    });
});
