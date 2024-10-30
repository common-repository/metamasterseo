<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add settings menu
function metamasterseo_add_admin_menu() {
    add_menu_page(
        esc_html__('MetaMasterSEO Settings', 'metamasterseo'),
        esc_html__('MetaMasterSEO', 'metamasterseo'),
        'manage_options',
        'metamasterseo',
        'metamasterseo_settings_page'
    );
}
add_action( 'admin_menu', 'metamasterseo_add_admin_menu' );

// Register settings
function metamasterseo_register_settings() {
    // General Settings
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_meta_title' );
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_meta_description' );
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_meta_keywords' );

    // Sitemap Settings
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_generate_sitemap' );
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_sitemap_post_types' );

    // Verification Codes
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_google_verification' );
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_google_analytics' );
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_bing_verification' );
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_yandex_verification' );
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_facebook_app_id' );

    // Advanced Settings
    register_setting( 'metamasterseo_settings_group', 'metamasterseo_nofollow_links' );
}
add_action( 'admin_init', 'metamasterseo_register_settings' );

// Settings page content
function metamasterseo_settings_page() {
    $post_types = metamasterseo_get_post_types();
    ?>
    <div class="admin-wrap">
        <h1><?php echo esc_html__('MetaMasterSEO Settings', 'metamasterseo'); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active" data-target="general"><?php echo esc_html__('General', 'metamasterseo'); ?></a>
            <a href="#sitemap" class="nav-tab" data-target="sitemap"><?php echo esc_html__('Sitemap', 'metamasterseo'); ?></a>
            <a href="#analytics" class="nav-tab" data-target="analytics"><?php echo esc_html__('Analytics', 'metamasterseo'); ?></a>
            <a href="#import" class="nav-tab" data-target="import"><?php echo esc_html__('Import/Export SEO Data', 'metamasterseo'); ?></a>
            <a href="#advanced" class="nav-tab" data-target="advanced"><?php echo esc_html__('Advanced Settings', 'metamasterseo'); ?></a>
            <a href="#xml-sitemap" class="nav-tab" data-target="xml-sitemap"><?php echo esc_html__('XML Sitemap', 'metamasterseo'); ?></a>
        </h2>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php settings_fields( 'metamasterseo_settings_group' ); ?>
            <?php do_settings_sections( 'metamasterseo_settings_group' ); ?>

            <div id="general" class="tab-content active">
                <h2><?php echo esc_html__('General Settings', 'metamasterseo'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Default Meta Title', 'metamasterseo'); ?></th>
                        <td><input type="text" name="metamasterseo_meta_title" value="<?php echo esc_attr( get_option('metamasterseo_meta_title') ); ?>" style="width: 100%;" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Default Meta Description', 'metamasterseo'); ?></th>
                        <td><textarea name="metamasterseo_meta_description" style="width: 100%;"><?php echo esc_textarea( get_option('metamasterseo_meta_description') ); ?></textarea></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Default Meta Keywords', 'metamasterseo'); ?></th>
                        <td><input type="text" name="metamasterseo_meta_keywords" value="<?php echo esc_attr( get_option('metamasterseo_meta_keywords') ); ?>" style="width: 100%;" /></td>
                    </tr>
                </table>
            </div>

            <div id="sitemap" class="tab-content">
                <h2><?php echo esc_html__('Sitemap Settings', 'metamasterseo'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Generate a Sitemap?', 'metamasterseo'); ?></th>
                        <td>
                            <input type="checkbox" name="metamasterseo_generate_sitemap" value="1" <?php checked(1, get_option('metamasterseo_generate_sitemap'), true); ?> />
                            <?php if ( get_option('metamasterseo_generate_sitemap') ) : ?>
                                <br><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank"><?php echo esc_html__('View Sitemap', 'metamasterseo'); ?></a>
                                <br><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" download="sitemap.xml" class="button"><?php echo esc_html__('Download Sitemap', 'metamasterseo'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Sitemap Post Types', 'metamasterseo'); ?></th>
                        <td>
                            <?php foreach ( $post_types as $post_type ) : ?>
                                <label>
                                    <input type="checkbox" name="metamasterseo_sitemap_post_types[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php checked( in_array( $post_type->name, (array) get_option('metamasterseo_sitemap_post_types') ) ); ?> />
                                    <?php echo esc_html( $post_type->label ); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="analytics" class="tab-content">
                <h2><?php echo esc_html__('Analytics', 'metamasterseo'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Google Verification Code', 'metamasterseo'); ?></th>
                        <td><input type="text" name="metamasterseo_google_verification" value="<?php echo esc_attr( get_option('metamasterseo_google_verification') ); ?>" style="width: 100%;" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Google Analytics 4', 'metamasterseo'); ?></th>
                        <td><input type="text" name="metamasterseo_google_analytics" value="<?php echo esc_attr( get_option('metamasterseo_google_analytics') ); ?>" style="width: 100%;" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Bing Verification Code', 'metamasterseo'); ?></th>
                        <td><input type="text" name="metamasterseo_bing_verification" value="<?php echo esc_attr( get_option('metamasterseo_bing_verification') ); ?>" style="width: 100%;" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Yandex Verification Code', 'metamasterseo'); ?></th>
                        <td><input type="text" name="metamasterseo_yandex_verification" value="<?php echo esc_attr( get_option('metamasterseo_yandex_verification') ); ?>" style="width: 100%;" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Facebook App ID', 'metamasterseo'); ?></th>
                        <td><input type="text" name="metamasterseo_facebook_app_id" value="<?php echo esc_attr( get_option('metamasterseo_facebook_app_id') ); ?>" style="width: 100%;" /></td>
                    </tr>
                </table>
            </div>

            <div id="import" class="tab-content">
                <h2><?php echo esc_html__('Import/Export SEO Data', 'metamasterseo'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Import SEO Data', 'metamasterseo'); ?></th>
                        <td>
                            <input type="file" name="import_file" accept=".csv" />
                            <button type="submit" name="import_seo_data" class="button"><?php echo esc_html__('Import SEO Data', 'metamasterseo'); ?></button>
                            <?php wp_nonce_field('metamasterseo_import_nonce_action', 'metamasterseo_import_nonce'); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Export SEO Data', 'metamasterseo'); ?></th>
                        <td>
                            <form method="post" action="">
                                <?php wp_nonce_field('metamasterseo_export_nonce_action', 'metamasterseo_export_nonce'); ?>
                                <button type="submit" name="export_seo_data" class="button"><?php echo esc_html__('Export SEO Data', 'metamasterseo'); ?></button>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="advanced" class="tab-content">
                <h2><?php echo esc_html__('Advanced Settings', 'metamasterseo'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Open External Links in New Tab with Nofollow?', 'metamasterseo'); ?></th>
                        <td>
                            <input type="checkbox" name="metamasterseo_nofollow_links" value="1" <?php checked(1, get_option('metamasterseo_nofollow_links'), true); ?> />
                        </td>
                    </tr>
                </table>
            </div>

            <div id="xml-sitemap" class="tab-content">
                <h2><?php echo esc_html__('XML Sitemap', 'metamasterseo'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php echo esc_html__('Generate a Sitemap?', 'metamasterseo'); ?></th>
                        <td>
                            <input type="checkbox" name="metamasterseo_generate_sitemap" value="1" <?php checked(1, get_option('metamasterseo_generate_sitemap'), true); ?> />
                            <?php if ( get_option('metamasterseo_generate_sitemap') ) : ?>
                                <br><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank"><?php echo esc_html__('View Sitemap', 'metamasterseo'); ?></a>
                                <br><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" download="sitemap.xml" class="button"><?php echo esc_html__('Download Sitemap', 'metamasterseo'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Get all post types for sitemap options
function metamasterseo_get_post_types() {
    $args = array(
        'public' => true
    );
    $output = 'objects';
    $post_types = get_post_types( $args, $output );

    return $post_types;
}
?>
