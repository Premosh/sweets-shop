<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
$id = $_GET['id'] ?? null;
$products = load_products();
$product = null;
if ($id) {
    foreach ($products as $p) if ((string)$p['id'] === (string)$id) { $product = $p; break; }
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $short = trim($_POST['short'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    if ($name === '') $errors[] = 'Name required';
    if ($price <= 0) $errors[] = 'Price must be > 0';

    // handle image upload
    $imagePath = $product['image'] ?? 'assets/images/placeholder.svg';
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tmp = $_FILES['image']['tmp_name'];
        $orig = basename($_FILES['image']['name']);
        $ext = pathinfo($orig, PATHINFO_EXTENSION);
        $safe = preg_replace('/[^a-z0-9._-]/i', '', pathinfo($orig, PATHINFO_FILENAME));
        $nameFile = 'assets/images/' . $safe . '-' . time() . ( $ext ? '.' . $ext : '.img');
        $dest = __DIR__ . '/../' . $nameFile;
        if (move_uploaded_file($tmp, $dest)) {
            $imagePath = $nameFile;
        }
    }

    if (empty($errors)) {
        if ($id && $product) {
            // update
            foreach ($products as &$p) {
                if ((string)$p['id'] === (string)$id) {
                    $p['name'] = $name;
                    $p['short'] = $short;
                    $p['description'] = $desc;
                    $p['price'] = $price;
                    $p['image'] = $imagePath;
                    break;
                }
            }
            unset($p);
        } else {
            // create
            $newId = next_product_id();
            $products[] = [
                'id' => $newId,
                'name' => $name,
                'short' => $short,
                'description' => $desc,
                'price' => $price,
                'image' => $imagePath
            ];
        }
        save_products($products);
        header('Location: /admin/index.php');
        exit;
    }
}
include __DIR__ . '/../includes/header.php';
?>
<section>
    <h2><?php echo $product ? 'Edit' : 'New'; ?> Product</h2>
    <?php if ($errors): ?>
        <div class="notice error"><ul><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul></div>
    <?php endif; ?>
    <form method="post" action="" enctype="multipart/form-data" class="checkout-form">
        <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required></label>
        <label>Short: <input type="text" name="short" value="<?php echo htmlspecialchars($product['short'] ?? ''); ?>"></label>
        <label>Price: <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required></label>
        <label>Description: <textarea name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea></label>
        <label>Image: <input type="file" name="image" accept="image/*"></label>
        <?php if (!empty($product['image'])): ?>
            <p>Current image: <img src="<?php echo htmlspecialchars(site_url($product['image'])); ?>" style="height:60px;object-fit:cover;border:1px solid #ddd;padding:2px;border-radius:4px"></p>
        <?php endif; ?>
        <div class="checkout-actions">
            <button type="submit" class="primary"><?php echo $product ? 'Save' : 'Create'; ?></button>
            <a href="<?php echo htmlspecialchars(site_url('admin/index.php')); ?>">Cancel</a>
        </div>
    </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>