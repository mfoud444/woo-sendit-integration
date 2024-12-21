<?php
/**
 * Plugin Name: WooCommerce Sendit Integration
 * Description: Integrates WooCommerce with Sendit delivery service for automatic order processing and seamless API integration.
 * Version: 1.0.0
 * Author: Mohammed Foud Mohammed Ali
 * Author URI: https://yourwebsite.com
* Author Email: mfoud444@gmail.com
 * Author Phone: +967714589027
 * Text Domain: woo-sendit-integration
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Woo_Sendit_Integration {

    const VERSION = '1.0.0';

    public function __construct() {

        $this->define_constants();
        if (!$this->check_woocommerce()) {
            return;
        }
        $this->includes();
        $this->init_hooks();
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

     /**
     * Check if WooCommerce is active
     */
    private function check_woocommerce() {
        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            add_action('admin_notices', function() {
                echo '<div class="error"><p>';
                echo esc_html__('WooCommerce Sendit Integration requires WooCommerce to be installed and activated.', 'woo-sendit-integration');
                echo '</p></div>';
            });
            return false;
        }
        return true;
    }
    private function define_constants() {
        define('WSI_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('WSI_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('WSI_PLUGIN_VERSION', self::VERSION);
    }

    private function includes() {
        require_once WSI_PLUGIN_PATH . 'includes/wc-sendit-settings.php';
        require_once WSI_PLUGIN_PATH . 'includes/wc-sendit-api.php';
        require_once WSI_PLUGIN_PATH . 'includes/wc-sendit-order-handler.php';
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
        add_action('plugins_loaded', [$this, 'init_plugin']);
    }

    public function activate() {
        // Code to run on activation
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('WooCommerce is required to use this plugin.', 'woo-sendit-integration'));
        }
    }

    public function deactivate() {
        // Code to run on deactivation
    }

    public function init_plugin() {
        if (class_exists('WooCommerce')) {
            new WC_Sendit_Settings();
            new WC_Sendit_Order_Handler();
        }
    }

    public function enqueue_admin_assets($hook) {
        // Only load on plugin settings page
        if ('settings_page_wc-sendit-settings' !== $hook) {
            return;
        }

        // Correct way to get plugin directory URL
        $plugin_url = plugin_dir_url(__FILE__);

        // Enqueue CSS
        wp_enqueue_style(
            'wc-sendit-admin-style',
            $plugin_url . 'assets/css/admin-style.css',
            array(),
            '1.0.0'
        );

       
        wp_enqueue_script(
            'wc-sendit-admin-script',
            $plugin_url . 'assets/js/admin-script.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Ensure dashicons are loaded
        wp_enqueue_style('dashicons');
    }
}

new Woo_Sendit_Integration();
