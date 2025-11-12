<?php
// wishlist-api.php - AJAX endpoint for wishlist operations
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $action = $_POST['action'];
    $id = $_POST['id'];
    
    if ($action === 'add') {
        add_to_wishlist($id);
        $response['success'] = true;
        $response['message'] = 'Added to wishlist';
        $response['inWishlist'] = true;
    } elseif ($action === 'remove') {
        remove_from_wishlist($id);
        $response['success'] = true;
        $response['message'] = 'Removed from wishlist';
        $response['inWishlist'] = false;
    }
    
    $response['count'] = wishlist_count();
}

echo json_encode($response);
