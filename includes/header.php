<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/functions.php';
$cartCount = cart_count();
$wishlistCount = wishlist_count();
$base = site_base();
$site = function($p='') { return site_url($p); };
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Sweets Shop</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars(site_url('assets/css/style.css')); ?>">
</head>
<body>
<header class="site-header">
    <div class="container">
        <h1 class="logo"><a href="<?php echo htmlspecialchars(site_url('')); ?>">Sweets Shop</a></h1>
        <nav class="top-nav">
            <a href="<?php echo htmlspecialchars(site_url('')); ?>">Home</a>
            <a href="<?php echo htmlspecialchars(site_url('wishlist.php')); ?>">❤️ Wishlist (<span id="wishlist-count"><?php echo $wishlistCount; ?></span>)</a>
            <a href="<?php echo htmlspecialchars(site_url('cart.php')); ?>">🛒 Cart (<span id="cart-count"><?php echo $cartCount; ?></span>)</a>
        </nav>
    </div>
</header>
<main class="container">