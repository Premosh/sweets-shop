<?php
/**
 * includes/env.php — Environment-specific configuration
 * 
 * THIS FILE IS UNTRACKED (in .gitignore) — DO NOT COMMIT
 * Create this file on each deployment with your actual values
 * 
 * Template for InfinityFree / Production deployment:
 * - Replace YOUR_DOMAIN with your actual subdomain (e.g., myshop.epizy.com)
 * - Replace eSewa credentials with your actual merchant ID and secret key
 */

// Site URL — used for redirects and payment callbacks
// UPDATE THIS: replace with your actual InfinityFree subdomain
define('SITE_URL', 'https://YOUR_DOMAIN.epizy.com');

// eSewa Configuration
// Set to 'live' for production, 'RC' for testing
define('ESEWA_MODE', 'RC');

// TEST credentials (provided by eSewa for testing)
// For production: obtain live credentials from eSewa
define('ESEWA_MERCHANT_ID', 'JB0BBQ4aD0UqIThFJwAKBgAXEUkEGQUBBAwdOgABHD4DChwUAB0R');
define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');

// Optional: Admin password (override the one in config.php)
// define('ADMIN_PASSWORD', 'your_strong_password_here');

// Optional: Session configuration for InfinityFree
// ini_set('session.save_path', '/tmp');
// ini_set('session.gc_probability', 1);
// ini_set('session.gc_divisor', 1000);

// Optional: Database configuration (if you add database support later)
// define('DB_HOST', 'localhost');
// define('DB_USER', 'your_infinityfree_db_user');
// define('DB_PASS', 'your_infinityfree_db_password');
// define('DB_NAME', 'your_infinityfree_db_name');
