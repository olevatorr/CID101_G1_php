<?php

use Ecpay\Sdk\Factories\Factory;
use Ecpay\Sdk\Services\UrlService;

require __DIR__ . '/vendor/autoload.php';

// 接收表單提交的資料
$itemName = $_POST['itemName'];
$itemPrice = $_POST['itemPrice'];
$itemQuantity = $_POST['itemQuantity'];
$totalAmount = $itemPrice * $itemQuantity;

$factory = new Factory([
    'hashKey' => '5294y06JbISpM5x9',
    'hashIv' => 'v77hoKGq4kWxNNIS',
]);
$autoSubmitFormService = $factory->create('AutoSubmitFormWithCmvService');

$input = [
    'MerchantID' => '2000132',
    'MerchantTradeNo' => 'Test' . time(),
    'MerchantTradeDate' => date('Y/m/d H:i:s'),
    'PaymentType' => 'aio',
    'TotalAmount' => $totalAmount,
    'TradeDesc' => UrlService::ecpayUrlEncode('交易描述範例'),
    'ItemName' => "$itemName $itemPrice TWD x $itemQuantity",
    'ChoosePayment' => 'Credit',
    'EncryptType' => 1,

    // 請參考 example/Payment/GetCheckoutResponse.php 範例開發
    //'ReturnURL' => 'https://您的網域/ecpay_callback.php', 修改這個才能看到回饋
    'ReturnURL' => 'https://www.ecpay.com.tw/example/receive',
];
$action = 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';

echo $autoSubmitFormService->generate($input, $action);
