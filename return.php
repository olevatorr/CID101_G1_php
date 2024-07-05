<?php
require_once __DIR__ . '/vendor/autoload.php';

use Ecpay\Sdk\Factories\Factory;
use Ecpay\Sdk\Exceptions\RtnException;

$factory = new Factory([
    'hashKey' => 'pwFHCqoQZGmho4w6',
    'hashIv' => 'EkRm7iFT261dpevs',
]);

$checkoutResponse = $factory->create('CheckoutResponse');

try {
    $checkoutResponse->validate($_POST);
    
    // 支付成功，更新訂單狀態等
    $merchantTradeNo = $_POST['MerchantTradeNo'];
    $paymentDate = $_POST['PaymentDate'];
    $amount = $_POST['Amount'];
    $paymentType = $_POST['PaymentType'];
    
    // 在這裡處理訂單更新邏輯
    echo "支付成功！訂單號：$merchantTradeNo, 支付日期：$paymentDate, 金額：$amount, 支付方式：$paymentType";
    
} catch (RtnException $e) {
    // 處理錯誤
    echo '(' . $e->getCode() . ')' . $e->getMessage() . PHP_EOL;
}