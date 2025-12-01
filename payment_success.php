<?php
require_once 'config.php';
require_once 'database.php';
require_once 'vendor/autoload.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$session_id = isset($_GET['session_id']) ? $_GET['session_id'] : '';
$shipment_id = isset($_GET['shipment_id']) ? (int)$_GET['shipment_id'] : 0;

if (!$session_id || !$shipment_id) {
    header("Location: track_shipment.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    // Verify the Stripe session
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    if ($session->payment_status === 'paid') {
        $db->beginTransaction();

        // Update payment status
        $update_payment = $db->prepare("UPDATE payments SET
            status = 'completed',
            payment_method = 'stripe',
            transaction_id = :transaction_id,
            payment_date = NOW(),
            updated_at = NOW()
            WHERE shipment_id = :shipment_id AND status = 'pending'");
        $update_payment->bindValue(':transaction_id', $session->payment_intent);
        $update_payment->bindValue(':shipment_id', $shipment_id, PDO::PARAM_INT);
        $update_payment->execute();

        // Update shipment payment status
        $update_shipment = $db->prepare("UPDATE shipments SET
            payment_status = 'completed',
            updated_at = NOW()
            WHERE shipment_id = :shipment_id");
        $update_shipment->bindValue(':shipment_id', $shipment_id, PDO::PARAM_INT);
        $update_shipment->execute();

        // Get shipment and client details for notification
        $shipment_query = $db->prepare("SELECT s.*, c.company_name, u.email as client_email
            FROM shipments s
            JOIN clients c ON s.client_id = c.client_id
            JOIN users u ON c.user_id = u.user_id
            WHERE s.shipment_id = :shipment_id");
        $shipment_query->bindValue(':shipment_id', $shipment_id, PDO::PARAM_INT);
        $shipment_query->execute();
        $shipment = $shipment_query->fetch(PDO::FETCH_ASSOC);

        // Log activity
        logActivity($_SESSION['user_id'], 'payment_completed', 'payments', $shipment_id,
            'Payment completed via Stripe for shipment ' . ($shipment['tracking_number'] ?? $shipment_id));

        // Notify admin
        notifyAdmin(
            'Payment Received - ' . ($shipment['tracking_number'] ?? $shipment_id),
            'Payment has been successfully received via Stripe for shipment ' . ($shipment['tracking_number'] ?? $shipment_id) . ' (' . ($shipment['company_name'] ?? 'Unknown') . ')',
            'success',
            'payments',
            $shipment_id
        );

        // Send receipt email to client
        $client_email_subject = 'Payment Receipt for Shipment #' . ($shipment['tracking_number'] ?? $shipment_id);
        $client_email_body = '
            <h1>Thank you for your payment!</h1>
            <p>Your payment for shipment <strong>' . ($shipment['tracking_number'] ?? $shipment_id) . '</strong> has been successfully processed.</p>
            <p><strong>Amount Paid:</strong> ' . formatCurrency($session->amount_total / 100, $session->currency) . '</p>
            <p><strong>Transaction ID:</strong> ' . $session->payment_intent . '</p>
            <p>You can track your shipment here: <a href="' . APP_URL . '/track_shipment.php?tracking_number=' . ($shipment['tracking_number'] ?? '') . '">' . APP_URL . '/track_shipment.php</a></p>
        ';
        sendEmail($shipment['client_email'], $client_email_subject, $client_email_body);

        // Send notification email to admin
        $admin_email_subject = 'Payment Received for Shipment #' . ($shipment['tracking_number'] ?? $shipment_id);
        $admin_email_body = '
            <h1>Payment Received</h1>
            <p>A payment has been successfully received for shipment <strong>' . ($shipment['tracking_number'] ?? $shipment_id) . '</strong>.</p>
            <p><strong>Client:</strong> ' . ($shipment['company_name'] ?? 'Unknown') . '</p>
            <p><strong>Amount Paid:</strong> ' . formatCurrency($session->amount_total / 100, $session->currency) . '</p>
            <p><strong>Transaction ID:</strong> ' . $session->payment_intent . '</p>
            <p>Please review the shipment and update its status accordingly.</p>
        ';
        $admin_users_query = $db->query("SELECT email FROM users WHERE role = 'admin' AND status = 'active'");
        while($admin = $admin_users_query->fetch(PDO::FETCH_ASSOC)) {
            sendEmail($admin['email'], $admin_email_subject, $admin_email_body);
        }

        $db->commit();
        $success = true;
    }
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $error = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Prime Cargo Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-ship me-2"></i>Prime Cargo Limited
            </a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <?php if (isset($success) && $success): ?>
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 72px;"></i>
                            </div>
                            <h2 class="text-success mb-3">Payment Successful!</h2>
                            <p class="text-muted mb-4">
                                Your payment has been processed successfully.<br>
                                A receipt has been sent to your email address.
                            </p>
                            <div class="alert alert-info">
                                <strong>Next Steps:</strong><br>
                                Your shipment clearance is now complete. You will be notified when your goods are ready for collection.
                            </div>
                            <a href="track_shipment.php" class="btn btn-primary btn-lg mt-3">
                                <i class="fas fa-search me-2"></i>Track Your Shipment
                            </a>
                            <a href="dashboard.php" class="btn btn-outline-secondary btn-lg mt-3 ms-2">
                                <i class="fas fa-home me-2"></i>Go to Dashboard
                            </a>
                        <?php else: ?>
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 72px;"></i>
                            </div>
                            <h2 class="text-warning mb-3">Payment Verification Failed</h2>
                            <p class="text-muted mb-4">
                                We couldn't verify your payment. Please contact support if you believe this is an error.
                            </p>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                            <a href="payment.php?shipment_id=<?php echo $shipment_id; ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-redo me-2"></i>Try Again
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>