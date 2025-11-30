<?php
require_once 'config.php';
require_once 'database.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$shipment_id = isset($data['shipment_id']) ? (int)$data['shipment_id'] : 0;
$amount = isset($data['amount']) ? (int)$data['amount'] : 0; // Amount in cents
$currency = isset($data['currency']) ? $data['currency'] : 'usd';

if (!$shipment_id || !$amount) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Verify shipment belongs to this client
    $query = "SELECT s.*, c.company_name
              FROM shipments s
              JOIN clients c ON s.client_id = c.client_id
              WHERE s.shipment_id = :shipment_id AND c.user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':shipment_id', $shipment_id);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Shipment not found']);
        exit();
    }

    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);

    // Create Stripe checkout session using Stripe API
    $stripe_secret = STRIPE_SECRET_KEY;

    $success_url = APP_URL . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}&shipment_id=' . $shipment_id;
    $cancel_url = APP_URL . '/payment_cancel.php?shipment_id=' . $shipment_id;

    // Prepare Stripe API request
    $session_data = [
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => $currency,
                'product_data' => [
                    'name' => 'Customs Clearance - ' . $shipment['tracking_number'],
                    'description' => 'Goods: ' . substr($shipment['goods_description'], 0, 100),
                ],
                'unit_amount' => $amount,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $success_url,
        'cancel_url' => $cancel_url,
        'metadata' => [
            'shipment_id' => $shipment_id,
            'tracking_number' => $shipment['tracking_number'],
            'client_id' => $_SESSION['user_id']
        ]
    ];

    // Make API call to Stripe
    $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $stripe_secret,
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    // Build form-encoded data
    $post_data = http_build_query([
        'payment_method_types[]' => 'card',
        'line_items[0][price_data][currency]' => $currency,
        'line_items[0][price_data][product_data][name]' => 'Customs Clearance - ' . $shipment['tracking_number'],
        'line_items[0][price_data][product_data][description]' => 'Goods: ' . substr($shipment['goods_description'], 0, 100),
        'line_items[0][price_data][unit_amount]' => $amount,
        'line_items[0][quantity]' => 1,
        'mode' => 'payment',
        'success_url' => $success_url,
        'cancel_url' => $cancel_url,
        'metadata[shipment_id]' => $shipment_id,
        'metadata[tracking_number]' => $shipment['tracking_number'],
        'metadata[client_id]' => $_SESSION['user_id']
    ]);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        $error_response = json_decode($response, true);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $error_response['error']['message'] ?? 'Failed to create checkout session'
        ]);
        exit();
    }

    $session = json_decode($response, true);

    // Log activity
    logActivity($_SESSION['user_id'], 'stripe_session_created', 'payments', $shipment_id,
        'Created Stripe checkout session for shipment ' . $shipment['tracking_number']);

    header('Content-Type: application/json');
    echo json_encode([
        'id' => $session['id']
    ]);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
