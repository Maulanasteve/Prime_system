<?php
require_once 'config.php';
require_once 'database.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// Get shipment ID from URL
$shipment_id = isset($_GET['shipment_id']) ? (int)$_GET['shipment_id'] : 0;

if (!$shipment_id) {
    header("Location: track_shipment.php");
    exit();
}

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get shipment details
try {
    $query = "SELECT s.*, c.company_name, c.user_id as client_user_id FROM shipments s
              JOIN clients c ON s.client_id = c.client_id
              WHERE s.shipment_id = :shipment_id AND c.user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':shipment_id', $shipment_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        header("Location: track_shipment.php");
        exit();
    }

    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading shipment: " . $e->getMessage();
}

// Load pending payment created by agent declaration (amount in MWK, status='pending')
$pending_payment = null;
try {
    $pp = $db->prepare("SELECT * FROM payments WHERE shipment_id = :sid AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
    $pp->bindParam(':sid', $shipment_id, PDO::PARAM_INT);
    $pp->execute();
    $pending_payment = $pp->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading payment: " . $e->getMessage();
}

// Calculate amount in USD for Stripe (Stripe requires smallest currency unit - cents)
$amount_mwk = $pending_payment ? (float)$pending_payment['amount'] : 0;
$exchange_rate_usd_to_mwk = EXCHANGE_RATES['USD'] ?? 1700;
$amount_usd = $amount_mwk / $exchange_rate_usd_to_mwk;
$amount_cents = (int)($amount_usd * 100); // Stripe amount in cents
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Prime Cargo Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-ship me-2"></i>Prime Cargo Limited
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="track_shipment.php">
                    <i class="fas fa-arrow-left me-2"></i>Back to Tracking
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment for Shipment</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!$pending_payment): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>No pending payment found for this shipment.</strong>
                                <p class="mb-0 mt-2">Please contact your agent or admin if you believe this is an error.</p>
                            </div>
                            <a href="track_shipment.php" class="btn btn-secondary">Back to Tracking</a>
                        <?php else: ?>
                            <!-- Shipment Info -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-box me-2"></i>Shipment Details:</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Tracking Number:</strong><br><?php echo htmlspecialchars($shipment['tracking_number']); ?></p>
                                        <p class="mb-1"><strong>Goods Description:</strong><br><?php echo htmlspecialchars($shipment['goods_description']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Origin:</strong> <?php echo htmlspecialchars($shipment['origin_country']); ?></p>
                                        <p class="mb-1"><strong>Destination:</strong> <?php echo htmlspecialchars($shipment['destination_port']); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Amount -->
                            <div class="card mb-4 border-success">
                                <div class="card-body text-center">
                                    <h5 class="text-muted mb-2">Total Amount Due</h5>
                                    <h2 class="text-success mb-2">
                                        MWK <?php echo number_format($amount_mwk, 2); ?>
                                    </h2>
                                    <p class="text-muted mb-0">
                                        <small>(Approx. $<?php echo number_format($amount_usd, 2); ?> USD)</small>
                                    </p>
                                    <hr>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Payment will be processed securely through Stripe
                                    </small>
                                </div>
                            </div>

                            <!-- Payment Methods -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Payment Options</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Stripe Checkout Button -->
                                    <div class="d-grid gap-2">
                                        <button id="stripeCheckoutBtn" class="btn btn-primary btn-lg">
                                            <i class="fab fa-cc-stripe me-2"></i>Pay with Card (Stripe)
                                        </button>
                                        <small class="text-muted text-center">
                                            <i class="fas fa-lock me-1"></i>
                                            Secure payment powered by Stripe. We accept Visa, Mastercard, and more.
                                        </small>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Alternative Payment Methods -->
                                    <div class="accordion" id="altPaymentAccordion">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingBank">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBank">
                                                    <i class="fas fa-university me-2"></i>Bank Transfer
                                                </button>
                                            </h2>
                                            <div id="collapseBank" class="accordion-collapse collapse" data-bs-parent="#altPaymentAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Bank Details:</strong></p>
                                                    <ul>
                                                        <li>Bank: National Bank of Malawi</li>
                                                        <li>Account Name: Prime Cargo Limited</li>
                                                        <li>Account Number: 1234567890</li>
                                                        <li>Branch: Blantyre</li>
                                                    </ul>
                                                    <p class="text-danger"><small>Please include tracking number <strong><?php echo $shipment['tracking_number']; ?></strong> in the reference.</small></p>
                                                    <form method="POST" action="process_manual_payment.php">
                                                        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>">
                                                        <input type="hidden" name="payment_method" value="bank_transfer">
                                                        <div class="mb-3">
                                                            <label class="form-label">Transaction Reference</label>
                                                            <input type="text" name="transaction_ref" class="form-control" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-success">Mark as Paid</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingMobile">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMobile">
                                                    <i class="fas fa-mobile-alt me-2"></i>Mobile Money
                                                </button>
                                            </h2>
                                            <div id="collapseMobile" class="accordion-collapse collapse" data-bs-parent="#altPaymentAccordion">
                                                <div class="accordion-body">
                                                    <p><strong>Airtel Money / TNM Mpamba:</strong></p>
                                                    <ul>
                                                        <li>Account: Prime Cargo Limited</li>
                                                        <li>Number: 0888 123 456</li>
                                                    </ul>
                                                    <p class="text-danger"><small>Please include tracking number <strong><?php echo $shipment['tracking_number']; ?></strong> in the reference.</small></p>
                                                    <form method="POST" action="process_manual_payment.php">
                                                        <input type="hidden" name="shipment_id" value="<?php echo $shipment_id; ?>">
                                                        <input type="hidden" name="payment_method" value="mobile_money">
                                                        <div class="mb-3">
                                                            <label class="form-label">Transaction Reference</label>
                                                            <input type="text" name="transaction_ref" class="form-control" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-success">Mark as Paid</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-light">
                                <h6><i class="fas fa-shield-alt me-2"></i>Secure Payment Guarantee</h6>
                                <ul class="mb-0 small">
                                    <li>All transactions are encrypted and secure</li>
                                    <li>Your payment information is never stored on our servers</li>
                                    <li>Stripe is PCI-DSS Level 1 certified</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($pending_payment): ?>
    <script>
        // Initialize Stripe
        const stripe = Stripe('<?php echo STRIPE_PUBLISHABLE_KEY; ?>');

        // Stripe Checkout Button Handler
        document.getElementById('stripeCheckoutBtn').addEventListener('click', async function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            try {
                // Create checkout session on server
                const response = await fetch('create_stripe_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        shipment_id: <?php echo $shipment_id; ?>,
                        amount: <?php echo $amount_cents; ?>,
                        currency: 'usd'
                    })
                });

                const session = await response.json();

                if (session.error) {
                    alert('Error: ' + session.error);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fab fa-cc-stripe me-2"></i>Pay with Card (Stripe)';
                    return;
                }

                // Redirect to Stripe Checkout
                const result = await stripe.redirectToCheckout({
                    sessionId: session.id
                });

                if (result.error) {
                    alert(result.error.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fab fa-cc-stripe me-2"></i>Pay with Card (Stripe)';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fab fa-cc-stripe me-2"></i>Pay with Card (Stripe)';
            }
        });
    </script>
    <?php endif; ?>
</body>

</html>
