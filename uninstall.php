<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit; 
}

// Basic API Settings
delete_option('wc_sendit_enabled');
delete_option('wc_sendit_api_public_key');
delete_option('wc_sendit_api_secret_key');
delete_option('wc_sendit_auth_token');
