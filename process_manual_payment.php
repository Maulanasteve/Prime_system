<?php
require_once 'config.php';
require_once 'database.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit();
}

$shipment_id = isset($_POST['shipment_id']) ? (int)$_POST['shipment_id'] : 0;
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$transaction_ref = isset($_POST['transaction_ref']) ? trim($_POST['transaction_ref']) : '';

if (!$shipment_id || !$payment_method || !$transaction_ref) {
    $_SESSION['error'] = "All fields are required";
    header("Location: payment.php?shipment_id=" . $shipment_id);
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    // Verify shipment belongs to this client
    $query = "SELECT s.*, c.company_name, c.user_id as client_user_id
              FROM shipments s
              JOIN clients c ON s.client_id = c.client_id
              WHERE s.shipment_id = :shipment_id AND c.user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':shipment_id', $shipment_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $_SESSION['error'] = "Shipment not found";
        header("Location: track_shipment.php");
        exit();
    }

    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    // Start transaction
    $db->beginTransaction();

    // Update payment as pending verification (admin needs to verify manual payments)
    $update_payment = $db->prepare("UPDATE payments SET
        payment_method = :payment_method,
        transaction_id = :transaction_ref,
        payment_date = NOW(),
        updated_at = NOW()
        WHERE shipment_id = :shipment_id AND status = 'pending'");
    $update_payment->bindValue(':payment_method', $payment_method);
    $update_payment->bindValue(':transaction_ref', $transaction_ref);
    $update_payment->bindValue(':shipment_id', $shipment_id, PDO::PARAM_INT);
    $update_payment->execute();

    // Log activity
    logActivity($_SESSION['user_id'], 'manual_payment_submitted', 'payments', $shipment_id,
        'Manual payment submitted (' . $payment_method . ') for shipment ' . $shipment['tracking_number']);

    // Notify admin for verification
    notifyAdmin(
        'Manual Payment Submitted - ' . $shipment['tracking_number'],
        'A manual payment (' . $payment_method . ') has been submitted for shipment ' . $shipment['tracking_number'] . ' (' . $shipment['company_name'] . ').<br>Transaction Reference: ' . $transaction_ref . '<br>Please verify the payment.',
        'info',
        'payments',
        $shipment_id
    );

    $db->commit();

    $_SESSION['success'] = "Payment information submitted successfully. An admin will verify your payment shortly.";
    header("Location: track_shipment.php?shipment_id=" . $shipment_id);
    exit();

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    $_SESSION['error'] = "Error processing payment: " . $e->getMessage();
    header("Location: payment.php?shipment_id=" . $shipment_id);
    exit();
}
