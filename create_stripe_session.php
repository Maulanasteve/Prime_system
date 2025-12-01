<?php
require_once 'config.php';
require_once 'database.php';
require_once 'vendor/autoload.php';

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

    // Verify shipment belongs to this client and get client email
    $query = "SELECT s.*, c.company_name, u.email as client_email
              FROM shipments s
              JOIN clients c ON s.client_id = c.client_id
              JOIN users u ON c.user_id = u.user_id
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
    $client_email = $shipment['client_email'];

    // Create Stripe checkout session
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    $success_url = APP_URL . '/payment_success.php?session_id={CHECKOUT_SESSION_ID}&shipment_id=' . $shipment_id;
    $cancel_url = APP_URL . '/payment_cancel.php?shipment_id=' . $shipment_id;

    $session = \Stripe\Checkout\Session::create([
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
        'customer_email' => $client_email,
        'metadata' => [
            'shipment_id' => $shipment_id,
            'tracking_number' => $shipment['tracking_number'],
            'client_id' => $_SESSION['user_id']
        ]
    ]);

    // Log activity
    logActivity($_SESSION['user_id'], 'stripe_session_created', 'payments', $shipment_id,
        'Created Stripe checkout session for shipment ' . $shipment['tracking_number']);

    header('Content-Type: application/json');
    echo json_encode([
        'id' => $session->id
    ]);

} catch (\Stripe\Exception\ApiErrorException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}