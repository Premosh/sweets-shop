<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Handle delete action before any output so redirects work correctly
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $list = load_products();
    $new = [];
    foreach ($list as $p) {
        if ((string)$p['id'] !== (string)$id) $new[] = $p;
        else {
            // attempt to unlink image if stored in assets/images and not placeholder
            if (!empty($p['image']) && strpos($p['image'], 'assets/images') !== false) {
                $path = __DIR__ . '/..' . '/' . $p['image'];
                if (file_exists($path) && basename($path) !== 'placeholder.svg') @unlink($path);
            }
        }
    }
    save_products($new);
    header('Location: /admin/index.php');
    exit;
}

$products = load_products();
include __DIR__ . '/../includes/header.php';
?>
<section>
    <h2>Admin — Products</h2>
    <p><a href="<?php echo htmlspecialchars(site_url('admin/edit.php')); ?>">+ New Product</a> | <a href="<?php echo htmlspecialchars(site_url('admin/logout.php')); ?>">Logout</a></p>
    <table class="cart-table">
        <thead><tr><th>ID</th><th>Name</th><th>Price</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['id']); ?></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo format_price($p['price']); ?></td>
                <td>
                    <a href="/admin/edit.php?id=<?php echo $p['id']; ?>">Edit</a>
                    <a href="/admin/index.php?action=delete&id=<?php echo $p['id']; ?>" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php
include __DIR__ . '/../includes/footer.php';
?>