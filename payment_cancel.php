<?php
require_once 'config.php';
require_once 'database.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.php");
    exit();
}

$shipment_id = isset($_GET['shipment_id']) ? (int)$_GET['shipment_id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - Prime Cargo Limited</title>
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
                        <div class="mb-4">
                            <i class="fas fa-times-circle text-danger" style="font-size: 72px;"></i>
                        </div>
                        <h2 class="text-danger mb-3">Payment Cancelled</h2>
                        <p class="text-muted mb-4">
                            Your payment was cancelled. No charges have been made to your account.
                        </p>
                        <div class="alert alert-info">
                            You can try again or choose an alternative payment method.
                        </div>
                        <a href="payment.php?shipment_id=<?php echo $shipment_id; ?>" class="btn btn-primary btn-lg mt-3">
                            <i class="fas fa-redo me-2"></i>Try Again
                        </a>
                        <a href="track_shipment.php" class="btn btn-outline-secondary btn-lg mt-3 ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Back to Tracking
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
