<?php
// Set CORS headers
header("Access-Control-Allow-Origin: *"); // Replace * with your frontend domain in production
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use Ecpay\Sdk\Factories\Factory;
use Ecpay\Sdk\Services\UrlService;

require __DIR__ . '/vendor/autoload.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit();
}

// Receive JSON format POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate required fields
$required_fields = ['itemName', 'itemPrice', 'itemQuantity'];
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit();
    }
}

// Extract and validate data
$itemName = filter_var($data['itemName'], FILTER_SANITIZE_SPECIAL_CHARS);
$itemPrice = filter_var($data['itemPrice'], FILTER_VALIDATE_FLOAT);
$itemQuantity = filter_var($data['itemQuantity'], FILTER_VALIDATE_INT);

// Validate price and quantity
if ($itemPrice === false || $itemQuantity === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid price or quantity format']);
    exit();
}

// Calculate total amount
$totalAmount = $itemPrice * $itemQuantity;

try {
    $factory = new Factory([
        'hashKey' => 'pwFHCqoQZGmho4w6',
        'hashIv' => 'EkRm7iFT261dpevs',
    ]);
    $autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

    $input = [
        'MerchantID' => '3002607',
        'MerchantTradeNo' => 'Test' . time(),
        'MerchantTradeDate' => date('Y/m/d H:i:s'),
        'PaymentType' => 'aio',
        'TotalAmount' => $totalAmount,
        'TradeDesc' => UrlService::ecpayUrlEncode('Transaction Description Example'),
        'ItemName' => "$itemName $itemPrice TWD x $itemQuantity",
        'ChoosePayment' => 'Credit',
        'EncryptType' => 1,
        'ReturnURL' => 'https://tibamef2e.com/cid101/g1/api/return.php',
        'ClientBackURL' => 'https://tibamef2e.com/cid101/g1/front/',
    ];
    $action = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';

    $form = $autoSubmitFormService->generate($input, $action);
    
    // Instead of echoing the form directly, we'll send it as JSON
    echo json_encode(['form' => $form, 'action' => $action]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('ECPay Payment Error: ' . $e->getMessage());
    echo json_encode(['error' => 'An error occurred while processing the payment. Please try again later.']);
}
?>