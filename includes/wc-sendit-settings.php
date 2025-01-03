<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WC_Sendit_Settings {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_options_page(
            __('Sendit Integration Settings', 'woo-sendit-integration'),
            __('Sendit Integration', 'woo-sendit-integration'),
            'manage_options',
            'wc-sendit-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        // Basic API Settings
        register_setting('wc_sendit_settings', 'wc_sendit_enabled');
        register_setting('wc_sendit_settings', 'wc_sendit_api_public_key');
        register_setting('wc_sendit_settings', 'wc_sendit_api_secret_key');

        // Default Order Settings
        register_setting('wc_sendit_settings', 'wc_sendit_default_pickup_district');
        register_setting('wc_sendit_settings', 'wc_sendit_default_district');
        register_setting('wc_sendit_settings', 'wc_sendit_default_comment');
        
        // Order Options
        register_setting('wc_sendit_settings', 'wc_sendit_allow_open');
        register_setting('wc_sendit_settings', 'wc_sendit_allow_try');
        register_setting('wc_sendit_settings', 'wc_sendit_products_from_stock');
        register_setting('wc_sendit_settings', 'wc_sendit_option_exchange');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Sendit Integration Settings', 'woo-sendit-integration'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wc_sendit_settings');
                do_settings_sections('wc_sendit_settings');
                ?>
                
                <h2><?php esc_html_e('API Settings', 'woo-sendit-integration'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Enable Integration', 'woo-sendit-integration'); ?></th>
                        <td>
                            <input type="checkbox" name="wc_sendit_enabled" value="yes" <?php checked(get_option('wc_sendit_enabled'), 'yes'); ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Public Key', 'woo-sendit-integration'); ?></th>
                        <td>
                            <input type="text" name="wc_sendit_api_public_key" value="<?php echo esc_attr(get_option('wc_sendit_api_public_key')); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Secret Key', 'woo-sendit-integration'); ?></th>
                        <td>
                            <input type="text" name="wc_sendit_api_secret_key" value="<?php echo esc_attr(get_option('wc_sendit_api_secret_key')); ?>" class="regular-text" />
                        </td>
                    </tr>
                </table>

                <h2><?php esc_html_e('Default Order Settings', 'woo-sendit-integration'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Default Pickup District', 'woo-sendit-integration'); ?></th>
                        <td>
                            <input type="text" name="wc_sendit_default_pickup_district" value="<?php echo esc_attr(get_option('wc_sendit_default_pickup_district', 'Agadir')); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Default District', 'woo-sendit-integration'); ?></th>
                        <td>
                            <input type="text" name="wc_sendit_default_district" value="<?php echo esc_attr(get_option('wc_sendit_default_district', 'Agadir')); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Default Comment', 'woo-sendit-integration'); ?></th>
                        <td>
                            <textarea name="wc_sendit_default_comment" class="large-text" rows="3"><?php echo esc_textarea(get_option('wc_sendit_default_comment','')); ?></textarea>
                        </td>
                    </tr>
                </table>

                <h2><?php esc_html_e('Order Options', 'woo-sendit-integration'); ?></h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Allow Open', 'woo-sendit-integration'); ?></th>
                        <td>
                            <input type="checkbox" name="wc_sendit_allow_open" value="1" <?php checked(get_option('wc_sendit_allow_open', '1'), '1'); ?> />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Allow Try', 'woo-sendit-integration'); ?></th>
                        <td>
                            <input type="checkbox" name="wc_sendit_allow_try" value="1" <?php checked(get_option('wc_sendit_allow_try', '1'), '1'); ?> />
                        </td>
                    </tr>
                
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}