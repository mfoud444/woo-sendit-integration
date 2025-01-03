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



    public function get_district_id($city_name) {
        if (!$this->token) {
            return new WP_Error('no_token', __('Authentication token is missing.', 'woo-sendit-integration'));
        }

        $response = wp_remote_get("{$this->api_url}/districts", [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json',
            ],
            'body' => [
                'querystring' => $city_name,
                'page' => 1
            ]
        ]);

 
        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['data']) && is_array($data['data'])) {
            // Find exact or close match
            foreach ($data['data'] as $district) {
                if (strtolower($district['name']) === strtolower($city_name)) {
                    return $district['id'];
                }
            }
            // If no exact match found, return first result
            if (!empty($data['data'][0]['id'])) {
                return $data['data'][0]['id'];
            }
        }

        return new WP_Error('district_not_found', __('District not found.', 'woo-sendit-integration'));
    }

}