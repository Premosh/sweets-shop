<?php
require_once __DIR__ . '/includes/functions.php';
$products = load_products();

// Get selected category from URL
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';

// Get all unique categories with product counts
$categories = [];
$categoryCounts = [];
foreach ($products as $p) {
    if (isset($p['category'])) {
        $cat = $p['category'];
        if (!in_array($cat, $categories)) {
            $categories[] = $cat;
        }
        if (!isset($categoryCounts[$cat])) {
            $categoryCounts[$cat] = 0;
        }
        $categoryCounts[$cat]++;
    }
}
sort($categories);

// Filter products by category if selected
$filteredProducts = $products;
if ($selectedCategory) {
    $filteredProducts = array_filter($products, function($p) use ($selectedCategory) {
        return isset($p['category']) && $p['category'] === $selectedCategory;
    });
}

include __DIR__ . '/includes/header.php';
?>
<section>
    <div style="margin: 2rem 0 1rem 0;">
        <h2 style="margin: 0; color: #333;">🍫 Our Sweets</h2>
        <?php if ($selectedCategory): ?>
            <p style="color: #666; margin: 0.5rem 0 0; font-size: 0.95rem;">
                Showing <strong><?php echo count($filteredProducts); ?></strong> item<?php echo count($filteredProducts) !== 1 ? 's' : ''; ?> in <strong><?php echo htmlspecialchars($selectedCategory); ?></strong>
                <a href="<?php echo htmlspecialchars(site_url('')); ?>" style="margin-left: 1rem; color: #d35400; text-decoration: none;">← Back to all</a>
            </p>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($categories)): ?>
        <div class="category-filters">
            <a href="<?php echo htmlspecialchars(site_url('')); ?>" class="filter-btn <?php echo $selectedCategory === '' ? 'active' : ''; ?>">
                All Products (<?php echo count($products); ?>)
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?php echo htmlspecialchars(site_url('?category=' . urlencode($cat))); ?>" class="filter-btn <?php echo $selectedCategory === $cat ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat); ?> (<?php echo $categoryCounts[$cat]; ?>)
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($filteredProducts)): ?>
        <div class="notice info" style="margin-top: 2rem;">
            <p>No products found in this category.</p>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($filteredProducts as $p): ?>
                <article class="card">
                    <div class="card-image">
                        <a href="<?php echo htmlspecialchars(site_url('product.php?id=' . urlencode($p['id']))); ?>">
                            <img src="<?php echo htmlspecialchars(site_url($p['image'] ?? 'assets/images/placeholder.svg')); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
                        </a>
                        <?php if (isset($p['category'])): ?>
                            <span class="category-tag"><?php echo htmlspecialchars($p['category']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($p['badge'])): ?>
                            <?php $b = strtolower(str_replace(' ', '-', $p['badge'])); ?>
                            <span class="product-badge <?php echo htmlspecialchars($b === 'best-seller' ? 'best' : (strpos($b,'popular')!==false ? 'popular' : '')); ?>"><?php echo htmlspecialchars($p['badge']); ?></span>
                        <?php endif; ?>
                        <button class="wishlist-btn <?php echo is_in_wishlist($p['id']) ? 'active' : ''; ?>" onclick="toggleWishlist(this, <?php echo htmlspecialchars($p['id']); ?>)" title="Add to wishlist">❤️</button>
                    </div>
                    <h3><?php echo htmlspecialchars($p['name']); ?></h3>
                    <p class="price"><?php echo format_price($p['price']); ?></p>
                    <p class="desc"><?php echo htmlspecialchars($p['short'] ?? ''); ?></p>
                    <form method="post" action="<?php echo htmlspecialchars(site_url('cart.php')); ?>" class="add-to-cart-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($p['id']); ?>">
                        <label>Qty <input type="number" name="qty" value="1" min="1"></label>
                        <button type="submit">Add to cart</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
<script src="<?php echo htmlspecialchars(site_url('assets/js/wishlist.js')); ?>"></script>