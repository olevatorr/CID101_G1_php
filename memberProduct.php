<?php

// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 定義付款方式對應表
    $paymentMethods = [
        0 => '超商繳費',
        1 => '銀行轉帳',
        2 => '信用卡',
        3 => 'Line Pay'
    ];

    // 獲取會員ID
    $u_id = isset($_GET['U_ID']) ? $_GET['U_ID'] : null;

    if (!$u_id) {
        throw new Exception('會員ID未提供');
    }

    // 查詢訂單
    $orderSql = "
        SELECT PO_ID, U_ID, PO_NAME, PO_PHONE, PO_AMOUNT, PO_ADDR, 
        PM_ID, PO_DATE, S_STATUS, PO_TRANS
        FROM PRODUCT_ORDER
        WHERE U_ID = :U_ID
    ";
    $orderStmt = $pdo->prepare($orderSql);
    $orderStmt->execute(['U_ID' => $u_id]);
    $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

    // 查詢訂單明細
    $detailsSql = "
        SELECT PO_ID, P_ID, P_NAME, P_PRICE, PO_QTY
        FROM PRODUCT_ORDER_DETAILS
        WHERE PO_ID IN (SELECT PO_ID FROM PRODUCT_ORDER WHERE U_ID = :U_ID)
    ";
    $detailsStmt = $pdo->prepare($detailsSql);
    $detailsStmt->execute(['U_ID' => $u_id]);
    $details = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);

    // 將訂單和訂單明細組合在一起並轉換 PM_ID
    $ordersWithDetails = [];
    foreach ($orders as $order) {
        // 將 PM_ID 轉換為對應的付款方式文字
        $order['PM_ID'] = $paymentMethods[$order['PM_ID']];

        $order['details'] = array_filter($details, function ($detail) use ($order) {
            return $detail['PO_ID'] === $order['PO_ID'];
        });
        $ordersWithDetails[] = $order;
    }

    $result = ["error" => false, "msg" => "", "data" => $ordersWithDetails]; // 準備成功的 JSON 響應數據
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
} catch (Exception $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲其他異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

?>
