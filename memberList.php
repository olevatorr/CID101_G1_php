<?php
header('Content-Type: application/json');

// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 獲取 PO_ID，支持單個 ID 或多個 ID
    $po_ids = isset($_GET['PO_ID']) ? $_GET['PO_ID'] : null;

    if (!$po_ids) {
        throw new Exception('PO_ID未提供');
    }

    // 如果 PO_ID 是以逗號分隔的字串，將其轉換為數組
    if (is_string($po_ids)) {
        $po_ids = explode(',', $po_ids);
    }

    // 檢查 PO_ID 是否為有效的數組
    if (!is_array($po_ids) || empty($po_ids)) {
        throw new Exception('PO_ID 格式無效');
    }

    // 建立 SQL 查詢語句，處理多個 PO_ID
    $placeholders = rtrim(str_repeat('?,', count($po_ids)), ',');
    $sql = "SELECT * FROM PRODUCT_ORDER_DETAILS WHERE PO_ID IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($po_ids);
    $prodRows = $stmt->fetchAll(PDO::FETCH_ASSOC); // 獲取所有查詢結果行，並以關聯數組的形式返回

    $result = ["error" => false, "msg" => "", "productOrder" => $prodRows]; // 準備成功的 JSON 響應數據
    http_response_code(200); // 設置 HTTP 狀態碼為 200 OK
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => 'Database error: ' . $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
    http_response_code(500); // 設置 HTTP 狀態碼為 500 Internal Server Error
} catch (Exception $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲其他異常，並準備錯誤的 JSON 響應數據
    http_response_code(400); // 設置 HTTP 狀態碼為 400 Bad Request
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出
?>
