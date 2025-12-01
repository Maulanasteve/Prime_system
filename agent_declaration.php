<?php
require_once 'config.php';
require_once 'database.php';
require_once __DIR__ . '/includes/FileHandler.php';

// RBAC: Agents only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agent') {
    header('Location: login.php');
    exit();
}

$agent_id = (int)$_SESSION['user_id'];

function rate_to_mwk($base)
{
    $base = strtoupper(substr((string)$base, 0, 3));
    if ($base === 'MWK') return 1.0;
    $fallback = EXCHANGE_RATES[$base] ?? 1.0;
    $url = 'https://api.exchangerate.host/latest?base=' . urlencode($base) . '&symbols=MWK';
    try {
        $ctx = stream_context_create(['http' => ['timeout' => 6]]);
        $resp = @file_get_contents($url, false, $ctx);
        if ($resp === false) return (float)$fallback;
        $data = json_decode($resp, true);
        $rate = isset($data['rates']['MWK']) ? (float)$data['rates']['MWK'] : 0.0;
        return $rate > 0 ? $rate : (float)$fallback;
    } catch (Throwable $e) {
        return (float)$fallback;
    }
}

// Ad valorem tariff rates (fraction) per HS for import duty (similar to calculator)
$tariff_percent = [
    '8471.30.00' => 0.15,
    '5208.52.00' => 0.25,
    '8432.80.00' => 0.10,
    '8517.13.00' => 0.20,
    '8528.72.00' => 0.25,
    '8708.99.00' => 0.15,
    '9503.00.00' => 0.20,
    '6104.43.00' => 0.30,
    '8525.50.00' => 0.20,
    '8471.41.00' => 0.15,
];

// Assessed customs value per kg (in MWK) used to derive Declared Value from HS + Weight
$assessed_value_mwk_per_kg = [
    '8528.72.00' => 40000,
    '8708.99.00' => 25000,
    '5208.52.00' => 15000,
    '6104.43.00' => 18000,
    '8432.80.00' => 20000,
    '8517.13.00' => 35000,
    '9503.00.00' => 12000,
    '8471.30.00' => 30000,
    '8471.41.00' => 32000,
    '8525.50.00' => 28000,
];
$assessed_default_mwk_per_kg = 20000; // fallback per-kg valuation in MWK

// Calculator currencies (for embedded tax calculator UI)
$calc_currencies = [
    'USD' => 'US Dollar',
    'EUR' => 'Euro',
    'GBP' => 'British Pound',
    'MWK' => 'Malawi Kwacha',
    'ZMW' => 'Zambian Kwacha',
    'ZAR' => 'South African Rand',
    'CNY' => 'Chinese Yuan',
    'INR' => 'Indian Rupee',
    'KES' => 'Kenyan Shilling',
    'NGN' => 'Nigerian Naira',
    'GHS' => 'Ghanaian Cedi',
    'UGX' => 'Ugandan Shilling',
];
$full_name = $_SESSION['full_name'] ?? 'Agent';

$shipment_id = isset($_GET['shipment_id']) ? (int)$_GET['shipment_id'] : 0;
$error = '';
$success = '';

$database = new Database();
$db = $database->getConnection();
if (!$db) {
    die('Database connection failed');
}

// Migrations are managed via SQL files. No runtime schema changes here.

// Verify shipment belongs to this agent
$shipment = null;
if ($shipment_id) {
    $stmt = $db->prepare("SELECT s.*, c.company_name FROM shipments s JOIN clients c ON s.client_id = c.client_id WHERE s.shipment_id = :sid AND s.agent_id = :aid");
    $stmt->bindParam(':sid', $shipment_id, PDO::PARAM_INT);
    $stmt->bindParam(':aid', $agent_id, PDO::PARAM_INT);
    $stmt->execute();
    $shipment = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$shipment) {
        header('Location: agent_dashboard.php');
        exit();
    }
}

// Load existing declaration if available
$declaration = null;
if ($shipment_id) {
    $dstmt = $db->prepare("SELECT * FROM declarations WHERE shipment_id = :sid ORDER BY declaration_id DESC LIMIT 1");
    $dstmt->bindParam(':sid', $shipment_id, PDO::PARAM_INT);
    $dstmt->execute();
    $declaration = $dstmt->fetch(PDO::FETCH_ASSOC);
}

// Defaults
$default_currency = $shipment['currency'] ?? 'USD';
$default_fx = EXCHANGE_RATES[$default_currency] ?? (EXCHANGE_RATES['USD'] ?? 1700);
$default_vat = VAT_RATE;

// Basic HS tariff codes and descriptions (extend as needed)
$tariff_codes = [
    '8471.30.00' => 'Portable digital automatic data processing machines',
    '8471.41.00' => 'Laptop computers',
    '8517.13.00' => 'Smartphones',
    '8525.50.00' => 'Digital cameras',
    '8528.72.00' => 'Color television receivers',
    '5208.52.00' => 'Woven fabrics of cotton',
    '6104.43.00' => "Women's dresses, synthetic fibres",
    '8432.80.00' => 'Agricultural machinery',
    '8708.99.00' => 'Motor vehicle parts',
    '9503.00.00' => 'Other toys'
];

// Specific duty rates (MWK per kg) for selected HS codes
$specific_duty_mwk_per_kg = [
    '8528.72.00' => 1200.0, // Color television receivers
    '8708.99.00' => 800.0,  // Motor vehicle parts
    '5208.52.00' => 450.0,  // Woven fabrics of cotton
    '6104.43.00' => 650.0,  // Women's dresses, synthetic fibres
    '8432.80.00' => 500.0,  // Agricultural machinery
];

// Suggest a tariff code from goods description
$suggested_code = null;
try {
    $desc = strtolower(trim($declaration['goods_description'] ?? ($shipment['goods_description'] ?? '')));
    if ($desc !== '') {
        $keyword_map = [
            '8471.41.00' => ['laptop', 'notebook', 'computer', 'macbook'],
            '8471.30.00' => ['tablet', 'ipad'],
            '8517.13.00' => ['phone', 'smartphone', 'iphone', 'android'],
            '8525.50.00' => ['camera', 'digital camera'],
            '8528.72.00' => ['television', 'tv', 'lcd', 'led tv'],
            '5208.52.00' => ['cotton fabric', 'woven cotton', 'fabric cotton'],
            '6104.43.00' => ['dress', 'dresses', 'garment'],
            '8432.80.00' => ['agricultural', 'farm', 'plough', 'tractor implement'],
            '8708.99.00' => ['auto parts', 'vehicle parts', 'spare parts'],
            '9503.00.00' => ['toy', 'toys'],
        ];
        foreach ($keyword_map as $code => $words) {
            foreach ($words as $w) {
                if (strpos($desc, $w) !== false) {
                    $suggested_code = $code;
                    break 2;
                }
            }
        }
    }
} catch (Throwable $e) {
    $suggested_code = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF optional: validate if token present
    $input = sanitizeInput($_POST);

    try {
        $db->beginTransaction();

        // Normalize numeric inputs
        $currency = substr($input['currency'] ?? $default_currency, 0, 3);
        $exchange_rate_live = rate_to_mwk($currency);
        $exchange_rate = ($exchange_rate_live && $exchange_rate_live > 0) ? (float)$exchange_rate_live : (float)($input['exchange_rate'] ?? $default_fx);

        // Get commodity code and gross weight to derive value
        $commodity_code = $input['commodity_code'] ?? null;
        $gross_weight = isset($input['gross_weight']) ? (float)$input['gross_weight'] : 0;

        // Derive declared value from HS and weight (logic from agent_tax_calculation.php)
        $assessed_per_kg_mwk = $assessed_value_mwk_per_kg[$commodity_code] ?? $assessed_default_mwk_per_kg;
        $declared_value_mwk = max(0, $gross_weight) * $assessed_per_kg_mwk;
        $currency_amount = $declared_value_mwk / max(0.000001, $exchange_rate);

        // Auto-calc base by default; allow manual override if provided and > 0
        $tax_base_mwk = (isset($input['tax_base_mwk']) && (float)$input['tax_base_mwk'] > 0)
            ? (float)$input['tax_base_mwk']
            : $declared_value_mwk;

        $vat_rate = isset($input['vat_rate']) ? (float)$input['vat_rate'] : (float)$default_vat;

        // Ad valorem import duty + VAT on (declared + duty)
        $declared_mwk = $declared_value_mwk;
        $ad_valorem_rate = 0.0;
        if (!empty($commodity_code) && isset($tariff_percent[$commodity_code])) {
            $ad_valorem_rate = (float)$tariff_percent[$commodity_code];
        }
        $duty_mwk = $declared_mwk * $ad_valorem_rate;
        $vat_amount_mwk = ($vat_rate !== null) ? (($declared_mwk + $duty_mwk) * ((float)$vat_rate / 100.0)) : 0.0;
        $total_tax_mwk = $duty_mwk + $vat_amount_mwk;
        $total_declaration_mwk = $declared_mwk + $total_tax_mwk;

        // Additional tax rates and amounts (currently not used but fields exist)
        $ait_rate = null;
        $ait_amount_mwk = 0;
        $icd_rate = null;
        $icd_amount_mwk = 0;
        $exc_rate = null;
        $exc_amount_mwk = 0;
        $lev_rate = null;
        $lev_amount_mwk = 0;

        $total_fees_mwk = $total_tax_mwk;
        // Tax base equals declared value in MWK
        $tax_base_mwk = $declared_mwk;

        // Other fields
        $mode_of_payment = $input['mode_of_payment'] ?? null;
        $assessment_number = $input['assessment_number'] ?? null;
        $receipt_number = $input['receipt_number'] ?? null;
        $assessment_date = $input['assessment_date'] ?? null;
        $receipt_date = $input['receipt_date'] ?? null;

        // Party fields (basic)
        $consignor_name = $input['consignor_name'] ?? null;
        $consignee_name = $input['consignee_name'] ?? null;
        $consignee_address = $input['consignee_address'] ?? null;
        $declarant_name = $input['declarant_name'] ?? null;
        $declaration_type = $input['declaration_type'] ?? null;
        $clearance_office = $input['clearance_office'] ?? null;
        $registration_no = $input['registration_no'] ?? null;
        $registration_date = $input['registration_date'] ?? null;

        // Countries & routing
        $country_consignor = $input['country_consignor'] ?? null;
        $country_dispatch = $input['country_dispatch'] ?? null;
        $country_origin = $input['country_origin'] ?? null;
        $country_destination = $input['country_destination'] ?? null;
        $delivery_terms = $input['delivery_terms'] ?? null;
        $delivery_place = $input['delivery_place'] ?? null;
        $nature_of_transaction = $input['nature_of_transaction'] ?? null;

        // Transport & location
        $means_of_transport_arrival = $input['means_of_transport_arrival'] ?? null;
        $means_of_transport_border = $input['means_of_transport_border'] ?? null;
        $departure_date = $input['departure_date'] ?? null;
        $place_loading = $input['place_loading'] ?? null;
        $location_goods = $input['location_goods'] ?? null;
        $office_entry_exit = $input['office_entry_exit'] ?? null;
        $warehouse_code = $input['warehouse_code'] ?? null;
        $period = $input['period'] ?? null;

        // Goods metrics & tariff
        $packages_description = $input['packages_description'] ?? null;
        $package_type = $input['package_type'] ?? null;
        $num_packages = isset($input['num_packages']) ? (int)$input['num_packages'] : null;
        $net_weight = isset($input['net_weight']) ? (float)$input['net_weight'] : null;
        // $commodity_code already defined above for tax calculations
        $invoice_value = isset($input['invoice_value']) ? (float)$input['invoice_value'] : null;
        $units = isset($input['units']) ? (int)$input['units'] : null;
        $statistical_value_mwk = isset($input['statistical_value_mwk']) ? (float)$input['statistical_value_mwk'] : null;
        $previous_document_ref = $input['previous_document_ref'] ?? null;

        // Note: tax_base_mwk already set to declared_mwk; duty handled above

        // Banking & guarantee & validation
        $bank_code = $input['bank_code'] ?? null;
        $guarantee = $input['guarantee'] ?? null;
        $validation_ref = $input['validation_ref'] ?? null;

        // Insert or update declaration
        if ($declaration) {
            $sql = "UPDATE declarations SET
                        consignor_name = :consignor_name,
                        consignee_name = :consignee_name,
                        consignee_address = :consignee_address,
                        declarant_name = :declarant_name,
                        declaration_type = :declaration_type,
                        clearance_office = :clearance_office,
                        registration_no = :registration_no,
                        registration_date = :registration_date,
                        country_consignor = :country_consignor,
                        country_dispatch = :country_dispatch,
                        country_origin = :country_origin,
                        country_destination = :country_destination,
                        delivery_terms = :delivery_terms,
                        delivery_place = :delivery_place,
                        means_of_transport_arrival = :means_of_transport_arrival,
                        means_of_transport_border = :means_of_transport_border,
                        departure_date = :departure_date,
                        place_loading = :place_loading,
                        location_goods = :location_goods,
                        office_entry_exit = :office_entry_exit,
                        currency = :currency,
                        currency_amount = :currency_amount,
                        exchange_rate = :exchange_rate,
                        statistical_value_mwk = :statistical_value_mwk,
                        tax_base_mwk = :tax_base_mwk,
                        ait_rate = :ait_rate, ait_amount_mwk = :ait_amount_mwk,
                        icd_rate = :icd_rate, icd_amount_mwk = :icd_amount_mwk,
                        exc_rate = :exc_rate, exc_amount_mwk = :exc_amount_mwk,
                        vat_rate = :vat_rate, vat_amount_mwk = :vat_amount_mwk,
                        lev_rate = :lev_rate, lev_amount_mwk = :lev_amount_mwk,
                        total_fees_mwk = :total_fees_mwk,
                        total_declaration_mwk = :total_declaration_mwk,
                        packages_description = :packages_description,
                        package_type = :package_type,
                        num_packages = :num_packages,
                        gross_weight = :gross_weight,
                        net_weight = :net_weight,
                        commodity_code = :commodity_code,
                        invoice_value = :invoice_value,
                        units = :units,
                        previous_document_ref = :previous_document_ref,
                        mode_of_payment = :mode_of_payment,
                        assessment_number = :assessment_number,
                        assessment_date = :assessment_date,
                        receipt_number = :receipt_number,
                        receipt_date = :receipt_date,
                        bank_code = :bank_code,
                        guarantee = :guarantee,
                        notes = :notes
                    WHERE declaration_id = :did";
        } else {
            $sql = "INSERT INTO declarations (
                        shipment_id, consignor_name, consignee_name, consignee_address, declarant_name,
                        declaration_type, clearance_office, registration_no, registration_date,
                        country_consignor, country_dispatch, country_origin, country_destination,
                        delivery_terms, delivery_place,
                        means_of_transport_arrival, means_of_transport_border, departure_date, place_loading, location_goods, office_entry_exit,
                        currency, currency_amount, exchange_rate, statistical_value_mwk, tax_base_mwk,
                        ait_rate, ait_amount_mwk, icd_rate, icd_amount_mwk, exc_rate, exc_amount_mwk,
                        vat_rate, vat_amount_mwk, lev_rate, lev_amount_mwk,
                        total_fees_mwk, total_declaration_mwk,
                        packages_description, package_type, num_packages, gross_weight, net_weight,
                        commodity_code, invoice_value, units,
                        mode_of_payment, assessment_number, assessment_date, receipt_number, receipt_date, bank_code, guarantee,
                        notes, created_by
                    ) VALUES (
                        :shipment_id, :consignor_name, :consignee_name, :consignee_address, :declarant_name,
                        :declaration_type, :clearance_office, :registration_no, :registration_date,
                        :country_consignor, :country_dispatch, :country_origin, :country_destination,
                        :delivery_terms, :delivery_place,
                        :means_of_transport_arrival, :means_of_transport_border, :departure_date, :place_loading, :location_goods, :office_entry_exit,
                        :currency, :currency_amount, :exchange_rate, :statistical_value_mwk, :tax_base_mwk,
                        :ait_rate, :ait_amount_mwk, :icd_rate, :icd_amount_mwk, :exc_rate, :exc_amount_mwk,
                        :vat_rate, :vat_amount_mwk, :lev_rate, :lev_amount_mwk,
                        :total_fees_mwk, :total_declaration_mwk,
                        :packages_description, :package_type, :num_packages, :gross_weight, :net_weight,
                        :commodity_code, :invoice_value, :units,
                        :mode_of_payment, :assessment_number, :assessment_date, :receipt_number, :receipt_date, :bank_code, :guarantee,
                        :notes, :created_by
                    )";
        }

        $stmt = $db->prepare($sql);
        if (!$declaration) {
            $stmt->bindParam(':shipment_id', $shipment_id, PDO::PARAM_INT);
            $stmt->bindParam(':created_by', $agent_id, PDO::PARAM_INT);
        } else {
            $stmt->bindParam(':did', $declaration['declaration_id'], PDO::PARAM_INT);
        }

        $notes = $input['notes'] ?? null;
        $stmt->bindParam(':consignor_name', $consignor_name);
        $stmt->bindParam(':consignee_name', $consignee_name);
        $stmt->bindParam(':consignee_address', $consignee_address);
        $stmt->bindParam(':declarant_name', $declarant_name);
        $stmt->bindParam(':declaration_type', $declaration_type);
        $stmt->bindParam(':clearance_office', $clearance_office);
        $stmt->bindParam(':registration_no', $registration_no);
        $stmt->bindParam(':registration_date', $registration_date);
        $stmt->bindParam(':country_consignor', $country_consignor);
        $stmt->bindParam(':country_dispatch', $country_dispatch);
        $stmt->bindParam(':country_origin', $country_origin);
        $stmt->bindParam(':country_destination', $country_destination);
        $stmt->bindParam(':delivery_terms', $delivery_terms);
        $stmt->bindParam(':delivery_place', $delivery_place);
        $stmt->bindParam(':means_of_transport_arrival', $means_of_transport_arrival);
        $stmt->bindParam(':means_of_transport_border', $means_of_transport_border);
        $stmt->bindParam(':departure_date', $departure_date);
        $stmt->bindParam(':place_loading', $place_loading);
        $stmt->bindParam(':location_goods', $location_goods);
        $stmt->bindParam(':office_entry_exit', $office_entry_exit);
        $stmt->bindParam(':currency', $currency);
        $stmt->bindParam(':currency_amount', $currency_amount);
        $stmt->bindParam(':exchange_rate', $exchange_rate);
        $stmt->bindParam(':statistical_value_mwk', $statistical_value_mwk);
        $stmt->bindParam(':tax_base_mwk', $tax_base_mwk);
        $stmt->bindParam(':ait_rate', $ait_rate);
        $stmt->bindParam(':ait_amount_mwk', $ait_amount_mwk);
        $stmt->bindParam(':icd_rate', $icd_rate);
        $stmt->bindParam(':icd_amount_mwk', $icd_amount_mwk);
        $stmt->bindParam(':exc_rate', $exc_rate);
        $stmt->bindParam(':exc_amount_mwk', $exc_amount_mwk);
        $stmt->bindParam(':vat_rate', $vat_rate);
        $stmt->bindParam(':vat_amount_mwk', $vat_amount_mwk);
        $stmt->bindParam(':lev_rate', $lev_rate);
        $stmt->bindParam(':lev_amount_mwk', $lev_amount_mwk);
        $stmt->bindParam(':total_fees_mwk', $total_fees_mwk);
        $stmt->bindParam(':total_declaration_mwk', $total_declaration_mwk);
        $stmt->bindParam(':packages_description', $packages_description);
        $stmt->bindParam(':package_type', $package_type);
        $stmt->bindParam(':num_packages', $num_packages);
        $stmt->bindParam(':gross_weight', $gross_weight);
        $stmt->bindParam(':net_weight', $net_weight);
        $stmt->bindParam(':commodity_code', $commodity_code);
        $stmt->bindParam(':invoice_value', $invoice_value);
        $stmt->bindParam(':units', $units);
        $stmt->bindParam(':mode_of_payment', $mode_of_payment);
        $stmt->bindParam(':assessment_number', $assessment_number);
        $stmt->bindParam(':assessment_date', $assessment_date);
        $stmt->bindParam(':receipt_number', $receipt_number);
        $stmt->bindParam(':receipt_date', $receipt_date);
        $stmt->bindParam(':bank_code', $bank_code);
        $stmt->bindParam(':guarantee', $guarantee);
        $stmt->bindParam(':notes', $notes);

        $stmt->execute();

        $decl_id = $declaration ? (int)$declaration['declaration_id'] : (int)$db->lastInsertId();

        // Propagate HS code to shipments table if provided
        if (!empty($commodity_code)) {
            try {
                $upShip = $db->prepare("UPDATE shipments SET tariff_number = :code, updated_at = NOW() WHERE shipment_id = :sid");
                $upShip->bindValue(':code', $commodity_code);
                $upShip->bindValue(':sid', $shipment_id, PDO::PARAM_INT);
                $upShip->execute();
            } catch (Exception $eUp) { /* non-blocking */
            }
        }

        // Optional: upload scanned form
        if (!empty($_FILES['scanned_form']['name'])) {
            $fh = new FileHandler();
            $uploadResult = $fh->uploadFile($_FILES['scanned_form'], 'declarations', $decl_id);
            if ($uploadResult) {
                $up = $db->prepare("UPDATE declarations SET scanned_form_path = :p WHERE declaration_id = :id");
                $up->bindValue(':p', $uploadResult['filepath']);
                $up->bindValue(':id', $decl_id, PDO::PARAM_INT);
                $up->execute();
            } else {
                $error = 'Scanned form upload failed: ' . ($fh->getLastError() ?: 'Unknown error');
            }
        }

        // Update shipment cached tax amounts for convenience
        $us = $db->prepare("UPDATE shipments SET tax_amount = :usd, tax_amount_mwk = :mwk, updated_at = NOW() WHERE shipment_id = :sid");
        // Store USD equivalent approx if currency is USD else 0 (could convert back if needed)
        $usd_equiv = $currency === 'USD' ? $total_fees_mwk / max(1, (float)(EXCHANGE_RATES['USD'] ?? $default_fx)) : 0;
        $us->bindValue(':usd', $usd_equiv);
        $us->bindValue(':mwk', $total_fees_mwk);
        $us->bindValue(':sid', $shipment_id, PDO::PARAM_INT);
        $us->execute();

        $db->commit();

        // Create or update a pending payment request for the client (amount in MWK)
        try {
            // Determine client user responsible for this shipment
            $cstmt = $db->prepare("SELECT u.user_id AS client_user_id, u.email AS client_email, c.company_name
                                   FROM shipments s
                                   JOIN clients c ON s.client_id = c.client_id
                                   JOIN users u ON c.user_id = u.user_id
                                   WHERE s.shipment_id = :sid LIMIT 1");
            $cstmt->bindValue(':sid', $shipment_id, PDO::PARAM_INT);
            $cstmt->execute();
            $cinfo = $cstmt->fetch(PDO::FETCH_ASSOC);

            if ($cinfo && !empty($cinfo['client_user_id'])) {
                $client_user_id = (int)$cinfo['client_user_id'];
                $company_name = $cinfo['company_name'] ?? '';

                // Upsert a pending payment for this shipment
                $amount_mwk = (float)$total_declaration_mwk;
                $currency_code = 'MWK';

                $pchk = $db->prepare("SELECT payment_id FROM payments WHERE shipment_id = :sid AND status = 'pending' ORDER BY created_at DESC LIMIT 1");
                $pchk->bindValue(':sid', $shipment_id, PDO::PARAM_INT);
                $pchk->execute();
                $p = $pchk->fetch(PDO::FETCH_ASSOC);

                if ($p) {
                    $upd = $db->prepare("UPDATE payments SET amount = :amt, currency = :cur, updated_at = NOW() WHERE payment_id = :pid");
                    $upd->bindValue(':amt', $amount_mwk);
                    $upd->bindValue(':cur', $currency_code);
                    $upd->bindValue(':pid', (int)$p['payment_id'], PDO::PARAM_INT);
                    $upd->execute();
                    $payment_id = (int)$p['payment_id'];
                } else {
                    $ins = $db->prepare("INSERT INTO payments (shipment_id, amount, currency, status, created_at) VALUES (:sid, :amt, :cur, 'pending', NOW())");
                    $ins->bindValue(':sid', $shipment_id, PDO::PARAM_INT);
                    $ins->bindValue(':amt', $amount_mwk);
                    $ins->bindValue(':cur', $currency_code);
                    $ins->execute();
                    $payment_id = (int)$db->lastInsertId();
                }

                // Notify client with a message about payment request
                try {
                    $subject = 'Payment Request for Shipment #' . ($shipment['tracking_number'] ?? $shipment_id);
                    $payLink = rtrim(APP_URL, '/') . '/payment.php?shipment_id=' . urlencode((string)$shipment_id);
                    $content = 'Dear client, a payment request of MWK ' . number_format($amount_mwk, 2) . ' has been issued for your shipment ' . ($shipment['tracking_number'] ?? ('#' . $shipment_id)) . ".\n" .
                        'To pay now, please open: ' . $payLink;
                    $msg = $db->prepare("INSERT INTO messages (sender_id, recipient_id, shipment_id, subject, content, message_type, created_at) VALUES (:sid, :rid, :shid, :subj, :cont, 'payment_request', NOW())");
                    $msg->bindValue(':sid', $agent_id, PDO::PARAM_INT);
                    $msg->bindValue(':rid', $client_user_id, PDO::PARAM_INT);
                    $msg->bindValue(':shid', $shipment_id, PDO::PARAM_INT);
                    $msg->bindValue(':subj', $subject);
                    $msg->bindValue(':cont', $content);
                    $msg->execute();

                    // Optional email notification to client
                    $clientEmail = $cinfo['client_email'] ?? '';
                    if (!empty($clientEmail) && filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
                        sendEmail($clientEmail, $subject, $content);
                    }
                } catch (Exception $em) { /* non-blocking */
                }
            }
        } catch (Exception $ep) { /* non-blocking */
        }

        logActivity($agent_id, 'save_declaration', 'declarations', $decl_id, 'Saved declaration for shipment #' . $shipment_id);
        $success = 'Declaration saved successfully.';

        // Reload declaration
        $dstmt = $db->prepare("SELECT * FROM declarations WHERE declaration_id = :id");
        $dstmt->bindParam(':id', $decl_id, PDO::PARAM_INT);
        $dstmt->execute();
        $declaration = $dstmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $db->rollBack();
        $error = 'Error saving declaration: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customs Declaration - Prime Cargo Limited</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        #calculateTaxBtn {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        #calculateTaxBtn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        #calculateTaxBtn:disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }
        #taxResultsCard {
            animation: slideDown 0.4s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-ship me-2"></i>Prime Cargo Limited
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white" href="agent_dashboard.php">
                    <i class="fas fa-user-tie me-2"></i>Agent Dashboard
                </a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-file-invoice-dollar me-2"></i>Customs Declaration</h2>
                    <?php if ($shipment): ?>
                        <p class="text-muted mb-0">Shipment: <strong><?php echo htmlspecialchars($shipment['tracking_number']); ?></strong> — <?php echo htmlspecialchars($shipment['company_name']); ?></p>
                    <?php endif; ?>
                </div>
                <a href="dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header"><strong>Parties & References</strong></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Consignor</label>
                                    <input type="text" class="form-control" name="consignor_name" value="<?php echo htmlspecialchars($declaration['consignor_name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Consignee</label>
                                    <input type="text" class="form-control" name="consignee_name" value="<?php echo htmlspecialchars($declaration['consignee_name'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Consignee Address</label>
                                    <textarea class="form-control" name="consignee_address" rows="2"><?php echo htmlspecialchars($declaration['consignee_address'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Declarant</label>
                                    <input type="text" class="form-control" name="declarant_name" value="<?php echo htmlspecialchars($declaration['declarant_name'] ?? $full_name); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Declaration Type</label>
                                    <input type="text" class="form-control" name="declaration_type" value="<?php echo htmlspecialchars($declaration['declaration_type'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Clearance Office</label>
                                    <input type="text" class="form-control" name="clearance_office" value="<?php echo htmlspecialchars($declaration['clearance_office'] ?? 'CHILEKA INTERNATIONAL'); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Registration No</label>
                                    <input type="text" class="form-control" name="registration_no" value="<?php echo htmlspecialchars($declaration['registration_no'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Registration Date</label>
                                    <input type="date" class="form-control" name="registration_date" value="<?php echo htmlspecialchars($declaration['registration_date'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header"><strong>Tax Calculator</strong></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">HS Tariff Code <span class="text-danger">*</span></label>
                                    <select class="form-select" id="calculator_hs">
                                        <option value="">Select Tariff Code</option>
                                        <?php $calc_current_code = $declaration['commodity_code'] ?? ($suggested_code ?? ''); ?>
                                        <?php foreach ($tariff_codes as $code => $label): ?>
                                            <option value="<?php echo $code; ?>" <?php echo ($calc_current_code === $code) ? 'selected' : ''; ?>><?php echo $code; ?> - <?php echo $label; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Select the appropriate Harmonized System code</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Currency <span class="text-danger">*</span></label>
                                    <select class="form-select" id="currencySelect">
                                        <option value="">Select Currency</option>
                                        <?php $calc_curr = $declaration['currency'] ?? $default_currency; ?>
                                        <?php foreach ($calc_currencies as $cc => $cname): ?>
                                            <option value="<?php echo $cc; ?>" <?php echo ($calc_curr === $cc) ? 'selected' : ''; ?>><?php echo $cc; ?> — <?php echo $cname; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text">Live rate to MWK (info, not used in amount): <strong id="ratePreview">—</strong></div>
                                </div>
                                <input type="hidden" name="currency" id="currency_hidden" value="<?php echo htmlspecialchars($declaration['currency'] ?? $default_currency); ?>">

                                <div class="col-md-3">
                                    <label class="form-label">Currency Amount</label>
                                    <input type="number" step="0.01" class="form-control" name="currency_amount" value="<?php echo htmlspecialchars($declaration['currency_amount'] ?? '0'); ?>" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Exchange Rate</label>
                                    <input type="number" step="0.000001" class="form-control" name="exchange_rate" value="<?php echo htmlspecialchars($declaration['exchange_rate'] ?? $default_fx); ?>" required readonly>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tax Base (MWK)</label>
                                    <input type="number" step="0.01" class="form-control" name="tax_base_mwk" value="<?php echo htmlspecialchars($declaration['tax_base_mwk'] ?? '0'); ?>" readonly>
                                    <div class="form-text">Auto-calculated</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gross Weight (kg)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="gross_weight" id="gross_weight_field" value="<?php echo htmlspecialchars($declaration['gross_weight'] ?? ''); ?>">
                                </div>

                                <input type="hidden" name="commodity_code" id="commodity_code_hidden" value="<?php echo htmlspecialchars($declaration['commodity_code'] ?? ($suggested_code ?? '')); ?>">
                                
                                <div class="col-12">
                                    <label class="form-label">Goods Description</label>
                                    <textarea class="form-control" name="goods_description" rows="2"><?php echo htmlspecialchars($declaration['goods_description'] ?? ($shipment['goods_description'] ?? '')); ?></textarea>
                                </div>

                                <!-- Calculate Button -->
                                <div class="col-12 mt-3">
                                    <button type="button" id="calculateTaxBtn" class="btn btn-success btn-lg w-100" disabled>
                                        <i class="fas fa-calculator me-2"></i>Calculate Taxes
                                    </button>
                                    <small class="text-muted d-block mt-2 text-center" id="calculateHint">
                                        Please select HS Code, Currency, and enter Weight to calculate taxes
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header"><strong><i class="fas fa-money-bill-wave me-2"></i>Amount</strong></div>
                        <div class="card-body text-center">
                            <div class="text-muted small mb-1">Amount (MWK)</div>
                            <div id="amountDisplay" class="display-6 fw-bold">0.00</div>
                            <div class="form-text">Auto-calculated from HS, weight, and invoice</div>
                            <div id="amountHint" class="small text-warning mt-2" style="display:none;">Enter Gross Weight or provide a packing list value to compute amount.</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Declaration
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-4" id="taxResultsCard" style="display: none;">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <strong><i class="fas fa-check-circle me-2"></i>Tax Calculation Results</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6>Import Duty</h6>
                            <div>Rate: <span id="dutyRatePct">0%</span></div>
                            <div>Amount: <span id="dutyAmountCur">—</span></div>
                            <div class="text-muted">Amount (MWK): <span id="dutyAmountMWK">—</span></div>
                        </div>
                        <div class="col-md-6">
                            <h6>VAT (<span id="vatRatePct">16.5%</span>)</h6>
                            <div>Base: <span id="vatBaseCur">—</span></div>
                            <div class="text-muted">Base (MWK): <span id="vatBaseMWK">—</span></div>
                            <div>Amount: <span id="vatAmountCur">—</span></div>
                            <div class="text-muted">Amount (MWK): <span id="vatAmountMWK">—</span></div>
                        </div>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6>Summary (<span id="summaryCurCode">—</span>)</h6>
                            <div>Declared Value: <span id="sumCurDeclared">—</span></div>
                            <div>Total Taxes: <span id="sumCurTotalTaxes">—</span></div>
                            <div>Import Duty: <span id="sumCurDuty">—</span></div>
                            <div>VAT: <span id="sumCurVAT">—</span></div>
                        </div>
                        <div class="col-md-6">
                            <h6>Summary (Malawi Kwacha - MWK)</h6>
                            <div>Declared Value: <span id="sumMWKDeclared">—</span></div>
                            <div>Total Taxes: <span id="sumMWKTotalTaxes">—</span></div>
                            <div>Import Duty: <span id="sumMWKDuty">—</span></div>
                            <div>VAT: <span id="sumMWKVAT">—</span></div>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <strong>Total Amount Due:</strong> <span id="totalDueCur">—</span> / <span id="totalDueMWK">—</span>
                    </div>
                    <div class="text-muted" id="exchangeInfo">Exchange Rate: —</div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6>Tax Information</h6>
                            <div class="small">Tax Calculation Guide:</div>
                            <ul class="small mb-0">
                                <li>Import duty rates vary by HS code</li>
                                <li>VAT is calculated on value + duty</li>
                                <li>Rates are based on Malawi customs</li>
                                <li>Always verify with customs authorities</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Important Notes:</h6>
                            <ul class="small mb-0">
                                <li>Tax rates may change</li>
                                <li>Special exemptions may apply</li>
                                <li>Verify with MRA for accuracy</li>
                                <li>Keep records for audit purposes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-compute like the Tax Calculator (Declared + Duty + VAT)
        (function() {
            const specificDuty = <?php echo json_encode($specific_duty_mwk_per_kg ?? []); ?>;
            const defaultCurrency = <?php echo json_encode($default_currency ?? 'USD'); ?>;
            const tariffPercent = <?php echo json_encode($tariff_percent ?? []); ?>; // ad valorem duty fraction per HS
            const fallbackRates = <?php echo json_encode(EXCHANGE_RATES ?? []); ?>; // per-currency fallback MWK rates
            const assessedValuePerKg = <?php echo json_encode($assessed_value_mwk_per_kg ?? []); ?>;
            const assessedDefaultPerKg = <?php echo json_encode($assessed_default_mwk_per_kg ?? 20000); ?>;
            const defaultSpecificDuty = 500.0; // MWK per kg fallback when HS has no explicit rate
            const VAT_FRAC = <?php echo json_encode(((float)VAT_RATE) / 100.0); ?>;

            async function fetchRate(base) {
                const preview = document.getElementById('ratePreview');
                if (!base) {
                    if (preview) preview.textContent = '—';
                    return 1.0;
                }
                try {
                    const res = await fetch('https://api.exchangerate.host/latest?base=' + encodeURIComponent(base) + '&symbols=MWK');
                    if (!res.ok) throw new Error('rate http');
                    const data = await res.json();
                    const rate = (data && data.rates && data.rates.MWK) ? parseFloat(data.rates.MWK) : NaN;
                    if (!isFinite(rate)) throw new Error('rate parse');
                    if (preview) preview.textContent = rate.toFixed(2);
                    return rate;
                } catch (e) {
                    // Use per-currency fallback if available
                    const fb = (typeof base === 'string' && fallbackRates[base]) ? parseFloat(fallbackRates[base]) : 1.0;
                    if (preview) preview.textContent = isFinite(fb) ? fb.toFixed(2) : '—';
                    return isFinite(fb) ? fb : 1.0;
                }
            }

            // Ensure currency changes immediately update rate and UI
            async function setCurrencyAndRate(curCode) {
                const code = curCode || defaultCurrency;
                const hiddenCur = document.getElementById('currency_hidden');
                if (hiddenCur) hiddenCur.value = code;
                const fRate = document.querySelector('input[name="exchange_rate"]');
                const preview = document.getElementById('ratePreview');
                // Set fallback instantly for responsiveness
                const fb = (fallbackRates && fallbackRates[code]) ? parseFloat(fallbackRates[code]) : NaN;
                if (isFinite(fb) && fb > 0) {
                    if (fRate) fRate.value = fb.toFixed(6);
                    if (preview) preview.textContent = fb.toFixed(2);
                }
                await computeAuto();
                // Then fetch live and overwrite
                const live = await fetchRate(code);
                if (isFinite(live) && live > 0) {
                    if (fRate) fRate.value = live.toFixed(6);
                    if (preview) preview.textContent = live.toFixed(2);
                    await computeAuto();
                }
            }

            function lockFields(lock) {
                const fCur = document.querySelector('input[name="currency"]');
                const fAmt = document.querySelector('input[name="currency_amount"]');
                const fRate = document.querySelector('input[name="exchange_rate"]');
                if (fCur) fCur.readOnly = !!lock;
                if (fAmt) fAmt.readOnly = !!lock;
                if (fRate) fRate.readOnly = !!lock;
            }

            async function computeAuto() {
                const hsCalc = document.getElementById('calculator_hs');
                const fCur = document.querySelector('input[name="currency"]');
                const fAmt = document.querySelector('input[name="currency_amount"]');
                const fRate = document.querySelector('input[name="exchange_rate"]');
                const fBase = document.querySelector('input[name="tax_base_mwk"]');
                const fGW = document.getElementById('gross_weight_field');
                const code = hsCalc ? hsCalc.value : '';
                // Keep hidden commodity_code in sync
                const hiddenHS = document.getElementById('commodity_code_hidden');
                if (hiddenHS) hiddenHS.value = code;
                // Currency from field or default
                const curSelect = document.getElementById('currencySelect');
                const cur = (curSelect && curSelect.value) ? curSelect.value : ((fCur && fCur.value) ? fCur.value : defaultCurrency);
                if (fCur) fCur.value = cur;

                // FX: use current field value if already set by currency change; otherwise fetch
                let rate = (fRate && isFinite(parseFloat(fRate.value))) ? parseFloat(fRate.value) : NaN;
                if (!isFinite(rate) || rate <= 0) {
                    rate = await fetchRate(cur);
                    if (fRate) fRate.value = rate.toFixed(6);
                }

                // Derive declared value from HS and weight
                const grossWeight = fGW ? parseFloat(fGW.value || '0') : 0;
                const assessedPerKg = (code && Object.prototype.hasOwnProperty.call(assessedValuePerKg, code)) ? parseFloat(assessedValuePerKg[code]) : assessedDefaultPerKg;
                const declaredMWK = grossWeight * assessedPerKg;
                const amount = declaredMWK / rate; // This is currency amount

                if (fAmt) fAmt.value = (isFinite(amount) ? amount.toFixed(2) : '0.00');
                if (fBase) fBase.value = declaredMWK.toFixed(2);
                
                // Ad valorem duty and VAT (match server)
                const adRate = (code && Object.prototype.hasOwnProperty.call(tariffPercent, code)) ? parseFloat(tariffPercent[code]) : 0;
                const dutyCur = (isFinite(amount) ? amount : 0) * adRate;
                const dutyMWK = dutyCur * rate;
                const vatBaseCur = (isFinite(amount) ? amount : 0) + dutyCur;
                const vatBaseMWK = vatBaseCur * rate;
                const vatCur = vatBaseCur * VAT_FRAC;
                const vatMWK = vatCur * rate;
                const taxesCur = dutyCur + vatCur;
                const taxesMWK = dutyMWK + vatMWK;
                const totalDueCur = (isFinite(amount) ? amount : 0) + taxesCur;
                const totalMWK = declaredMWK + taxesMWK;
                // UI display
                const amtEl = document.getElementById('amountDisplay');
                if (amtEl) {
                    amtEl.textContent = isFinite(totalMWK) ? totalMWK.toFixed(2) : '0.00';
                }
                const hint = document.getElementById('amountHint');
                if (hint) {
                    hint.style.display = (totalMWK <= 0.000001) ? 'block' : 'none';
                }
                lockFields(true);

                // Results panel updates
                const codeLabel = cur || defaultCurrency;
                const fmtCur = (v) => `${codeLabel} ${isFinite(v)?v.toFixed(2):'0.00'}`;
                const fmtMWK = (v) => `MWK ${isFinite(v)?v.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}):'0.00'}`;
                const pct = (v) => `${((isFinite(v)?v:0)*100).toFixed(1)}%`;
                const dutyRateEl = document.getElementById('dutyRatePct');
                if (dutyRateEl) dutyRateEl.textContent = pct(adRate);
                const dutyCurEl = document.getElementById('dutyAmountCur');
                if (dutyCurEl) dutyCurEl.textContent = fmtCur(dutyCur);
                const dutyMWKEl = document.getElementById('dutyAmountMWK');
                if (dutyMWKEl) dutyMWKEl.textContent = fmtMWK(dutyMWK);
                const vatRateEl = document.getElementById('vatRatePct');
                if (vatRateEl) vatRateEl.textContent = `${(VAT_FRAC*100).toFixed(1)}%`;
                const vatBaseCurEl = document.getElementById('vatBaseCur');
                if (vatBaseCurEl) vatBaseCurEl.textContent = fmtCur(vatBaseCur);
                const vatBaseMWKEl = document.getElementById('vatBaseMWK');
                if (vatBaseMWKEl) vatBaseMWKEl.textContent = fmtMWK(vatBaseMWK);
                const vatCurEl = document.getElementById('vatAmountCur');
                if (vatCurEl) vatCurEl.textContent = fmtCur(vatCur);
                const vatMWKEl = document.getElementById('vatAmountMWK');
                if (vatMWKEl) vatMWKEl.textContent = fmtMWK(vatMWK);
                const curCodeEl = document.getElementById('summaryCurCode');
                if (curCodeEl) curCodeEl.textContent = codeLabel;
                const sCurDecl = document.getElementById('sumCurDeclared');
                if (sCurDecl) sCurDecl.textContent = fmtCur(amount);
                const sCurTaxes = document.getElementById('sumCurTotalTaxes');
                if (sCurTaxes) sCurTaxes.textContent = fmtCur(taxesCur);
                const sCurDuty = document.getElementById('sumCurDuty');
                if (sCurDuty) sCurDuty.textContent = fmtCur(dutyCur);
                const sCurVAT = document.getElementById('sumCurVAT');
                if (sCurVAT) sCurVAT.textContent = fmtCur(vatCur);
                const sMWKDecl = document.getElementById('sumMWKDeclared');
                if (sMWKDecl) sMWKDecl.textContent = fmtMWK(declaredMWK);
                const sMWKTaxes = document.getElementById('sumMWKTotalTaxes');
                if (sMWKTaxes) sMWKTaxes.textContent = fmtMWK(taxesMWK);
                const sMWKDuty = document.getElementById('sumMWKDuty');
                if (sMWKDuty) sMWKDuty.textContent = fmtMWK(dutyMWK);
                const sMWKVAT = document.getElementById('sumMWKVAT');
                if (sMWKVAT) sMWKVAT.textContent = fmtMWK(vatMWK);
                const totalCurEl = document.getElementById('totalDueCur');
                if (totalCurEl) totalCurEl.textContent = fmtCur(totalDueCur);
                const totalMWKEl = document.getElementById('totalDueMWK');
                if (totalMWKEl) totalMWKEl.textContent = fmtMWK(totalMWK);
                const exchEl = document.getElementById('exchangeInfo');
                if (exchEl) exchEl.textContent = `Exchange Rate: 1 ${codeLabel} = ${rate.toFixed(2)} MWK`;
            }

            // Function to check if all required fields are filled
            function checkCalculateButton() {
                const hsCalc = document.getElementById('calculator_hs');
                const curSelect = document.getElementById('currencySelect');
                const grossWeight = document.getElementById('gross_weight_field');
                const calculateBtn = document.getElementById('calculateTaxBtn');
                const hint = document.getElementById('calculateHint');

                const hasHS = hsCalc && hsCalc.value;
                const hasCurrency = curSelect && curSelect.value;
                const hasWeight = grossWeight && parseFloat(grossWeight.value) > 0;

                if (hasHS && hasCurrency && hasWeight) {
                    if (calculateBtn) {
                        calculateBtn.disabled = false;
                        calculateBtn.classList.remove('btn-secondary');
                        calculateBtn.classList.add('btn-success');
                    }
                    if (hint) hint.textContent = 'Click "Calculate Taxes" to see the breakdown';
                } else {
                    if (calculateBtn) {
                        calculateBtn.disabled = true;
                        calculateBtn.classList.remove('btn-success');
                        calculateBtn.classList.add('btn-secondary');
                    }
                    if (hint) hint.textContent = 'Please select HS Code, Currency, and enter Weight to calculate taxes';
                }
            }

            // Add event listeners to check button state
            const hsCalcLive = document.getElementById('calculator_hs');
            const gwField = document.getElementById('gross_weight_field');
            const currencySelect = document.getElementById('currencySelect');

            if (hsCalcLive) {
                hsCalcLive.addEventListener('change', checkCalculateButton);
            }
            if (gwField) {
                gwField.addEventListener('input', checkCalculateButton);
                gwField.addEventListener('change', checkCalculateButton);
            }
            if (currencySelect) {
                // Initialize select from hidden if select is empty, then set currency & rate
                const hiddenCurEl = document.getElementById('currency_hidden');
                if (!currencySelect.value && hiddenCurEl) {
                    currencySelect.value = hiddenCurEl.value || '';
                }
                // Perform initial sync to ensure rate/field are correct on load
                setCurrencyAndRate(currencySelect.value || (hiddenCurEl ? hiddenCurEl.value : defaultCurrency));
                currencySelect.addEventListener('change', () => {
                    setCurrencyAndRate(currencySelect.value);
                    checkCalculateButton();
                });
            }

            // Calculate Tax Button Click Handler
            const calculateBtn = document.getElementById('calculateTaxBtn');
            if (calculateBtn) {
                calculateBtn.addEventListener('click', async function() {
                    // Change button state
                    this.disabled = true;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Calculating...';

                    // Run calculation
                    await computeAuto();

                    // Show results card with animation
                    const resultsCard = document.getElementById('taxResultsCard');
                    if (resultsCard) {
                        resultsCard.style.display = 'block';
                        resultsCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }

                    // Reset button
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-check-circle me-2"></i>Recalculate Taxes';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-primary');
                });
            }

            // Check button state on page load
            checkCalculateButton();
        })();
    </script>
</body>

</html>