jQuery(document).ready(function($) {
    $("#metamasterseo-tabs").tabs({
        activate: function(event, ui) {
            updatePreviewsAndAnalyzer();
        }
    });

    function updateContentAnalyzer() {
        var title = $('#metamasterseo_page_title').val();
        var description = $('#metamasterseo_meta_description').val();
        var keyphrase = $('#metamasterseo_focus_keyphrase').val();

        var analysis = '<strong>Real-time Content Analyzer:</strong><br>';
        analysis += 'Title Length: ' + title.length + ' characters <span class="' + getIndicatorClass(title.length, 30, 60) + '">' + getIndicatorText(title.length, 30, 60) + '</span><br>';
        analysis += 'Description Length: ' + description.length + ' characters <span class="' + getIndicatorClass(description.length, 50, 160) + '">' + getIndicatorText(description.length, 50, 160) + '</span><br>';
        
        if (keyphrase.length > 0) {
            var keyphraseInTitle = title.includes(keyphrase);
            var keyphraseInDescription = description.includes(keyphrase);
            analysis += 'Focus Keyphrase in Title: ' + (keyphraseInTitle ? 'Yes' : 'No') + ' <span class="' + (keyphraseInTitle ? 'good' : 'bad') + '">' + (keyphraseInTitle ? 'Good' : 'Bad') + '</span><br>';
            analysis += 'Focus Keyphrase in Description: ' + (keyphraseInDescription ? 'Yes' : 'No') + ' <span class="' + (keyphraseInDescription ? 'good' : 'bad') + '">' + (keyphraseInDescription ? 'Good' : 'Bad') + '</span><br>';
        }

        $('#metamasterseo-content-analyzer').html(analysis);
        updateIndicators();
    }

    function getIndicatorText(value, min, max) {
        if (value < min) return 'Too short';
        if (value > max) return 'Too long';
        return 'Good';
    }

    function getIndicatorClass(value, min, max) {
        if (value < min || value > max) return 'bad';
        return 'good';
    }

    function updateIndicators() {
        updateFieldIndicator('#metamasterseo_page_title', 30, 60);
        updateFieldIndicator('#metamasterseo_meta_description', 50, 160);
        updateFieldIndicator('#metamasterseo_focus_keyphrase', 1, 100);
    }

    function updateFieldIndicator(selector, min, max) {
        var value = $(selector).val().length;
        var indicator = $(selector + '_indicator');
        indicator.text(getIndicatorText(value, min, max));
        indicator.removeClass('good bad').addClass(getIndicatorClass(value, min, max));
    }

    function evaluateField(field, minLength, maxLength) {
        var value = field.val();
        var indicator = $('#' + field.attr('id') + '_indicator');
        var length = value.length;

        if (length === 0) {
            indicator.text('');
            indicator.removeClass('good bad poor');
        } else if (length < minLength) {
            indicator.text('Poor');
            indicator.removeClass('good');
            indicator.addClass('poor');
        } else if (length > maxLength) {
            indicator.text('Bad');
            indicator.removeClass('good poor');
            indicator.addClass('bad');
        } else {
            indicator.text('Good');
            indicator.removeClass('bad poor');
            indicator.addClass('good');
        }
    }

    function updateGooglePreview() {
        var pageTitle = $('#metamasterseo_page_title').val();
        var metaDescription = $('#metamasterseo_meta_description').val();
        var slug = $('#metamasterseo_slug').val();
        var googlePreview = $('#metamasterseo-google-preview');
        
        googlePreview.html(`
            <div class="google-preview">
                <div class="google-preview-title">${pageTitle}</div>
                <div class="google-preview-url">${window.location.origin}/${slug}</div>
                <div class="google-preview-description">${metaDescription}</div>
            </div>
        `);
    }

    function updateFacebookPreview() {
        var facebookTitle = $('#metamasterseo_facebook_title').val();
        var facebookDescription = $('#metamasterseo_facebook_description').val();
        var facebookImage = $('#metamasterseo_facebook_image').val();
        var facebookPreview = $('#metamasterseo-facebook-preview');
        
        facebookPreview.html(`
            <div class="facebook-preview">
                ${facebookImage ? '<img src="' + facebookImage + '" alt="Facebook Image" class="facebook-preview-image">' : ''}
                <div class="facebook-preview-title">${facebookTitle}</div>
                <div class="facebook-preview-description">${facebookDescription}</div>
            </div>
        `);
    }

    function updateTwitterPreview() {
        var twitterTitle = $('#metamasterseo_twitter_title').val();
        var twitterDescription = $('#metamasterseo_twitter_description').val();
        var twitterImage = $('#metamasterseo_twitter_image').val();
        var twitterPreview = $('#metamasterseo-twitter-preview');
        
        twitterPreview.html(`
            <div class="twitter-preview">
                ${twitterImage ? '<img src="' + twitterImage + '" alt="Twitter Image" class="twitter-preview-image">' : ''}
                <div class="twitter-preview-title">${twitterTitle}</div>
                <div class="twitter-preview-description">${twitterDescription}</div>
            </div>
        `);
    }

    function updatePreviewsAndAnalyzer() {
        updateContentAnalyzer();
        updateGooglePreview();
        updateFacebookPreview();
        updateTwitterPreview();
    }

    function mediaUpload(buttonClass) {
        var _custom_media = true,
            _orig_send_attachment = wp.media.editor.send.attachment;

        $('body').on('click', buttonClass, function(e) {
            var button = $(this);
            var buttonId = button.attr('id');
            var send_attachment_bkp = wp.media.editor.send.attachment;
            _custom_media = true;

            wp.media.editor.send.attachment = function(props, attachment) {
                if (_custom_media) {
                    button.siblings('input:hidden').val(attachment.url);
                    button.siblings('img').attr('src', attachment.url).show();
                    if (buttonId.includes('facebook')) {
                        updateFacebookPreview();
                    }
                    if (buttonId.includes('twitter')) {
                        updateTwitterPreview();
                    }
                } else {
                    return _orig_send_attachment.apply(buttonId, [props, attachment]);
                }
            }

            wp.media.editor.open(button);
            return false;
        });

        $('.add_media').on('click', function() {
            _custom_media = false;
        });
    }

    var fieldsToEvaluate = [
        { selector: '#metamasterseo_page_title', minLength: 30, maxLength: 60 },
        { selector: '#metamasterseo_meta_description', minLength: 50, maxLength: 160 },
        { selector: '#metamasterseo_focus_keyphrase', minLength: 3, maxLength: 10 },
        { selector: '#metamasterseo_facebook_title', minLength: 30, maxLength: 60 },
        { selector: '#metamasterseo_facebook_description', minLength: 50, maxLength: 160 },
        { selector: '#metamasterseo_twitter_title', minLength: 30, maxLength: 60 },
        { selector: '#metamasterseo_twitter_description', minLength: 50, maxLength: 160 }
    ];

    fieldsToEvaluate.forEach(function(field) {
        var element = $(field.selector);
        element.on('input', function() {
            evaluateField(element, field.minLength, field.maxLength);
            updatePreviewsAndAnalyzer();
        });

        // Initial evaluation
        evaluateField(element, field.minLength, field.maxLength);
        updatePreviewsAndAnalyzer();
    });

    mediaUpload('.upload_image_button');
    updatePreviewsAndAnalyzer();
});

jQuery(document).ready(function($) {
    $('#generate-meta-data').on('click', function() {
        var content = $('#metamasterseo_page_title').val(); // or other content to be used for generation
        $.post(ajaxurl, {
            action: 'generate_meta_data',
            content: content
        }, function(response) {
            if (response.success) {
                $('#metamasterseo_page_title').val(response.data.title);
                $('#metamasterseo_meta_description').val(response.data.description);
                // Set other fields as needed
            } else {
                alert('Error: ' + response.data);
            }
        });
    });
});
