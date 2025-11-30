<?php

/**
 * Configuration File for Prime Cargo Limited
 * Centralized system settings and constants
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'prime_cargo_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('APP_NAME', 'Prime Cargo Limited');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/Prime_system');
define('APP_TIMEZONE', 'Africa/Blantyre');

// File Upload Configuration
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'xls', 'xlsx']);
define('UPLOAD_PATH', 'uploads/');
define('UPLOAD_TEMP_PATH', 'uploads/temp/');

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Tax Configuration (Malawi)
define('VAT_RATE', 16.5); // 16.5%
define('DEFAULT_CURRENCY', 'USD');
define('TARGET_CURRENCY', 'MWK');

// Exchange Rates (Base: USD to MWK)
define('EXCHANGE_RATES', [
    'USD' => 1700,    // 1 USD = 1,700 MWK
    'EUR' => 1850,    // 1 EUR = 1,850 MWK
    'GBP' => 2150,    // 1 GBP = 2,150 MWK
    'CNY' => 235,     // 1 CNY = 235 MWK
    'INR' => 20,      // 1 INR = 20 MWK
    'ZAR' => 90,      // 1 ZAR = 90 MWK
    'KES' => 12,      // 1 KES = 12 MWK
    'NGN' => 1.2,     // 1 NGN = 1.2 MWK
    'GHS' => 140,     // 1 GHS = 140 MWK
    'UGX' => 0.45     // 1 UGX = 0.45 MWK
]);

// Stripe Payment Configuration
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51SXsNjFdEiHBG304ReP8ZzesVglSQzHa5dgJl8PV4czvyWuJN0uk7FbesKO316MUULxVKwaKkcyI9N6iPNMlruNW00zF1TYsD8');
define('STRIPE_SECRET_KEY', 'sk_test_51SXsNjFdEiHBG304wPKFt2nVHMFhlOMA0uawL0vxOZ1BJusQJVaVUYjiG6nETJN0FS0FUIKUR1e76jVy0brSGQtm00Cen2qhgy');
define('STRIPE_CURRENCY', 'usd'); // Stripe charges in USD

// Email Configuration
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@primecargo.mw');
define('SMTP_PASS', '');
define('SMTP_FROM', 'noreply@primecargo.mw');
define('SMTP_FROM_NAME', 'Prime Cargo Limited');

// Pagination
define('ITEMS_PER_PAGE', 20);
define('MAX_PAGES_DISPLAY', 5);

// Date Formats
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'M d, Y');
define('DISPLAY_DATETIME_FORMAT', 'M d, Y H:i');

// Error Reporting (Set to false in production)
define('DEBUG_MODE', true);

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Function to generate CSRF token
function generateCSRFToken()
{
    return $_SESSION['csrf_token'];
}

// Function to validate CSRF token
function validateCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Function to sanitize input
function sanitizeInput($input)
{
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to validate email
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to generate random string
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// Function to format currency
function formatCurrency($amount, $currency = 'USD')
{
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'MWK' => 'MK'
    ];

    $symbol = $symbols[$currency] ?? $currency;
    return $symbol . number_format($amount, 2);
}

// Function to convert currency to MWK
function convertToMWK($amount, $fromCurrency)
{
    if ($fromCurrency === 'MWK') {
        return $amount;
    }

    $rate = EXCHANGE_RATES[$fromCurrency] ?? 1;
    return $amount * $rate;
}

// Function to log activity
function logActivity($userId, $action, $tableName, $recordId = null, $details = '')
{
    try {
        // Check if Database class exists before trying to use it
        if (class_exists('Database')) {
            $database = new Database();
            $db = $database->getConnection();

            if ($db) {
                $query = "INSERT INTO activity_log (user_id, action, table_name, record_id, details, created_at) 
                          VALUES (:user_id, :action, :table_name, :record_id, :details, NOW())";

                $stmt = $db->prepare($query);
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':action', $action);
                $stmt->bindParam(':table_name', $tableName);
                $stmt->bindParam(':record_id', $recordId);
                $stmt->bindParam(':details', $details);

                return $stmt->execute();
            }
        } else {
            // If Database class is not available, just log to error log
            error_log("Activity Log: User $userId performed $action on $tableName" . ($recordId ? " (ID: $recordId)" : "") . " - $details");
        }
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
    return false;
}

function createNotification($userId, $title, $message, $type = 'info', $relatedTable = null, $relatedId = null)
{
    try {
        if (class_exists('Database')) {
            $database = new Database();
            $db = $database->getConnection();

            if ($db) {
                $query = "INSERT INTO notifications (user_id, title, message, type, related_table, related_id, created_at) 
                          VALUES (:user_id, :title, :message, :type, :related_table, :related_id, NOW())";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':message', $message);
                $stmt->bindParam(':type', $type);
                $stmt->bindParam(':related_table', $relatedTable);
                $stmt->bindParam(':related_id', $relatedId);
                return $stmt->execute();
            }
        } else {
            error_log("Notification: Could not create notification for user $userId: $title - $message");
        }
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
    }
    return false;
}

function notifyAdmin($title, $message, $type = 'info', $relatedTable = null, $relatedId = null)
{
    try {
        if (class_exists('Database')) {
            $database = new Database();
            $db = $database->getConnection();

            if ($db) {
                // Get all admin users
                $query = "SELECT user_id FROM users WHERE role = 'admin' AND status = 'active'";
                $stmt = $db->prepare($query);
                $stmt->execute();
                $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($admins as $admin) {
                    createNotification($admin['user_id'], $title, $message, $type, $relatedTable, $relatedId);
                }
                return true;
            }
        }
    } catch (Exception $e) {
        error_log("Error notifying admins: " . $e->getMessage());
    }
    return false;
}
