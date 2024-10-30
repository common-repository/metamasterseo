<?php
/*
Plugin Name: Meta Master SEO
Description: A comprehensive SEO plugin for WordPress that provides meta data management, sitemap generation, and more.
Version: 1.2
Author: Weblogix Soft Team
Author URI: https://weblogixsoft.com
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin path
define( 'METAMASTERSEO_PATH', plugin_dir_path( __FILE__ ) );

// Include necessary files
require_once METAMASTERSEO_PATH . 'includes/admin-page.php';
require_once METAMASTERSEO_PATH . 'includes/sitemap.php';
require_once METAMASTERSEO_PATH . 'includes/import-export.php';
require_once METAMASTERSEO_PATH . 'includes/class-metamasterseo-meta-box.php';

// Enqueue admin styles and scripts
function metamasterseo_enqueue_admin_assets( $hook ) {
    if ( 'post.php' === $hook || 'post-new.php' === $hook || strpos($hook, 'metamasterseo') !== false ) {
        wp_enqueue_style( 'metamasterseo-admin-styles', plugins_url( 'css/admin-styles.css', __FILE__ ) );
        wp_enqueue_script( 'metamasterseo-admin-scripts', plugins_url( 'js/admin-scripts.js', __FILE__ ), array('jquery'), null, true );
        wp_enqueue_script( 'metamasterseo-seo-script', plugins_url( 'js/metamaster-seo.js', __FILE__ ), array( 'jquery' ), '1.0', true );
        wp_enqueue_style( 'metamasterseo-seo-style', plugins_url( 'css/metamaster-seo.css', __FILE__ ) );
    }
}
add_action( 'admin_enqueue_scripts', 'metamasterseo_enqueue_admin_assets' );

// Initialize the plugin
function metamasterseo_seo_init() {
    MetaMasterSEO_Meta_Box::init();
}
add_action( 'plugins_loaded', 'metamasterseo_seo_init' );

// Output meta data in the homepage header
function metamasterseo_homepage_meta_tags() {
    if ( is_front_page() ) {
        $meta_title = esc_attr( get_option( 'metamasterseo_meta_title' ) );
        $meta_description = esc_attr( get_option( 'metamasterseo_meta_description' ) );
        $meta_keywords = esc_attr( get_option( 'metamasterseo_meta_keywords' ) );

        if ( ! empty( $meta_title ) ) {
            echo '<title>' . esc_html( $meta_title ) . '</title>' . PHP_EOL;
        }
        if ( ! empty( $meta_description ) ) {
            echo '<meta name="description" content="' . esc_attr( $meta_description ) . '">' . PHP_EOL;
        }
        if ( ! empty( $meta_keywords ) ) {
            echo '<meta name="keywords" content="' . esc_attr( $meta_keywords ) . '">' . PHP_EOL;
        }

        // Verification codes
        $google_verification = esc_attr( get_option( 'metamasterseo_google_verification' ) );
        $bing_verification = esc_attr( get_option( 'metamasterseo_bing_verification' ) );
        $yandex_verification = esc_attr( get_option( 'metamasterseo_yandex_verification' ) );

        if ( ! empty( $google_verification ) ) {
            echo '<meta name="google-site-verification" content="' . esc_attr( $google_verification ) . '">' . PHP_EOL;
        }
        if ( ! empty( $bing_verification ) ) {
            echo '<meta name="msvalidate.01" content="' . esc_attr( $bing_verification ) . '">' . PHP_EOL;
        }
        if ( ! empty( $yandex_verification ) ) {
            echo '<meta name="yandex-verification" content="' . esc_attr( $yandex_verification ) . '">' . PHP_EOL;
        }

        // Facebook App ID
        $facebook_app_id = esc_attr( get_option( 'metamasterseo_facebook_app_id' ) );
        if ( ! empty( $facebook_app_id ) ) {
            echo '<meta property="fb:app_id" content="' . esc_attr( $facebook_app_id ) . '">' . PHP_EOL;
        }
    }
}
add_action( 'wp_head', 'metamasterseo_homepage_meta_tags', 1 );

// Modify links to open in a new tab with nofollow if the setting is enabled
function metamasterseo_modify_links( $content ) {
    if ( get_option( 'metamasterseo_nofollow_links' ) ) {
        $pattern = '/<a(.*?)href="http(s)?:\/\/(.*?)"(.*?)>/i';
        $replacement = '<a$1href="http$2://$3" target="_blank" rel="nofollow"$4>';
        $content = preg_replace( $pattern, $replacement, $content );
    }
    return $content;
}
add_filter( 'the_content', 'metamasterseo_modify_links' );

// Overwrite default page title, meta description, and add meta tags
function metamasterseo_filter_wp_title( $title ) {
    if ( is_singular() ) {
        global $post;
        $custom_title = get_post_meta( $post->ID, '_metamasterseo_page_title', true );
        if ( $custom_title ) {
            return esc_html( $custom_title );
        }
    }
    return $title;
}
add_filter( 'pre_get_document_title', 'metamasterseo_filter_wp_title' );

function metamasterseo_add_meta_tags() {
    if ( is_singular() ) {
        global $post;
        $meta_description = get_post_meta( $post->ID, '_metamasterseo_meta_description', true );
        $focus_keyphrase = get_post_meta( $post->ID, '_metamasterseo_focus_keyphrase', true );
        if ( $meta_description ) {
            echo '<meta name="description" content="' . esc_attr( $meta_description ) . '">' . "\n";
        }
        if ( $focus_keyphrase ) {
            echo '<meta name="keywords" content="' . esc_attr( $focus_keyphrase ) . '">' . "\n";
        }
    }
}
add_action( 'wp_head', 'metamasterseo_add_meta_tags' );
