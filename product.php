<?php
require_once __DIR__ . '/includes/functions.php';
$id = $_GET['id'] ?? null;
$product = $id ? find_product($id) : null;
if (!$product) {
    http_response_code(404);
    include __DIR__ . '/includes/header.php';
    echo "<h2>Product not found</h2>";
    include __DIR__ . '/includes/footer.php';
    exit;
}
include __DIR__ . '/includes/header.php';
?>
<section class="product-detail">
    <div class="media">
        <div style="position: relative; display: inline-block;">
            <img src="<?php echo htmlspecialchars(site_url($product['image'] ?? 'assets/images/placeholder.svg')); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <?php if (!empty($product['badge'])): ?>
                <?php $pb = strtolower(str_replace(' ', '-', $product['badge'])); ?>
                <span class="product-badge <?php echo htmlspecialchars($pb === 'best-seller' ? 'best' : (strpos($pb,'popular')!==false ? 'popular' : '')); ?>" style="right: 8px; top: 8px; position: absolute;"><?php echo htmlspecialchars($product['badge']); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="meta">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <?php if (isset($product['category'])): ?>
            <p class="category-tag"><?php echo htmlspecialchars($product['category']); ?></p>
        <?php endif; ?>
        <p class="price"><?php echo format_price($product['price']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?></p>
        <div style="display: flex; gap: 1rem; margin-top: 1rem;">
            <form method="post" action="<?php echo htmlspecialchars(site_url('cart.php')); ?>" class="add-to-cart-form">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                <label>Qty <input type="number" name="qty" value="1" min="1"></label>
                <button type="submit">Add to cart</button>
            </form>
            <button class="wishlist-btn <?php echo is_in_wishlist($product['id']) ? 'active' : ''; ?>" onclick="toggleWishlist(this, <?php echo htmlspecialchars($product['id']); ?>)" title="Add to wishlist" style="width: 50px; height: 50px; font-size: 1.5rem;">❤️</button>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(site_url('assets/js/wishlist.js')); ?>"></script>