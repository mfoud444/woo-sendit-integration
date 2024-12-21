<?php
if (!defined('ABSPATH')) {
    exit; 
}

class WC_Sendit_Order_Handler {
    private $api;

    public function __construct() {
        $this->api = new WC_Sendit_API();
        add_action('woocommerce_order_status_completed', [$this, 'handle_order_status_completed']);
    }

    public function handle_order_status_completed($order_id) {
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
        
        // Log the order data
        error_log('Order Data: ' . print_r($order_data, true));
        error_log('create_delivery: ' . print_r($response, true));
        if (is_wp_error($response)) {
            error_log('Sendit API error: ' . $response->get_error_message());
        } else {
            update_post_meta($order_id, '_sendit_delivery_code', $response['data']['code']);
        }
    }
  
    
        private function format_order_data($order) {
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
            $order_number = $order->get_order_number();
        
            // Get the order total
            $order_total = $order->get_total();
            $customer_note = $order->get_customer_note();

        
            // Get the products from the order
            $products = [];
            $product_names = [];
            foreach ($order->get_items() as $item_id => $item) {
                $product = $item->get_product();
                $products[] = [
                    'reference' => $product->get_sku(),
                    'name'      => $product->get_name(),
                    'quantity'  => $item->get_quantity(),
                    'code'      => $product->get_id(),
                ];
                $product_names[] = $product->get_name(); // Collect product names
            }
        
            // Convert product names to a comma-separated string
            $product_names_string = implode(', ', $product_names);
        
            // Get packaging ID (you might need to adjust based on your system)
            $packaging_id = 1; 
        
            // You can replace this with actual district ID, or dynamically determine it based on order data
            $district_id = 1;
        
            // Prepare the actual order data array
            return [
                'pickup_district_id' => 1, 
                'district_id'        => $district_id, 
                'name'               => $billing_first_name . ' ' . $billing_last_name,
                'amount'             => $order_total,
                'address'            => $billing_address_1 . ' ' . $billing_address_2 . ', ' . $billing_city . ', ' . $billing_state . ' ' . $billing_postcode . ', ' . $billing_country,
                'phone'              => $billing_phone,
                'comment'            =>  $customer_note, 
                'reference'          => $order_number,
                'allow_open'         => 1, 
                'allow_try'          => 0, // Adjust based on your business logic
                'products_from_stock'=> 0, // This should be based on your logic or order details
                'products'           =>   $product_names_string, // Actual products in the order
                'product_names'      => $product_names_string, // Added: comma-separated product names
                'packaging_id'       => $packaging_id, // Replace with actual packaging logic if needed
                'option_exchange'    => 0, 
                'delivery_exchange_id'=> '', 
            ];
        }
        
    
}
