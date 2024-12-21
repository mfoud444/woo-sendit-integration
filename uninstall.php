<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; 
}

// Clean up plugin data
delete_option('wc_sendit_enabled');
delete_option('wc_sendit_api_public_key');
delete_option('wc_sendit_api_secret_key');
delete_option('wc_sendit_auth_token');
