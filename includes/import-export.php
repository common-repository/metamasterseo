<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Ensure WP_Filesystem is initialized
if ( ! function_exists( 'WP_Filesystem' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
}

global $wp_filesystem;

if ( ! WP_Filesystem() ) {
    // Handle error (e.g., display an error message)
    return;
}

// Export SEO data
function metamasterseo_export_data() {
    if ( isset( $_POST['export_seo_data'] ) ) {
        if ( ! isset( $_POST['metamasterseo_export_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['metamasterseo_export_nonce'] ) ), 'metamasterseo_export_nonce_action' ) ) {
            // Nonce verification failed
            return;
        }

        $filename = 'seo-data-' . current_time('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . esc_attr($filename));

        // Temporary variable to hold file content
        $output = '';
        
        // Add the CSV header
        $output .= implode(',', array(
            'Post ID', 'Default Page Title', 'SEO Page Title', 'Default Slug', 'SEO Slug', 'Meta Description', 
            'Breadcrumb Title', 'Focus Keyphrase', 'Facebook Title', 
            'Facebook Description', 'Facebook Image', 'Twitter Title', 
            'Twitter Description', 'Twitter Image', 'Allow Search Engines', 
            'Follow Links'
        )) . "\n";

        $posts = get_posts(array(
            'post_type' => 'any',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ));

        foreach ( $posts as $post ) {
            $meta_fields = array(
                'metamasterseo_page_title' => get_post_meta($post->ID, '_metamasterseo_page_title', true) ?: '',
                'metamasterseo_meta_description' => get_post_meta($post->ID, '_metamasterseo_meta_description', true) ?: '',
                'metamasterseo_slug' => get_post_meta($post->ID, '_metamasterseo_slug', true) ?: '',
                'metamasterseo_breadcrumb_title' => get_post_meta($post->ID, '_metamasterseo_breadcrumb_title', true) ?: '',
                'metamasterseo_focus_keyphrase' => get_post_meta($post->ID, '_metamasterseo_focus_keyphrase', true) ?: '',
                'metamasterseo_facebook_title' => get_post_meta($post->ID, '_metamasterseo_facebook_title', true) ?: '',
                'metamasterseo_facebook_description' => get_post_meta($post->ID, '_metamasterseo_facebook_description', true) ?: '',
                'metamasterseo_facebook_image' => get_post_meta($post->ID, '_metamasterseo_facebook_image', true) ?: '',
                'metamasterseo_twitter_title' => get_post_meta($post->ID, '_metamasterseo_twitter_title', true) ?: '',
                'metamasterseo_twitter_description' => get_post_meta($post->ID, '_metamasterseo_twitter_description', true) ?: '',
                'metamasterseo_twitter_image' => get_post_meta($post->ID, '_metamasterseo_twitter_image', true) ?: '',
                'metamasterseo_allow_search_engines' => get_post_meta($post->ID, '_metamasterseo_allow_search_engines', true) ?: '',
                'metamasterseo_follow_links' => get_post_meta($post->ID, '_metamasterseo_follow_links', true) ?: '',
            );

            $output .= implode(',', array_map('sanitize_text_field', array(
                $post->ID,
                $post->post_title,
                $meta_fields['metamasterseo_page_title'],
                $post->post_name,
                $meta_fields['metamasterseo_slug'],
                $meta_fields['metamasterseo_meta_description'],
                $meta_fields['metamasterseo_breadcrumb_title'],
                $meta_fields['metamasterseo_focus_keyphrase'],
                $meta_fields['metamasterseo_facebook_title'],
                $meta_fields['metamasterseo_facebook_description'],
                $meta_fields['metamasterseo_facebook_image'],
                $meta_fields['metamasterseo_twitter_title'],
                $meta_fields['metamasterseo_twitter_description'],
                $meta_fields['metamasterseo_twitter_image'],
                $meta_fields['metamasterseo_allow_search_engines'],
                $meta_fields['metamasterseo_follow_links']
            ))) . "\n";
        }

        echo esc_textarea($output); // Echo the entire content at once, properly escaped
        exit;
    }
}
add_action('admin_init', 'metamasterseo_export_data');

// Import SEO data
function metamasterseo_import_data() {
    if ( isset( $_POST['import_seo_data'] ) && ! empty($_FILES['import_file']['tmp_name']) ) {
        if ( ! isset( $_POST['metamasterseo_import_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['metamasterseo_import_nonce'] ) ), 'metamasterseo_import_nonce_action' ) ) {
            // Nonce verification failed
            return;
        }

        $file = $_FILES['import_file']['tmp_name'];
        global $wp_filesystem;

        if ( ! WP_Filesystem() ) {
            // Handle error (e.g., display an error message)
            return;
        }

        $file_content = $wp_filesystem->get_contents($file);
        if ( $file_content === false ) {
            error_log('Failed to open import file.');
            return;
        }

        $lines = explode("\n", $file_content);
        $header = str_getcsv(array_shift($lines));

        foreach ($lines as $line) {
            $row = str_getcsv($line);
            if ($row === false || count($row) < 16) continue;

            $post_id = intval($row[0]);
            if ($post_id <= 0) continue;

            $meta_data = array(
                'metamasterseo_page_title' => sanitize_text_field($row[2]),
                'metamasterseo_meta_description' => sanitize_text_field($row[5]),
                'metamasterseo_slug' => sanitize_title($row[4]),
                'metamasterseo_breadcrumb_title' => sanitize_text_field($row[6]),
                'metamasterseo_focus_keyphrase' => sanitize_text_field($row[7]),
                'metamasterseo_facebook_title' => sanitize_text_field($row[8]),
                'metamasterseo_facebook_description' => sanitize_text_field($row[9]),
                'metamasterseo_facebook_image' => esc_url_raw($row[10]),
                'metamasterseo_twitter_title' => sanitize_text_field($row[11]),
                'metamasterseo_twitter_description' => sanitize_text_field($row[12]),
                'metamasterseo_twitter_image' => esc_url_raw($row[13]),
                'metamasterseo_allow_search_engines' => sanitize_text_field($row[14]),
                'metamasterseo_follow_links' => sanitize_text_field($row[15]),
            );

            foreach ($meta_data as $meta_key => $meta_value) {
                if (!empty($meta_value)) {
                    update_post_meta($post_id, '_' . sanitize_text_field($meta_key), $meta_value);
                } else {
                    delete_post_meta($post_id, '_' . sanitize_text_field($meta_key));
                }
            }

            // Update post title if SEO Page Title is provided
            if (!empty($row[2])) {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_title' => sanitize_text_field($row[2]),
                ));
            }

            // Update post slug if SEO Slug is provided
            if (!empty($row[4])) {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_name' => sanitize_title($row[4]),
                ));
            }
        }
    }
}
add_action('admin_init', 'metamasterseo_import_data');
