</main>
<footer class="site-footer">
    <div class="container">
        <p>© <?php echo date('Y'); ?> Sweets Shop — Premosh</p>
    </div>
</footer>
</footer>
<!-- Expose a small runtime config so JS can call the correct URLs even when served from a subfolder -->
<script>
    window.SS = window.SS || {};
    window.SS.cartUrl = <?php echo json_encode(site_url('cart.php')); ?>;
    window.SS.assetsBase = <?php echo json_encode(site_url('assets/')); ?>;
</script>
<script src="<?php echo htmlspecialchars(site_url('assets/js/app.js')); ?>"></script>
</body>
</html>