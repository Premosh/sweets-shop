<?php
// config.php — local configuration for Sweets Shop (rollback of hosting changes)
// Keep this file in version control for local dev; change ADMIN_PASSWORD to secure admin area.
if (!defined('ADMIN_PASSWORD')) define('ADMIN_PASSWORD', 'admin123');

// Site URL used for redirects and callbacks during local development
$siteUrl = 'http://localhost:8000';

// eSewa Integration Settings (test defaults)
if (!defined('ESEWA_MERCHANT_ID')) {
    define('ESEWA_MERCHANT_ID', 'JB0BBQ4aD0UqIThFJwAKBgAXEUkEGQUBBAwdOgABHD4DChwUAB0R'); // Test merchant ID
}

if (!defined('ESEWA_URL')) {
    define('ESEWA_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form');
}

if (!defined('ESEWA_VERIFY_URL')) {
    define('ESEWA_VERIFY_URL', 'https://rc-epay.esewa.com.np/api/epay/transaction/status');
}

if (!defined('ESEWA_SECRET_KEY')) {
    define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q'); // Test secret key
}

if (!defined('SITE_URL')) {
    define('SITE_URL', $siteUrl);
}

// You can add other local settings here, e.g.:
// if (!defined('SITE_NAME')) define('SITE_NAME', 'My Sweets Shop');


