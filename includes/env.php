<?php
/**
 * includes/env.php — Production Environment Configuration
 * 
 * IMPORTANT: This file is NOT tracked by Git (.gitignore)
 * Created for: InfinityFree deployment
 * Domain: https://sweets-shop.infinityfreeapp.com/
 * 
 * DO NOT commit this file to version control!
 */

// Site URL — used for redirects and payment callbacks
// Set to your InfinityFree domain
define('SITE_URL', 'https://sweets-shop.infinityfreeapp.com');

// eSewa Configuration
// Currently set to 'RC' (test) environment for safe testing
// Switch to 'live' only after confirming payments work correctly
define('ESEWA_MODE', 'RC');

// eSewa Test Credentials (RC Environment)
// These are official test credentials provided by eSewa
// Request live credentials from: https://developer.esewa.com.np once you're ready for production
define('ESEWA_MERCHANT_ID', 'JB0BBQ4aD0UqIThFJwAKBgAXEUkEGQUBBAwdOgABHD4DChwUAB0R');
define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');

// Admin Panel Password
// Access at: https://sweets-shop.infinityfreeapp.com/admin/
// IMPORTANT: Change this to a strong, unique password!
define('ADMIN_PASSWORD', 'admin123');

// Optional: Session configuration for InfinityFree
// Uncomment these lines if you experience session-related issues
// ini_set('session.save_path', '/tmp');
// ini_set('session.gc_probability', 1);
// ini_set('session.gc_divisor', 1000);

// Optional: Database configuration (for future use)
// Uncomment and configure when you add database support to the app
// define('DB_HOST', 'localhost');
// define('DB_USER', 'your_infinityfree_db_user');
// define('DB_PASS', 'your_infinityfree_db_password');
// define('DB_NAME', 'your_infinityfree_db_name');

// ============================================================================
// DEPLOYMENT CHECKLIST for https://sweets-shop.infinityfreeapp.com
// ============================================================================
// 
// ✅ Steps to complete on InfinityFree:
// 
// 1. Upload this file (includes/env.php) to your hosting via file manager or FTP
// 
// 2. Configure eSewa Callback URLs in your eSewa merchant dashboard:
//    - Callback URL: https://sweets-shop.infinityfreeapp.com/checkout.php
//    - Use RC (test) environment for now
// 
// 3. Test the site:
//    - Visit: https://sweets-shop.infinityfreeapp.com
//    - Add products to cart
//    - Try checkout with test eSewa credentials
// 
// 4. Monitor logs:
//    - Check InfinityFree control panel error logs for any issues
//    - Test eSewa payment verification is working
// 
// 5. Go live (when ready):
//    - Request live eSewa credentials: https://developer.esewa.com.np
//    - Update this file with live credentials
//    - Change ESEWA_MODE to 'live'
//    - Update eSewa callbacks to use live domain
//    - Change ADMIN_PASSWORD to something strong
// 
// ============================================================================
