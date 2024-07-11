<?php

try {
    require_once("config.php"); // 引入資料庫配置文件

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 獲取 POST 請求的數據
        $input = json_decode(file_get_contents('php://input'), true);

        // 從請求中獲取會員 ID 和商品 ID
        $userId = isset($input['U_ID']) ? intval($input['U_ID']) : 0;
        $productId = isset($input['P_ID']) ? intval($input['P_ID']) : 0;

        if ($userId > 0 && $productId > 0) {
            // 執行 SQL 語句刪除指定會員對應的收藏商品
            $sql = "DELETE FROM PRODUCT_COLLECTION WHERE U_ID = :userId AND P_ID = :productId";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
            $stmt->execute();

            // 返回成功的 JSON 響應
            $result = ["error" => false, "msg" => "取消收藏成功"];
        } else {
            // 返回錯誤的 JSON 響應，參數不合法
            $result = ["error" => true, "msg" => "無效的會員 ID 或商品 ID"];
        }
    } else {
        // 如果不是 POST 請求，返回錯誤的 JSON 響應
        $result = ["error" => true, "msg" => "請求方法不正確"];
    }

    echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

} catch (PDOException $e) {
    // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
    $result = ["error" => true, "msg" => $e->getMessage()];
    echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出
}
?>
