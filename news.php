<?php

// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php"); // 引入資料庫配置文件

    $sql = "SELECT * FROM NEWS"; // 準備 SQL 查詢語句，從資料庫中選擇所有知識數據
    $news = $pdo->query($sql); // 執行 SQL 查詢，用pdo物件的query方法，使用sql這個
    $prodRows = $news->fetchAll(PDO::FETCH_ASSOC); // 獲取所有查詢結果行，並以關聯數組的形式返回
    
    $countSql = "SELECT COUNT(*) AS count FROM NEWS";
    $countResult = $pdo->query($countSql);
    $countRow = $countResult->fetch(PDO::FETCH_ASSOC);
    $newsCount = $countRow['count'];

    $result = ["error" => false, "msg" => "", "news" => $prodRows, "newsCount" => $newsCount]; // 準備成功的 JSON 響應數據
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

?>
