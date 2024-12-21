# WooCommerce Sendit Integration

A WordPress plugin that integrates WooCommerce with Sendit delivery service for automatic order processing.

## Description

This plugin provides seamless integration between WooCommerce and Sendit delivery service. It allows automatic order synchronization and delivery management directly from your WordPress dashboard.

## Features

- Easy API configuration
- Secure credential storage
- Real-time connection status
- Automatic order synchronization
- Connection status monitoring

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- WooCommerce 5.0 or higher
- Active Sendit account with API credentials

## Installation

1. Download the plugin zip file
2. Go to WordPress admin panel > Plugins > Add New
3. Click "Upload Plugin" and choose the downloaded zip file
4. Click "Install Now"
5. After installation, click "Activate"

## Configuration

1. Go to WordPress admin panel > Settings > Sendit Integration
2. Enable the integration by checking "Enable Integration"
3. Enter your Sendit API credentials:
   - Public Key
   - Secret Key
4. Click "Save Changes"
5. Click "Try Login" to verify your credentials

## API Documentation

The plugin integrates with Sendit API v1.0.0. For detailed API documentation, visit:
`https://app.sendit.ma/api/v1/`

### Authentication

The plugin uses token-based authentication:
1. Initial authentication using Public/Secret keys
2. Subsequent requests use the obtained token
3. Token is automatically refreshed when needed

## Development

### Project Structure 

```
    woo-sendit-integration/
    ├── assets/
    │ ├── css/
    │ │ └── admin-style.css
    │ └── js/
    │ └── admin-script.js
    ├── includes/
    │ ├── wc-sendit-settings.php
    │ ├── wc-sendit-api.php
    │ └── wc-sendit-order-handler.php
    ├── index.php
    ├── uninstall.php
    └── woo-sendit-integration.php
```

## Security

- API credentials are stored securely in WordPress options
- All API communications use HTTPS
- Password fields are properly masked
- Nonce verification for form submissions
- Capability checks for admin actions

