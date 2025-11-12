<?php
require_once __DIR__ . '/includes/functions.php';
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$id = $_POST['id'] ?? $_GET['id'] ?? null;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : (isset($_GET['qty']) ? (int)$_GET['qty'] : 1);

if ($action === 'add' && $id) {
    add_to_cart($id, $qty);
    // If request is AJAX, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'count' => cart_count()]);
        exit;
    }
    header('Location: ' . site_url('cart.php'));
    exit;
}

if ($action === 'update') {
    // Support batch updates from the cart form which submits qtys[] (qtys[id] = qty)
    if (isset($_POST['qtys']) && is_array($_POST['qtys'])) {
        foreach ($_POST['qtys'] as $pid => $q) {
            // normalize to integer
            $qInt = (int)$q;
            update_cart_item($pid, $qInt);
        }
    } elseif ($id) {
        // fallback single-item update
        update_cart_item($id, $qty);
    }
    header('Location: ' . site_url('cart.php'));
    exit;
}

if ($action === 'remove' && $id) {
    remove_cart_item($id);
    // If request is AJAX, return JSON so the UI can update without a reload
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'count' => cart_count()]);
        exit;
    }
    header('Location: ' . site_url('cart.php'));
    exit;
}

if ($action === 'clear') {
    clear_cart();
    header('Location: ' . site_url('cart.php'));
    exit;
}

include __DIR__ . '/includes/header.php';
$cart = get_cart();
$products = load_products();
$map = [];
foreach ($products as $p) $map[$p['id']] = $p;
?>
<section>
    <h2>Your Cart</h2>
    <?php if (empty($cart)): ?>
        <p>Your cart is empty. <a href="<?php echo htmlspecialchars(site_url('')); ?>">Continue shopping</a>.</p>
    <?php else: ?>
        <form method="post" action="<?php echo htmlspecialchars(site_url('cart.php')); ?>">
            <input type="hidden" name="action" value="update">
            <table class="cart-table">
                <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($cart as $pid => $q):
                    $p = $map[$pid] ?? null;
                    if (!$p) continue;
                    $subtotal = $p['price'] * $q;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo format_price($p['price']); ?></td>
                    <td>
                        <input type="number" name="qtys[<?php echo htmlspecialchars($pid); ?>]" value="<?php echo htmlspecialchars($q); ?>" min="0">
                    </td>
                    <td><?php echo format_price($subtotal); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars(site_url('cart.php?action=remove&id=' . urlencode($pid))); ?>">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
                <div class="cart-actions">
                <button type="submit">Update cart</button>
                <a class="btn" href="<?php echo htmlspecialchars(site_url('cart.php?action=clear')); ?>">Clear cart</a>
                <a class="btn primary" href="<?php echo htmlspecialchars(site_url('checkout.php')); ?>">Checkout</a>
            </div>
        </form>
        <aside class="cart-summary">
            <p>Items: <?php echo cart_count(); ?></p>
            <p>Total: <?php echo format_price(cart_total()); ?></p>
        </aside>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>