
<?php
if (!defined('ABSPATH')) {
    exit;
}

class WC_Sendit_Order_Handler
{
    private $api;

    public function __construct()
    {
        $this->api = new WC_Sendit_API();

        add_action('woocommerce_order_status_completed', [$this, 'handle_order_status_completed']);
    }

    public function handle_order_status_completed($order_id)
    {
        if (get_option('wc_sendit_enabled') !== 'yes') {
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        $public_key = get_option('wc_sendit_api_public_key');
        $secret_key = get_option('wc_sendit_api_secret_key');

        // Authenticate if no token exists
        if (!$this->api->token) {
            $this->api->authenticate($public_key, $secret_key);
        }

        $order_data = $this->format_order_data($order);
        $response = $this->api->create_delivery($order_data);


        if (is_wp_error($response)) {
          
        } else {
            update_post_meta($order_id, '_sendit_delivery_code', $response['data']['code']);
        }
    }

    private function format_order_data($order)
    {
     
        $settings = [
            'pickup_district' => get_option('wc_sendit_default_pickup_district', 'Agadir'),
            'district' => get_option('wc_sendit_default_district', 'Agadir'),
            'comment' => get_option('wc_sendit_default_comment', ''),
            'allow_open' => get_option('wc_sendit_allow_open', '1'),
            'allow_try' => get_option('wc_sendit_allow_try', '1'),
            'products_from_stock' => get_option('wc_sendit_products_from_stock', '0'),
            'option_exchange' => get_option('wc_sendit_option_exchange', '0'),
        ];
     
        // Get the billing and shipping details from the order
        $billing_first_name = $order->get_billing_first_name();
        $billing_last_name = $order->get_billing_last_name();
        $billing_address_1 = $order->get_billing_address_1();
        $billing_address_2 = $order->get_billing_address_2();
        $billing_city = $order->get_billing_city();
        $billing_postcode = $order->get_billing_postcode();
        $billing_country = $order->get_billing_country();
        $billing_state = $order->get_billing_state();
        $billing_phone = $order->get_billing_phone();
        $order_total = $order->get_total();
        $customer_note = $order->get_customer_note();

        // Use default district ID if lookup fails
        $district_id = $this->api->get_district_id($billing_city);
        if (is_wp_error($district_id)) {
         
            $district_id = $this->api->get_district_id($billing_city);
            if (is_wp_error($district_id)) {
                $district_id = 54;
            }
        }
        // Get products
        $products = [];
        $product_names = [];
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();

            // Get product name
            $product_name = $product->get_name();
            $short_name = (strlen($product_name) > 5) ? substr($product_name, 0, 5) : $product_name;
            $size = $product->get_attribute('taille');
            $length = $product->get_attribute('mesurez');
            $color = $product->get_attribute('couleur');
            $product_details = $short_name . '/' . $size . '/' . $length . '/' . $color;
            $product_names[] = $product_details;
        }

      
        $product_names_string = implode(', ', $product_names);

        // Use default pickup district from settings
        $pickup_district_id = $this->api->get_district_id($settings['pickup_district']);
        if (is_wp_error($pickup_district_id)) {
           
            $pickup_district_id = 54;
        }

        return [
            'pickup_district_id' => $pickup_district_id,
            'district_id'        => $district_id,
            'name'               => $billing_first_name . ' ' . $billing_last_name,
            'amount'             => $order_total,
            'address'            => $billing_address_1 . ' ' . $billing_address_2 . ', ' . $billing_city . ', ' . $billing_state . ' ' . $billing_postcode . ', ' . $billing_country,
            'phone'              => $billing_phone,
            'comment'            => $customer_note ?: $settings['comment'],
            'reference'          => '',
            'allow_open'         => $settings['allow_open'],
            'allow_try'          => $settings['allow_try'],
            'products_from_stock' => 0,
            'products'           => $product_names_string,
            'packaging_id'       => 1,
            'option_exchange'    => 0,
            'delivery_exchange_id' => '',
        ];
    }
}