<?php
// config.php — Configuration for Sweets Shop
// 
// For production: create includes/env.php with your actual settings
// For local development: uses the defaults below

// Load production environment config if it exists (includes/env.php)
// This file should NOT be committed to Git (it's in .gitignore)
if (file_exists(__DIR__ . '/includes/env.php')) {
    require_once __DIR__ . '/includes/env.php';
}

// Default admin password (override in includes/env.php for production)
if (!defined('ADMIN_PASSWORD')) define('ADMIN_PASSWORD', 'admin123');

// Default Site URL for local development
// Overridden by includes/env.php if deployed to production
$isNetlify = getenv('NETLIFY') === 'true' || getenv('CONTEXT') !== false;
$netlifyUrl = getenv('URL') ?: getenv('DEPLOY_URL');
$siteUrl = $isNetlify && $netlifyUrl ? rtrim($netlifyUrl, '/') : 'http://localhost:8000';

// eSewa Integration Settings (test defaults)
// These are overridden by includes/env.php if defined there
if (!defined('ESEWA_MERCHANT_ID')) {
    define('ESEWA_MERCHANT_ID', getenv('ESEWA_MERCHANT_ID') ?: 'JB0BBQ4aD0UqIThFJwAKBgAXEUkEGQUBBAwdOgABHD4DChwUAB0R'); // Test merchant ID
}

if (!defined('ESEWA_URL')) {
    $esewaMerchant = getenv('ESEWA_MODE') === 'live' ? 'epay' : 'rc-epay';
    define('ESEWA_URL', "https://{$esewaMerchant}.esewa.com.np/api/epay/main/v2/form");
}

if (!defined('ESEWA_VERIFY_URL')) {
    $esewaMerchant = getenv('ESEWA_MODE') === 'live' ? 'epay' : 'rc-epay';
    define('ESEWA_VERIFY_URL', "https://{$esewaMerchant}.esewa.com.np/api/epay/transaction/status");
}

if (!defined('ESEWA_SECRET_KEY')) {
    define('ESEWA_SECRET_KEY', getenv('ESEWA_SECRET_KEY') ?: '8gBm/:&EnhH.1/q'); // Test secret key
}

if (!defined('SITE_URL')) {
    define('SITE_URL', $siteUrl);
}

// You can add other local settings here, e.g.:
// if (!defined('SITE_NAME')) define('SITE_NAME', 'My Sweets Shop');


