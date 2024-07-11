<?php
require_once("config.php"); // 引入資料庫配置文件

// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {

    if (!isset($pdo)) {
        throw new Exception('資料庫連接未設置');
    }

    // 獲取訂單ID
    $po_id = isset($_GET['PO_ID']) ? $_GET['PO_ID'] : null;

    if (!$po_id) {
        throw new Exception('訂單ID未提供');
    }

    // 開始一個事務
    $pdo->beginTransaction();

    // 刪除訂單明細
    $detailsSql = "
        DELETE FROM PRODUCT_ORDER_DETAILS
        WHERE PO_ID = :PO_ID
    ";
    $detailsStmt = $pdo->prepare($detailsSql);
    if (!$detailsStmt->execute(['PO_ID' => $po_id])) {
        throw new PDOException('刪除訂單明細失敗: ' . implode(' ', $detailsStmt->errorInfo()));
    }

    // 刪除訂單
    $orderSql = "
        DELETE FROM PRODUCT_ORDER
        WHERE PO_ID = :PO_ID
    ";
    $orderStmt = $pdo->prepare($orderSql);
    if (!$orderStmt->execute(['PO_ID' => $po_id])) {
        throw new PDOException('刪除訂單失敗: ' . implode(' ', $orderStmt->errorInfo()));
    }

    // 提交事務
    $pdo->commit();

    $result = ["error" => false, "msg" => "訂單刪除成功"]; // 準備成功的 JSON 響應數據
} catch (PDOException $e) {
    // 發生 PDO 異常時回滾事務
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
} catch (Exception $e) {
    // 發生其他異常時回滾事務
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲其他異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

?>
