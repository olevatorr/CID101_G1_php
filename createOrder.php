<?php
require_once("config.php");

// 允許所有來源訪問這個API，設置CORS頭
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// 如果是 OPTIONS 請求，直接返回
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 獲取POST數據
$data = json_decode(file_get_contents("php://input"));

try {
    $pdo->beginTransaction();

    // 插入主訂單
    $sql = "INSERT INTO PRODUCT_ORDER (PO_NAME, PO_ADDR, PO_PHONE, PO_DATE, PO_AMOUNT, PM_ID, PO_TRANS, S_STATUS) 
            VALUES (:PO_NAME, :PO_ADDR, :PO_PHONE, NOW(), :PO_AMOUNT, :PM_ID, :PO_TRANS, :S_STATUS)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindParam(":PO_NAME", $data->PO_NAME);
    $stmt->bindParam(":PO_ADDR", $data->PO_ADDR);
    $stmt->bindParam(":PO_PHONE", $data->PO_PHONE);
    $stmt->bindParam(":PO_AMOUNT", $data->PO_AMOUNT);
    $stmt->bindParam(":PM_ID", $data->PM_ID);
    $stmt->bindParam(":PO_TRANS", $data->PO_TRANS);
    $stmt->bindParam(":S_STATUS", $data->S_STATUS);
    
    $stmt->execute();
    
    $orderId = $pdo->lastInsertId();

    // 插入訂單詳情
    $sql = "INSERT INTO PRODUCT_ORDER_DETAILS (PO_ID, P_NAME, P_PRICE, PO_QTY) VALUES (:PO_ID, :P_NAME, :P_PRICE, :PO_QTY)";
    $stmt = $pdo->prepare($sql);
    
    foreach ($data->items as $item) {
        $stmt->bindParam(":PO_ID", $orderId);
        $stmt->bindParam(":P_NAME", $item->P_NAME);
        $stmt->bindParam(":P_PRICE", $item->P_PRICE);
        $stmt->bindParam(":PO_QTY", $item->PO_QTY);
        $stmt->execute();
    }

    $pdo->commit();
    echo json_encode(["success" => true, "message" => "訂單創建成功", "orderId" => $orderId]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(["success" => false, "message" => "訂單創建失敗: " . $e->getMessage()]);
}
?>