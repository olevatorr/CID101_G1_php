<?php

// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 假設會員的 U_ID 是通過 GET 請求傳遞的
    $userId = isset($_GET['U_ID']) ? intval($_GET['U_ID']) : 0;

    // 準備 SQL 查詢語句，聯接 product 和 product_collection 表格
    $sql = "
        SELECT p.*, 
        pc.U_ID 
        FROM PRODUCT p
        JOIN PRODUCT_COLLECTION pc ON p.P_ID = pc.P_ID 
        AND pc.U_ID = :U_ID
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':U_ID', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $prodRows = $stmt->fetchAll(PDO::FETCH_ASSOC); // 獲取所有查詢結果行，並以關聯數組的形式返回
    
    // 計算商品數量
    $countSql = "SELECT COUNT(*) AS COUNT FROM PRODUCT";
    $countResult = $pdo->query($countSql);
    $countRow = $countResult->fetch(PDO::FETCH_ASSOC);
    $productCount = $countRow['COUNT'];

    $result = ["error" => false, "msg" => "", "data" => $prodRows, "productCount" => $productCount]; // 準備成功的 JSON 響應數據
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

?>
