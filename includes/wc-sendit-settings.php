<?php
if (!defined('ABSPATH')) {
    exit; 
}

class WC_Sendit_Settings {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
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

    public function enqueue_styles() {
        wp_enqueue_style('sendit-integration-css', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    }

    public function render_settings_page() {
        $is_enabled = get_option('wc_sendit_enabled') === 'yes';
        
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['try_login'])) {
                check_admin_referer('wc_sendit_settings-options');
                $this->try_login();
            }
        }
        ?>
        <div class="wrap">
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
                            <input type="checkbox" 
                                   name="wc_sendit_enabled" 
                                   value="yes" 
                                   <?php checked($is_enabled); ?> />
                        </td>
                    </tr>

                    <?php if ($is_enabled): ?>
                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Connection Status', 'woo-sendit-integration'); ?></th>
                            <td>
                                <div class="sendit-connection-row">
                                    <div class="sendit-status <?php echo $api_status['success'] ? 'success' : 'error'; ?>">
                                        <span class="dashicons dashicons-<?php echo $api_status['success'] ? 'yes-alt' : 'warning'; ?>"></span>
                                        <?php 
                                        if ($api_status['success']) {
                                            echo sprintf(esc_html__('Connected as: %s', 'woo-sendit-integration'), $api_status['name']);
                                        } else {
                                            echo esc_html__('Not Connected - Unauthorized access to User', 'woo-sendit-integration');
                                        }
                                        ?>
                                    </div>
                                    <?php if (!$api_status['success']): ?>
                                        <button type="submit" 
                                                name="try_login" 
                                                class="button button-secondary">
                                            <?php esc_html_e('Try Login', 'woo-sendit-integration'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Public Key', 'woo-sendit-integration'); ?></th>
                            <td>
                                <input type="text" 
                                       name="wc_sendit_api_public_key" 
                                       value="<?php echo esc_attr($public_key); ?>" 
                                       class="regular-text" />
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php esc_html_e('Secret Key', 'woo-sendit-integration'); ?></th>
                            <td>
                                <div class="password-container">
                                    <input type="password" 
                                           id="wc_sendit_api_secret_key"
                                           name="wc_sendit_api_secret_key" 
                                           autocomplete="current-password"
                                           value="<?php echo esc_attr($secret_key); ?>" 
                                           class="regular-text" />
                                    <span class="toggle-password dashicons dashicons-visibility" 
                                          aria-label="<?php esc_attr_e('Toggle password visibility', 'woo-sendit-integration'); ?>">
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>

                <?php submit_button(__('Save Changes', 'woo-sendit-integration')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Handle disconnect action
     */
    private function handle_disconnect() {
        // Clear stored credentials
        update_option('wc_sendit_api_public_key', '');
        update_option('wc_sendit_api_secret_key', '');
        
        add_settings_error(
            'sendit_messages',
            'sendit_disconnect_success',
            __('Successfully disconnected from Sendit API.', 'woo-sendit-integration'),
            'success'
        );
    }

    /**
     * Handle the try login attempt
     */
    private function try_login() {
        $api_status = $this->check_api_connection();
        
        if ($api_status['success']) {
            add_settings_error(
                'wc_sendit_settings',
                'sendit_login_success',
                sprintf(
                    __('Successfully connected to Sendit API. Account: %s', 'woo-sendit-integration'),
                    $api_status['name']
                ),
                'success'
            );
        } else {
            add_settings_error(
                'wc_sendit_settings',
                'sendit_login_error',
                sprintf(
                    __('Connection failed: %s', 'woo-sendit-integration'),
                    $api_status['message']
                ),
                'error'
            );
        }
    }

    private function check_api_login_status() {
        // Retrieve stored public and secret keys
        $public_key = get_option('wc_sendit_api_public_key');
        $secret_key = get_option('wc_sendit_api_secret_key');
        
        // If no keys, we cannot authenticate
        if (empty($public_key) || empty($secret_key)) {
            return false;
        }

        // If we have keys, attempt authentication
        $api = new WC_Sendit_API();
        $api->authenticate($public_key, $secret_key);

        // Check if authentication was successful
        return !empty($api->token);
    }

    // Handle login action
    public function handle_login() {
        if (isset($_POST['wc_sendit_login']) && check_admin_referer('wc_sendit_login')) {
            $public_key = get_option('wc_sendit_api_public_key');
            $secret_key = get_option('wc_sendit_api_secret_key');

            // Attempt to authenticate
            $api = new WC_Sendit_API();
            $api->authenticate($public_key, $secret_key);

            if ($api->token) {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success"><p>Successfully logged in to Sendit API.</p></div>';
                });
            } else {
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error"><p>Failed to login to Sendit API. Check your API keys.</p></div>';
                });
            }
        }
    }

    // Handle logout action
    public function handle_logout() {
        if (isset($_POST['wc_sendit_logout']) && check_admin_referer('wc_sendit_logout')) {
            
            delete_option('wc_sendit_token');
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Successfully logged out from Sendit API.</p></div>';
            });
        }
    }

    private function check_api_connection() {
        $public_key = get_option('wc_sendit_api_public_key');
        $secret_key = get_option('wc_sendit_api_secret_key');

        if (empty($public_key) || empty($secret_key)) {
            return array(
                'success' => false,
                'message' => __('API credentials not configured', 'woo-sendit-integration')
            );
        }

        // Make API request to check connection
        $response = wp_remote_post('https://app.sendit.ma/api/v1/login', array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'public_key' => $public_key,
                'secret_key' => $secret_key
            ))
        ));

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => $response->get_error_message()
            );
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!empty($body['success']) && !empty($body['data']['name'])) {
            return array(
                'success' => true,
                'name' => $body['data']['name']
            );
        }

        return array(
            'success' => false,
            'message' => !empty($body['message']) ? $body['message'] : __('Unknown error', 'woo-sendit-integration')
        );
    }
}

