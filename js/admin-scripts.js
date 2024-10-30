jQuery(document).ready(function($) {
    // Add click event to tabs
    $('.nav-tab').click(function(e) {
        e.preventDefault();
        // Remove active class from all tabs and add to the clicked tab
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // Hide all tab content and show the related content
        var target = $(this).data('target');
        $('.tab-content').removeClass('active');
        $('#' + target).addClass('active');
    });

    // Show the first tab by default
    $('.nav-tab-wrapper a:first').addClass('nav-tab-active');
    $('.tab-content:first').addClass('active');

    // Image upload for Facebook
    $('#upload_fb_image_button').click(function(e) {
        e.preventDefault();
        var custom_uploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#metamasterseo_fb_image').val(attachment.url);
            $('#fb_image_preview').attr('src', attachment.url).show();
        }).open();
    });

    // Image upload for Twitter
    $('#upload_tw_image_button').click(function(e) {
        e.preventDefault();
        var custom_uploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        }).on('select', function() {
            var attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#metamasterseo_tw_image').val(attachment.url);
            $('#tw_image_preview').attr('src', attachment.url).show();
        }).open();
    });
});
