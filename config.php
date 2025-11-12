<?php
// config.php — local configuration for Sweets Shop
// IMPORTANT: For production, keep this file out of version control and set a strong admin password.
// Change the ADMIN_PASSWORD below to secure the admin area.
if (!defined('ADMIN_PASSWORD')) define('ADMIN_PASSWORD', 'admin123');

// Detect if running on Render.com or local
$isProduction = getenv('RENDER_EXTERNAL_URL') !== false;
$siteUrl = $isProduction ? rtrim(getenv('RENDER_EXTERNAL_URL'), '/') : 'http://localhost:8000';

// eSewa Integration Settings
// Test Merchant ID from official docs: https://developer.esewa.com.np/#/epay
if (!defined('ESEWA_MERCHANT_ID')) {
    define('ESEWA_MERCHANT_ID', getenv('ESEWA_MERCHANT_ID') ?: 'JB0BBQ4aD0UqIThFJwAKBgAXEUkEGQUBBAwdOgABHD4DChwUAB0R'); // Test merchant
}

// eSewa endpoints - Test Environment (v2 API)
// For production, update these URLs and credentials in Render environment variables
// Documentation URL: https://developer.esewa.com.np/#/epay
if (!defined('ESEWA_URL')) {
    $esewaMerchant = getenv('ESEWA_MODE') === 'live' ? 'epay' : 'rc-epay';
    define('ESEWA_URL', "https://{$esewaMerchant}.esewa.com.np/api/epay/main/v2/form");
}

if (!defined('ESEWA_VERIFY_URL')) {
    $esewaMerchant = getenv('ESEWA_MODE') === 'live' ? 'epay' : 'rc-epay';
    define('ESEWA_VERIFY_URL', "https://{$esewaMerchant}.esewa.com.np/api/epay/transaction/status");
}

if (!defined('ESEWA_SECRET_KEY')) {
    define('ESEWA_SECRET_KEY', getenv('ESEWA_SECRET_KEY') ?: '8gBm/:&EnhH.1/q'); // Test environment secret key
}

// Site URL for callbacks and redirects
if (!defined('SITE_URL')) {
    define('SITE_URL', $siteUrl);
}

// You can add other local settings here, e.g.:
// if (!defined('SITE_NAME')) define('SITE_NAME', 'My Sweets Shop');

