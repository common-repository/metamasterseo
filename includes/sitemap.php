<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Generate Sitemap
function metamasterseo_generate_sitemap() {
    if ( get_option( 'metamasterseo_generate_sitemap' ) ) {
        $post_types = get_option( 'metamasterseo_sitemap_post_types', array() );

        $posts = get_posts( array(
            'post_type' => array_map( 'sanitize_text_field', $post_types ),
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ) );

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ( $posts as $post ) {
            setup_postdata( $post );
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . esc_url( get_permalink( $post->ID ) ) . '</loc>';
            $sitemap .= '<lastmod>' . esc_html( get_the_modified_date( 'c', $post->ID ) ) . '</lastmod>';
            $sitemap .= '<changefreq>monthly</changefreq>';
            $sitemap .= '<priority>0.8</priority>';
            $sitemap .= '</url>';
        }

        $sitemap .= '</urlset>';

        // Use WP_Filesystem API to write the sitemap file
        if ( ! function_exists( 'WP_Filesystem' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        global $wp_filesystem;
        WP_Filesystem();

        $sitemap_file = ABSPATH . 'sitemap.xml';

        if ( ! $wp_filesystem->put_contents( $sitemap_file, wp_kses_post($sitemap), FS_CHMOD_FILE ) ) {
            // Handle error
            error_log( 'Failed to write sitemap.xml' );
        }

        wp_reset_postdata();
    }
}
add_action( 'publish_post', 'metamasterseo_generate_sitemap' );
add_action( 'save_post', 'metamasterseo_generate_sitemap' );
?>
