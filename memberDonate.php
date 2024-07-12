<?php

// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 引入數據庫連接配置文件
require_once("config.php");

try {
    // 獲取會員ID
    $u_id = isset($_GET['U_ID']) ? $_GET['U_ID'] : null;

    if (!$u_id) {
        throw new Exception('會員ID未提供');
    }

    // 使用 JOIN 來獲取 DONATE_ORDER 和對應的 USER 資料，並根據會員ID過濾
    $sql = "SELECT DO.*, U.U_NAME 
            FROM DONATE_ORDER DO
            LEFT JOIN USER U ON DO.U_ID = U.U_ID
            WHERE DO.U_ID = :U_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['U_ID' => $u_id]);
    $donateOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = ['success' => true, 'data' => $donateOrders]; // 準備成功的 JSON 響應數據
} catch (PDOException $e) {
    $result = ['success' => false, 'message' => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
} catch (Exception $e) {
    $result = ['success' => false, 'message' => $e->getMessage()]; // 捕獲其他異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出
?>
