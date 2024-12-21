<?php
if (!defined('ABSPATH')) {
    exit; 
}

class WC_Sendit_API {
    private $api_url = 'https://app.sendit.ma/api/v1';
    public $token;

    public function __construct() {
        $this->token = get_option('wc_sendit_auth_token');
    }

    public function authenticate($public_key, $secret_key) {
        $response = wp_remote_post("{$this->api_url}/login", [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => wp_json_encode([
                'public_key' => $public_key,
                'secret_key' => $secret_key,
            ]),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['success']) && $data['success']) {
            $this->token = $data['data']['token'];
            update_option('wc_sendit_auth_token', $this->token);
            return true;
        }

        return new WP_Error('auth_failed', __('Authentication failed.', 'woo-sendit-integration'));
    }

    public function create_delivery($order_data) {
        if (!$this->token) {
            return new WP_Error('no_token', __('Authentication token is missing.', 'woo-sendit-integration'));
        }

        $response = wp_remote_post("{$this->api_url}/deliveries", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json',
            ],
            'body'    => wp_json_encode($order_data),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['error'])) {
            return new WP_Error('delivery_error', $data['error']);
        }

        return $data;
    }
}
