<?php
require_once __DIR__ . '/includes/functions.php';

// Handle add to cart from wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'add-to-cart' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $qty = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
        if (find_product($id)) {
            add_to_cart($id, $qty);
            $_SESSION['message'] = "Added {$qty} item(s) to cart!";
        }
    } elseif ($action === 'remove' && isset($_POST['id'])) {
        $id = $_POST['id'];
        remove_from_wishlist($id);
        $_SESSION['message'] = 'Removed from wishlist!';
    }
    
    header('Location: ' . site_url('wishlist.php'));
    exit;
}

$wishlist = get_wishlist();
$products = load_products();

// Map products by ID
$map = [];
foreach ($products as $p) {
    $map[$p['id']] = $p;
}

// Get wishlist items
$items = [];
foreach ($wishlist as $id) {
    if (isset($map[$id])) {
        $items[] = $map[$id];
    }
}

include __DIR__ . '/includes/header.php';
?>

<section>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>Your Wishlist</h2>
        <a href="<?php echo htmlspecialchars(site_url('')); ?>" style="color: var(--accent); text-decoration: none; font-weight: 500;">← Back to Shop</a>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="notice success">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <?php if (empty($items)): ?>
        <div class="notice info">
            <p>Your wishlist is empty.</p>
            <p><a href="<?php echo htmlspecialchars(site_url('')); ?>">Continue Shopping</a></p>
        </div>
    <?php else: ?>
        <div style="margin-bottom: 1rem; color: #666; font-size: 0.95rem;">
            <?php echo count($items); ?> item<?php echo count($items) !== 1 ? 's' : ''; ?> in your wishlist
        </div>
        <div class="wishlist-grid">
            <?php foreach ($items as $product): ?>
                <div class="product-card" style="background: #fff; border: 1px solid #eee; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease;">
                    <div class="product-image" style="position: relative; overflow: hidden; height: 220px;">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php if (isset($product['category'])): ?>
                            <span class="category-badge" style="position: absolute; top: 10px; left: 10px; background: rgba(211,84,0,0.9); color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;"><?php echo htmlspecialchars($product['category']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($product['badge'])): ?>
                            <?php $bb = strtolower(str_replace(' ', '-', $product['badge'])); ?>
                            <span class="product-badge <?php echo htmlspecialchars($bb === 'best-seller' ? 'best' : (strpos($bb,'popular')!==false ? 'popular' : '')); ?>" style="right:10px;top:10px"><?php echo htmlspecialchars($product['badge']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info" style="padding: 1rem;">
                        <h3 style="margin: 0 0 0.5rem 0; font-size: 1.05rem; color: #222;"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p style="margin: 0 0 0.75rem 0; color: #888; font-size: 0.9rem;"><?php echo htmlspecialchars($product['short']); ?></p>
                        <p class="price" style="font-size: 1.25rem; margin: 0.5rem 0 1rem 0;"><?php echo format_price($product['price']); ?></p>
                        <p class="description" style="color: #999; font-size: 0.85rem; margin: 0 0 1rem 0; line-height: 1.4;"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-actions" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                            <form method="post" style="display: flex; gap: 0.5rem; flex: 1; min-width: 230px; align-items: center;" class="add-to-cart-form">
                                <input type="hidden" name="action" value="add-to-cart">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                <input type="number" name="quantity" value="1" min="1" max="99" style="width: 60px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; text-align: center; font-size: 0.9rem;">
                                <button type="submit" class="primary add-to-cart-btn" style="flex: 1; padding: 0.65rem 1rem;">Add to Cart</button>
                            </form>
                            <form method="post" style="flex: 1; min-width: 130px;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                <button type="submit" class="secondary" style="width: 100%; padding: 0.65rem 1rem;">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<script>
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const btn = this.querySelector('.add-to-cart-btn');
        btn.classList.add('btn-add-cart-clicked');
        btn.textContent = '✓ Added!';
        btn.style.backgroundColor = '#27ae60';
        
        setTimeout(() => {
            btn.classList.remove('btn-add-cart-clicked');
            btn.textContent = 'Add to Cart';
            btn.style.backgroundColor = '';
        }, 1500);
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
