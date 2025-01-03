<div id="top">

<p align="center">
  <img src="logo.jpeg" alt="WooCommerce Sendit Integration" width="300px" height="300px">
</p>

<p align="center">
  <em>Seamlessly integrate WooCommerce with Sendit for automated delivery management.</em>
</p>

<p align="center">
  <a href="https://github.com/your-repo/woocommerce-sendit-integration/actions">
    <img src="https://img.shields.io/github/actions/workflow/status/your-repo/woocommerce-sendit-integration/release-pipeline.yml?logo=githubactions&label=CI&logoColor=white&color=4169E1" alt="Github Actions">
  </a>
  <a href="https://app.codecov.io/gh/your-repo/woocommerce-sendit-integration">
    <img src="https://img.shields.io/codecov/c/github/your-repo/woocommerce-sendit-integration?logo=codecov&logoColor=white&label=Coverage&color=5D4ED3" alt="Test Coverage">
  </a>
  <a href="https://pypi.python.org/pypi/woocommerce-sendit-integration/">
    <img src="https://img.shields.io/pypi/v/woocommerce-sendit-integration?logo=Python&logoColor=white&label=PyPI&color=7934C5" alt="PyPI Version">
  </a>
  <a href="https://www.pepy.tech/projects/woocommerce-sendit-integration">
    <img src="https://img.shields.io/pepy/dt/woocommerce-sendit-integration?logo=PyPI&logoColor=white&label=Downloads&color=9400D3" alt="Total Downloads">
  </a>
  <a href="https://opensource.org/license/mit/">
    <img src="https://img.shields.io/github/license/your-repo/woocommerce-sendit-integration?logo=opensourceinitiative&logoColor=white&label=License&color=8A2BE2" alt="MIT License">
  </a>
</p>

</div>

<img src="https://raw.githubusercontent.com/eli64s/readme-ai/eb2a0b4778c633911303f3c00f87874f398b5180/docs/docs/assets/svg/line-gradient.svg" alt="line break" width="100%" height="3px">

## Quick Links

- [Introduction](#introduction)
- [Features](#features)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [Contributing Guidelines](#contributing)

---

## Introduction

**WooCommerce Sendit Integration** is a powerful WordPress plugin designed to automate order synchronization and delivery management by integrating WooCommerce with the Sendit delivery service. This plugin enables businesses to streamline their operations directly from their WordPress dashboard.

---

## Features

- **Easy API Configuration**: Quick and straightforward setup.  
- **Secure Credential Storage**: Safeguard your sensitive data.  
- **Real-Time Connection Status**: Stay updated with live monitoring.  
- **Automatic Order Synchronization**: Save time and eliminate manual tasks.  
- **Connection Status Monitoring**: Reliable status tracking.

---

## Getting Started

### Requirements

- **WordPress**: Version 5.8 or higher  
- **PHP**: Version 7.4 or higher  
- **WooCommerce**: Version 5.0 or higher  
- **Sendit API Credentials**: Active account with API keys.

### Installation

1. Download the plugin `.zip` file.  
2. Navigate to `WordPress Admin Panel > Plugins > Add New`.  
3. Click `Upload Plugin` and select the downloaded `.zip` file.  
4. Click `Install Now` and then `Activate`.  

---

## Configuration

1. Navigate to `WordPress Admin Panel > Settings > Sendit Integration`.  
2. Enable the integration by selecting the checkbox for "Enable Integration".  
3. Enter your **Sendit API Credentials**:
   - **Public Key**  
   - **Secret Key**  
4. Click `Save Changes`.  
5. Verify your credentials by clicking `Try Login`.

---

## Development

### API Documentation

This plugin integrates with the **Sendit API v1.0.0**. For detailed documentation, visit:  
[https://app.sendit.ma/api/v1/](https://app.sendit.ma/api/v1/)  

#### Authentication Process

- Initial authentication requires **Public** and **Secret Keys**.  
- A token is generated and used for subsequent API requests.  
- The token is refreshed automatically when necessary.  

### Project Structure

```
woo-sendit-integration/
├── assets/
│   ├── css/
│   │   └── admin-style.css
│   └── js/
│       └── admin-script.js
├── includes/
│   ├── wc-sendit-settings.php
│   ├── wc-sendit-api.php
│   └── wc-sendit-order-handler.php
├── index.php
├── uninstall.php
└── woo-sendit-integration.php
```

---

## Security

- Secure storage of API credentials in WordPress options.  
- Encrypted HTTPS communication for all API calls.  
- **Nonce verification** ensures the integrity of form submissions.  
- **Capability checks** protect admin actions from unauthorized access.  

---

## Contributing Guidelines

We welcome contributions! Check out our [contribution guidelines](#) for more details.

---

<p align="right">(<a href="#top">Back to top</a>)</p>
