<?php

// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 檢查是否有傳遞 NS_ID 參數，如果有則準備 SQL 查詢語句，帶入分類條件
    if (isset($_GET['NS_ID'])) {
        $NS_ID = $_GET['NS_ID'];
        $sql = "SELECT * FROM NEWS WHERE NS_ID = :NS_ID ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':NS_ID', $NS_ID, PDO::PARAM_INT);
        $stmt->execute();
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // 如果沒有 NS_ID 參數，則返回所有資料
        $sql = "SELECT * FROM NEWS";
        $stmt = $pdo->query($sql);
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 獲取新聞總數
    $countSql = "SELECT COUNT(*) AS count FROM NEWS";
    $countResult = $pdo->query($countSql);
    $countRow = $countResult->fetch(PDO::FETCH_ASSOC);
    $newsCount = $countRow['count'];

    $result = ["error" => false, "msg" => "", "news" => $news, "newsCount" => $newsCount]; // 準備成功的 JSON 響應數據
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

?>