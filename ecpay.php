<?php
header('Access-Control-Allow-Headers: Content-Type');

require_once("config.php");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

use Ecpay\Sdk\Factories\Factory;
use Ecpay\Sdk\Services\UrlService;

require __DIR__ . '/vendor/autoload.php';

// 檢查是否為 POST 請求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '不允許的方法']);
    exit();
}

// 接收 JSON 格式的 POST 數據
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// 驗證必要欄位
$required_fields = ['itemName', 'itemPrice', 'itemQuantity'];
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "缺少必要欄位：$field"]);
        exit();
    }
}

// 提取並驗證數據
$itemName = isset($data['itemName']) ? filter_var($data['itemName'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
$itemPrice = isset($data['itemPrice']) ? filter_var($data['itemPrice'], FILTER_VALIDATE_FLOAT) : false;
$itemQuantity = isset($data['itemQuantity']) ? filter_var($data['itemQuantity'], FILTER_VALIDATE_INT) : false;

// 驗證價格和數量是否有效
if ($itemPrice === false || $itemQuantity === false) {
    http_response_code(400);
    echo json_encode(['error' => '價格或數量格式不正確']);
    exit();
}

// 計算總金額
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
        'TradeDesc' => UrlService::ecpayUrlEncode('交易描述範例'),
        'ItemName' => "$itemName $itemPrice TWD x $itemQuantity",
        'ChoosePayment' => 'Credit',
        'EncryptType' => 1,
        'ReturnURL' => 'https://tibamef2e.com/cid101/g1/api/return.php',
        'ClientBackURL' => 'https://tibamef2e.com/cid101/g1/front/',
    ];
    $action = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';

    $form = $autoSubmitFormService->generate($input, $action);
    
    echo json_encode(['form' => $form]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('綠界支付錯誤: ' . $e->getMessage());
    echo json_encode(['error' => '處理付款時發生錯誤，請稍後再試。']);
}
?>
