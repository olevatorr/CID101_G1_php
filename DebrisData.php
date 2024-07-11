<?php
// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 設置內容類型為 JSON
header('Content-Type: application/json');

// 引入數據庫連接配置文件
require_once("config.php");

try {
    // 準備 SQL 查詢語句，按月份排序
    $sql = "SELECT * FROM DEBRIS_DATA ORDER BY DATE_FORMAT(DDL_DATE, '%Y-%m')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // 獲取所有查詢結果行，並以關聯數組的形式返回
    $prodRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 準備成功的 JSON 響應數據
    $result = ["error" => false, "msg" => "", "DEBRIS_DATA" => $prodRows];
} catch (PDOException $e) {
    // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
    $result = ["error" => true, "msg" => $e->getMessage()];
}

// 將 PHP 數組轉換為 JSON 格式並輸出
echo json_encode($result, JSON_NUMERIC_CHECK);
?>
