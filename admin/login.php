<?php
require_once __DIR__ . '/../includes/functions.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pw = $_POST['password'] ?? '';
    if (admin_login($pw)) {
        header('Location: /admin/index.php');
        exit;
    } else {
        $err = 'Invalid password';
    }
}
include __DIR__ . '/../includes/header.php';
?>
<section>
    <h2>Admin Login</h2>
    <?php if ($err): ?>
        <div class="notice error"><?php echo htmlspecialchars($err); ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars(site_url('admin/login.php')); ?>" class="checkout-form">
        <label>Password: <input type="password" name="password" required></label>
        <div class="checkout-actions">
            <button type="submit" class="primary">Login</button>
            <a href="<?php echo htmlspecialchars(site_url('')); ?>">Back to shop</a>
        </div>
    </form>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>