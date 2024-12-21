
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
        register_setting('wc_sendit_settings', 'wc_sendit_enabled');
        register_setting('wc_sendit_settings', 'wc_sendit_api_public_key');
        register_setting('wc_sendit_settings', 'wc_sendit_api_secret_key');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
        <?php echo esc_attr(get_option('wc_sendit_auth_token')); ?>
            <h1><?php esc_html_e('Sendit Integration Settings', 'woo-sendit-integration'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wc_sendit_settings');
                do_settings_sections('wc_sendit_settings');
                ?>
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
                            <input type="text" name="wc_sendit_api_public_key" value="<?php echo esc_attr(get_option('wc_sendit_api_public_key')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Secret Key', 'woo-sendit-integration'); ?></th>
                        <td>
                            <input type="text" name="wc_sendit_api_secret_key" value="<?php echo esc_attr(get_option('wc_sendit_api_secret_key')); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}