<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MetaMasterSEO_Meta_Box {

    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_box'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
    }

    public static function enqueue_scripts() {
        wp_enqueue_script('metamasterseo-seo-scripts', plugin_dir_url(__FILE__) . 'js/metamasterseo-seo.js', array('jquery', 'jquery-ui-tabs'), '1.0', true);
        wp_enqueue_media();
        wp_enqueue_style('metamasterseo-seo-styles', plugin_dir_url(__FILE__) . 'css/metamasterseo-seo.css');
        wp_enqueue_style('wp-jquery-ui-dialog');
    }

    public static function add_meta_boxes() {
        $post_types = get_post_types(array('public' => true));
        foreach ($post_types as $post_type) {
            add_meta_box(
                'metamasterseo_seo_meta_box',
                esc_html__('MetaMasterSEO Settings', 'metamasterseo'),
                array(__CLASS__, 'render_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }

    public static function render_meta_box($post) {
        // Add nonce for security and authentication
        wp_nonce_field('metamasterseo_seo_meta_box_nonce_action', 'metamasterseo_seo_meta_box_nonce');

        // Retrieve current values
        $meta_fields = array(
            'metamasterseo_page_title' => '',
            'metamasterseo_meta_description' => '',
            'metamasterseo_slug' => '',
            'metamasterseo_breadcrumb_title' => '',
            'metamasterseo_focus_keyphrase' => '',
            'metamasterseo_facebook_title' => '',
            'metamasterseo_facebook_description' => '',
            'metamasterseo_facebook_image' => '',
            'metamasterseo_twitter_title' => '',
            'metamasterseo_twitter_description' => '',
            'metamasterseo_twitter_image' => '',
            'metamasterseo_allow_search_engines' => '',
            'metamasterseo_follow_links' => ''
        );

        foreach ($meta_fields as $field => $value) {
            $meta_fields[$field] = get_post_meta($post->ID, '_' . sanitize_text_field($field), true);
        }

        // Render tab structure
        echo '<div id="metamasterseo-tabs">';
        echo '<ul>';
        echo '<li><a href="#metamasterseo-tab-seo">' . esc_html__('SEO', 'metamasterseo') . '</a></li>';
        echo '<li><a href="#metamasterseo-tab-social">' . esc_html__('Social', 'metamasterseo') . '</a></li>';
        echo '<li><a href="#metamasterseo-tab-advanced">' . esc_html__('Advanced', 'metamasterseo') . '</a></li>';
        echo '</ul>';

        // Render SEO tab
        echo '<div id="metamasterseo-tab-seo">';
        echo '<div class="metamasterseo-preview" id="metamasterseo-google-preview"></div>';
        echo '<table class="form-table">';
        self::render_text_field('Page Title', 'metamasterseo_page_title', esc_attr($meta_fields['metamasterseo_page_title']));
        self::render_textarea_field('Meta Description', 'metamasterseo_meta_description', esc_textarea($meta_fields['metamasterseo_meta_description']));
        self::render_text_field('Slug', 'metamasterseo_slug', esc_attr($meta_fields['metamasterseo_slug']));
        self::render_text_field('Breadcrumb Title', 'metamasterseo_breadcrumb_title', esc_attr($meta_fields['metamasterseo_breadcrumb_title']));
        self::render_text_field('Focus Keyphrase', 'metamasterseo_focus_keyphrase', esc_attr($meta_fields['metamasterseo_focus_keyphrase']));
        echo '</table>';
        echo '<div id="metamasterseo-content-analyzer"></div>';
        echo '</div>';

        // Render Social tab
        echo '<div id="metamasterseo-tab-social">';
        echo '<div class="metamasterseo-preview" id="metamasterseo-facebook-preview"></div>';
        echo '<div class="metamasterseo-preview" id="metamasterseo-twitter-preview"></div>';
        echo '<table class="form-table">';
        self::render_text_field('Facebook Title', 'metamasterseo_facebook_title', esc_attr($meta_fields['metamasterseo_facebook_title']));
        self::render_textarea_field('Facebook Description', 'metamasterseo_facebook_description', esc_textarea($meta_fields['metamasterseo_facebook_description']));
        self::render_image_field('Facebook Image', 'metamasterseo_facebook_image', esc_url($meta_fields['metamasterseo_facebook_image']));
        self::render_text_field('Twitter Title', 'metamasterseo_twitter_title', esc_attr($meta_fields['metamasterseo_twitter_title']));
        self::render_textarea_field('Twitter Description', 'metamasterseo_twitter_description', esc_textarea($meta_fields['metamasterseo_twitter_description']));
        self::render_image_field('Twitter Image', 'metamasterseo_twitter_image', esc_url($meta_fields['metamasterseo_twitter_image']));
        echo '</table>';
        echo '</div>';

        // Render Advanced tab
        echo '<div id="metamasterseo-tab-advanced">';
        echo '<table class="form-table">';
        self::render_select_field('Allow search engines to show this Page in search results?', 'metamasterseo_allow_search_engines', esc_attr($meta_fields['metamasterseo_allow_search_engines']));
        self::render_select_field('Should search engines follow links on this Page?', 'metamasterseo_follow_links', esc_attr($meta_fields['metamasterseo_follow_links']));
        echo '</table>';
        echo '</div>';

        echo '</div>'; // Close metamasterseo-tabs
    }

    private static function render_text_field($label, $name, $value) {
        echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th>';
        echo '<td>';
        echo '<input type="text" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" class="metamasterseo-full-width">';
        echo '<span id="' . esc_attr($name) . '_indicator" class="metamasterseo-indicator"></span>';
        echo '</td></tr>';
    }

    private static function render_textarea_field($label, $name, $value) {
        echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th>';
        echo '<td>';
        echo '<textarea id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" class="metamasterseo-full-width">' . esc_textarea($value) . '</textarea>';
        echo '<span id="' . esc_attr($name) . '_indicator" class="metamasterseo-indicator"></span>';
        echo '</td></tr>';
    }

    private static function render_select_field($label, $name, $value) {
        echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th>';
        echo '<td>';
        echo '<select id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" class="metamasterseo-full-width">';
        echo '<option value="yes"' . selected($value, 'yes', false) . '>Yes</option>';
        echo '<option value="no"' . selected($value, 'no', false) . '>No</option>';
        echo '</select>';
        echo '</td></tr>';
    }

    private static function render_image_field($label, $name, $value) {
        echo '<tr><th><label for="' . esc_attr($name) . '">' . esc_html($label) . '</label></th>';
        echo '<td>';
        echo '<input type="hidden" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '">';
        echo '<img id="' . esc_attr($name) . '_preview" src="' . esc_attr($value) . '" style="max-width:150px; margin-bottom:10px; display:' . ($value ? 'block' : 'none') . ';">';
        echo '<button type="button" class="upload_image_button button">' . esc_html__('Upload Image', 'metamasterseo') . '</button>';
        echo '</td></tr>';
    }

    public static function save_meta_box($post_id) {
        // Verify nonce
        if ( ! isset( $_POST['metamasterseo_seo_meta_box_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['metamasterseo_seo_meta_box_nonce'] ) ), 'metamasterseo_seo_meta_box_nonce_action' ) ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!isset($_POST['post_type'])) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save fields
        self::save_meta_field($post_id, 'metamasterseo_page_title');
        self::save_meta_field($post_id, 'metamasterseo_meta_description');
        self::save_meta_field($post_id, 'metamasterseo_slug');
        self::save_meta_field($post_id, 'metamasterseo_breadcrumb_title');
        self::save_meta_field($post_id, 'metamasterseo_focus_keyphrase');
        self::save_meta_field($post_id, 'metamasterseo_facebook_title');
        self::save_meta_field($post_id, 'metamasterseo_facebook_description');
        self::save_meta_field($post_id, 'metamasterseo_facebook_image');
        self::save_meta_field($post_id, 'metamasterseo_twitter_title');
        self::save_meta_field($post_id, 'metamasterseo_twitter_description');
        self::save_meta_field($post_id, 'metamasterseo_twitter_image');
        self::save_meta_field($post_id, 'metamasterseo_allow_search_engines');
        self::save_meta_field($post_id, 'metamasterseo_follow_links');

        // Save custom slug
        if (isset($_POST['metamasterseo_slug'])) {
            $new_slug = sanitize_title($_POST['metamasterseo_slug']);
            if (!empty($new_slug)) {
                remove_action('save_post', array(__CLASS__, 'save_meta_box')); // Unhook to prevent infinite loop
                wp_update_post(
                    array(
                        'ID'        => $post_id,
                        'post_name' => $new_slug,
                    )
                );
                add_action('save_post', array(__CLASS__, 'save_meta_box')); // Rehook after updating
            }
        }
    }

    private static function save_meta_field($post_id, $name) {
        if (isset($_POST[$name])) {
            update_post_meta($post_id, '_' . sanitize_text_field($name), sanitize_text_field($_POST[$name]));
        } else {
            delete_post_meta($post_id, '_' . sanitize_text_field($name));
        }
    }
}

MetaMasterSEO_Meta_Box::init();

?>
