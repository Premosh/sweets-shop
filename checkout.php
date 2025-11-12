<?php
require_once __DIR__ . '/includes/functions.php';
$errors = [];
$success = false;

// Handle eSewa response
$action = $_GET['action'] ?? '';
if ($action === 'esewa-success') {
    // Log the raw GET payload for debugging
    error_log('eSewa success callback GET: ' . json_encode($_GET));

    // eSewa sometimes redirects with different or missing query params. We'll accept the
    // transaction if we can derive the order/amount from the session (pending_order)
    $pid = $_GET['oid'] ?? ($_SESSION['pending_order']['id'] ?? null);
    $rid = $_GET['refId'] ?? null;
    $amt = isset($_GET['amt']) ? number_format((float)$_GET['amt'], 2, '.', '') : null;

    // Fallback to session-stored pending order amount when the gateway doesn't provide amt
    if ($amt === null && !empty($_SESSION['pending_order']['total'])) {
        $amt = number_format((float)$_SESSION['pending_order']['total'], 2, '.', '');
    }

    if (empty($pid) || empty($amt)) {
        // Still missing required identifiers
        $errors[] = 'Invalid payment response received.';
        error_log('eSewa callback missing pid or amount. PID: ' . var_export($pid, true) . ' AMT: ' . var_export($amt, true));
    } else {
        // Log verification attempt
        error_log("Attempting eSewa verification - Order: $pid, Amount: $amt, Ref: " . ($rid ?? 'N/A'));

        // Verify the payment with eSewa (now returns array with success, ref_id, response)
        $verifyResult = verify_esewa_payment($pid, $rid, $amt);
        if (is_array($verifyResult) && !empty($verifyResult['success'])) {
            // Payment verified - mark order as paid and clear cart
            clear_cart();
            $success = true;
            // Extract reference_id from gateway response if available
            $gatewayRefId = $verifyResult['ref_id'] ?? $rid;
            // Persist last payment details so we can show them even if eSewa didn't include them in the GET
            $_SESSION['last_payment'] = [
                'order_id' => $pid,
                'amount' => $amt,
                'ref' => $gatewayRefId
            ];
            error_log("eSewa payment successful. Order: $pid, Ref: " . ($gatewayRefId ?? 'N/A') . ", Amount: $amt");
        } else {
            $errors[] = 'Payment could not be verified. Your Reference ID: ' . htmlspecialchars($rid ?? 'N/A');
            error_log("eSewa payment verification failed. Order: $pid, Ref: " . ($rid ?? 'N/A') . ", Amount: $amt");
        }
    }
} elseif ($action === 'esewa-failure') {
    $errors[] = 'Payment was cancelled or failed. Please try again.';
    if (!empty($_GET['oid'])) {
        error_log("eSewa payment failed for order: " . $_GET['oid']);
    }
}

// Handle initial checkout form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    if ($name === '') $errors[] = 'Name is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
    if ($address === '') $errors[] = 'Address is required';

    if (empty($errors)) {
        // Create initial order
        $orderId = generate_order_id();
        $order = [
            'id' => $orderId,
            'name' => $name,
            'email' => $email,
            'address' => $address,
            'items' => get_cart(),
            'total' => cart_total(),
            'created_at' => date('c'),
            'status' => 'pending'
        ];
        
        // Get eSewa form data
        $esewa = get_esewa_form_data($order);
        
        // Generate signature for v2 API using the params and the signed_field_names
        $signature = generate_esewa_signature($esewa['params']);
        
        // Store order in session for post-payment processing
        $_SESSION['pending_order'] = $order;
        
        // Show the eSewa payment form
        include __DIR__ . '/includes/header.php';
        ?>
        <section>
            <h2>Complete Payment</h2>
            <div class="notice info">
                <p>Total Amount: <?php echo format_price($order['total']); ?></p>
                <p>Click below to pay securely with eSewa:</p>
            </div>
            <!-- eSewa Payment Form v2 API -->
            <form action="<?php echo htmlspecialchars($esewa['url']); ?>" method="POST">
                <input value="<?php echo htmlspecialchars($esewa['params']['total_amount']); ?>" name="total_amount" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['amount']); ?>" name="amount" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['transaction_uuid']); ?>" name="transaction_uuid" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['product_code']); ?>" name="product_code" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['product_service_charge']); ?>" name="product_service_charge" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['product_delivery_charge']); ?>" name="product_delivery_charge" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['tax_amount']); ?>" name="tax_amount" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['success_url']); ?>" name="success_url" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['failure_url']); ?>" name="failure_url" type="hidden">
                <input value="<?php echo htmlspecialchars($esewa['params']['signed_field_names']); ?>" name="signed_field_names" type="hidden">
                <input value="<?php echo htmlspecialchars($signature); ?>" name="signature" type="hidden">
                
                <button type="submit" class="primary">Pay with eSewa</button>
                <a href="<?php echo htmlspecialchars(site_url('cart.php')); ?>">Back to Cart</a>
            </form>
        </section>
        <?php
        include __DIR__ . '/includes/footer.php';
        exit;
    }
}
include __DIR__ . '/includes/header.php';
?>
<section>
    <h2>Checkout</h2>
    <?php if ($success): ?>
        <div class="notice success">
            <h3>Thank you — your payment was successful!</h3>
            <p>Your order is confirmed and we've emailed you the details.</p>
            <?php
                // Prefer session-stored last_payment values (set after verification), then GET, then pending_order
                $displayAmount = 0;
                $displayRef = '';
                if (!empty($_SESSION['last_payment']['amount'])) {
                    $displayAmount = $_SESSION['last_payment']['amount'];
                    $displayRef = $_SESSION['last_payment']['ref'] ?? '';
                } elseif (!empty($_GET['amt'])) {
                    $displayAmount = $_GET['amt'];
                    $displayRef = $_GET['refId'] ?? '';
                } elseif (!empty($_SESSION['pending_order']['total'])) {
                    $displayAmount = $_SESSION['pending_order']['total'];
                }
            ?>
            <p>Amount paid: <?php echo format_price($displayAmount); ?></p>
            <p>Reference ID: <?php echo htmlspecialchars($displayRef); ?></p>
            <p><a href="<?php echo htmlspecialchars(site_url('')); ?>">Continue Shopping</a></p>
        </div>
    <?php else: ?>
        <?php if ($errors): ?>
            <div class="notice error">
                <ul>
                    <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
                </ul>
            </div>
        <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars(site_url('checkout.php')); ?>" class="checkout-form">
            <label>Name:<input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required></label>
            <label>Email:<input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required></label>
            <label>Address:<textarea name="address" required><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea></label>
            <div class="checkout-actions">
                <button type="submit" class="primary">Place Order</button>
                <a href="<?php echo htmlspecialchars(site_url('cart.php')); ?>">Back to cart</a>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>