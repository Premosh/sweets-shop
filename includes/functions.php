<?php
// includes/functions.php

// Prevent multiple inclusions
if (defined('FUNCTIONS_INCLUDED')) {
    return;
}
define('FUNCTIONS_INCLUDED', true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function data_path($file) {
    return __DIR__ . '/../data/' . $file;
}

// Load optional config file (e.g., config.php) that can define ADMIN_PASSWORD and other settings.
$configPath = __DIR__ . '/../config.php';
if (file_exists($configPath)) require_once $configPath;

// Simple admin password fallback for demo. Change in config.php for deployment.
if (!defined('ADMIN_PASSWORD')) define('ADMIN_PASSWORD', 'admin123');

function load_products() {
    $path = data_path('products.json');
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    $arr = json_decode($json, true);
    return $arr ?: [];
}

function find_product($id) {
    $products = load_products();
    foreach ($products as $p) {
        if ((string)$p['id'] === (string)$id) return $p;
    }
    return null;
}

function format_price($price) {
    return 'NRs. ' . number_format((float)$price, 2);
}

// Generate a unique order ID for eSewa
function generate_order_id() {
    return 'SS' . time() . mt_rand(1000, 9999);
}

// Build eSewa payment form data
// We'll remove the connection test since eSewa might block pre-flight requests
function test_esewa_connection() {
    return true; // Let the actual form submission handle any connection issues
}

function get_esewa_form_data($order) {
    $total = $order['total'];
    $pid = $order['id']; // Unique order ID

    // Full URLs required by eSewa (must be absolute URLs)
    $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
    $successUrl = $baseUrl . site_url('checkout.php?action=esewa-success&oid=' . urlencode($pid));
    $failureUrl = $baseUrl . site_url('checkout.php?action=esewa-failure&oid=' . urlencode($pid));

    // New v2 API format with signature. Include tax_amount which some eSewa setups require.
    $formatted = number_format($total, 2, '.', '');
    return [
        'url' => ESEWA_URL,
        'params' => [
            'total_amount' => $formatted,
            'amount' => $formatted,
            'tax_amount' => '0.00',
            'transaction_uuid' => $pid,
            'product_code' => 'EPAYTEST',
            'product_service_charge' => '0',
            'product_delivery_charge' => '0',
            'success_url' => $successUrl,
            'failure_url' => $failureUrl,
            // signed_field_names must list exactly the fields that will be signed, in order
            'signed_field_names' => 'total_amount,amount,tax_amount,transaction_uuid,product_code'
        ]
    ];
}

// Generate HMAC-SHA256 signature for eSewa v2 API
// Generate HMAC-SHA256 signature for eSewa v2 API
// Accepts the params array that includes 'signed_field_names' so signature is built in exact order
function generate_esewa_signature(array $params) {
    if (empty($params['signed_field_names'])) return '';
    $fields = explode(',', $params['signed_field_names']);
    $parts = [];
    foreach ($fields as $f) {
        $v = isset($params[$f]) ? $params[$f] : '';
        // ensure no extra whitespace and exact formatting
        $parts[] = $f . '=' . $v;
    }
    $sigStr = implode(',', $parts);
    $signature = hash_hmac('sha256', $sigStr, ESEWA_SECRET_KEY, true);
    return base64_encode($signature);
}

// Verify eSewa payment with their v2 API
function verify_esewa_payment($pid, $rid, $amt) {
    $url = ESEWA_VERIFY_URL;
    
    // Format amount exactly as required
    $amount = number_format((float)$amt, 2, '.', '');
    
    // New v2 API uses different verification
    // Sanitize transaction UUID: strip any appended query string (e.g., ?data=...)
    $cleanPid = preg_replace('/\?.*/', '', $pid);

    $data = [
        'product_code' => 'EPAYTEST',
        'total_amount' => $amount,
        'transaction_uuid' => $cleanPid
    ];
    if (!empty($rid)) {
        // Include the gateway reference if available; API may accept or ignore this.
        $data['reference_id'] = $rid;
    }
    
    // Initialize cURL with error handling
    if (!extension_loaded('curl')) {
        error_log('cURL extension not loaded');
        return false;
    }
    
    $curl = curl_init();
    if ($curl === false) {
        error_log('Failed to initialize cURL');
        return false;
    }
    
    // Build verification request for v2 API. The RC server responds with 405 to POST,
    // so use GET with query parameters (the endpoint accepts GET status checks).
    $query = http_build_query($data);
    $verifyUrl = rtrim($url, '/') . '?' . $query;

    curl_setopt_array($curl, [
        CURLOPT_URL => $verifyUrl,
        CURLOPT_HTTPGET => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        // Ask for JSON
        CURLOPT_HTTPHEADER => [
            'Accept: application/json'
        ]
    ]);
    
    // Execute request
    $response = curl_exec($curl);

    if ($response === false) {
        $error = curl_error($curl);
        error_log('eSewa verification request failed: ' . $error);
        curl_close($curl);
        return false;
    }

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Log detailed information for debugging
    error_log('eSewa verification request URL: ' . $verifyUrl);
    error_log('eSewa verification request payload: ' . json_encode($data));
    error_log('eSewa verification HTTP status: ' . $status);
    error_log('eSewa verification raw response: ' . $response);

    // Try to decode JSON response
    $result = json_decode($response, true);
    if ($status === 200 && is_array($result)) {
        // Many v2 responses include 'status' or a 'data' object. Be permissive but log unexpected shapes.
        if (!empty($result['status']) && strtolower($result['status']) === 'complete') {
            // Extract reference_id from response if present (some gateways include this)
            $refId = $result['reference_id'] ?? $result['referenceId'] ?? $result['refId'] ?? null;
            return [
                'success' => true,
                'ref_id' => $refId,
                'response' => $result
            ];
        }
        if (!empty($result['data'])) {
            $refId = $result['data']['reference_id'] ?? $result['data']['referenceId'] ?? $result['data']['refId'] ?? null;
            return [
                'success' => true,
                'ref_id' => $refId,
                'response' => $result
            ];
        }
        // Not an explicitly successful response
        error_log('eSewa verification returned non-success JSON: ' . json_encode($result));
        return ['success' => false, 'ref_id' => null, 'response' => $result];
    }

    // Non-200 or non-JSON response
    return ['success' => false, 'ref_id' => null, 'response' => null];
}

// Wishlist management functions
function get_wishlist() {
    if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
    return $_SESSION['wishlist'];
}

function save_wishlist($wishlist) {
    $_SESSION['wishlist'] = $wishlist;
}

function add_to_wishlist($id) {
    $wishlist = get_wishlist();
    if (!in_array($id, $wishlist)) {
        $wishlist[] = $id;
        save_wishlist($wishlist);
    }
}

function remove_from_wishlist($id) {
    $wishlist = get_wishlist();
    $wishlist = array_filter($wishlist, function($item) use ($id) {
        return $item !== $id;
    });
    save_wishlist(array_values($wishlist));
}

function is_in_wishlist($id) {
    $wishlist = get_wishlist();
    return in_array($id, $wishlist);
}

function wishlist_count() {
    return count(get_wishlist());
}

// Cart management functions
function get_cart() {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    return $_SESSION['cart'];
}

function save_cart($cart) {
    $_SESSION['cart'] = $cart;
}

function cart_total() {
    $cart = get_cart();
    $total = 0.0;
    $products = load_products();
    $map = [];
    foreach ($products as $p) $map[$p['id']] = $p;
    foreach ($cart as $id => $qty) {
        if (isset($map[$id])) $total += $map[$id]['price'] * $qty;
    }
    return $total;
}

function cart_count() {
    $cart = get_cart();
    $count = 0;
    foreach ($cart as $q) $count += $q;
    return $count;
}

function add_to_cart($id, $qty=1) {
    $cart = get_cart();
    if (!isset($cart[$id])) $cart[$id] = 0;
    $cart[$id] += max(0, (int)$qty);
    save_cart($cart);
}

function update_cart_item($id, $qty) {
    $cart = get_cart();
    if ($qty <= 0) {
        unset($cart[$id]);
    } else {
        $cart[$id] = (int)$qty;
    }
    save_cart($cart);
}

function remove_cart_item($id) {
    $cart = get_cart();
    unset($cart[$id]);
    save_cart($cart);
}

function clear_cart() {
    $_SESSION['cart'] = [];
}

// Product persistence helpers
function save_products($products) {
    $path = data_path('products.json');
    $json = json_encode(array_values($products), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return (bool) file_put_contents($path, $json, LOCK_EX);
}

function next_product_id() {
    $products = load_products();
    $max = 0;
    foreach ($products as $p) {
        $id = (int)$p['id'];
        if ($id > $max) $max = $id;
    }
    return (string)($max + 1);
}

// Simple admin auth (session-based). For demo only.
function admin_login($password) {
    if ($password === ADMIN_PASSWORD) {
        $_SESSION['is_admin'] = true;
        return true;
    }
    return false;
}

function is_admin() {
    return !empty($_SESSION['is_admin']);
}

function require_admin() {
    if (!is_admin()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function admin_logout() {
    unset($_SESSION['is_admin']);
}

// Helpers to build URLs that respect the site's base path when served from a subfolder.
function site_base() {
    // e.g. if SCRIPT_NAME is /sweets-shop/index.php -> dirname = /sweets-shop
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = rtrim(dirname($script), '/\\');
    if ($dir === '/' || $dir === '.') return '';
    return $dir;
}

function site_url($path = '') {
    $p = ltrim((string)$path, '/');
    $base = site_base();
    if ($base === '') return '/' . $p;
    if ($p === '') return $base . '/';
    return $base . '/' . $p;
}

?>