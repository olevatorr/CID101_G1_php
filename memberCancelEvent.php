<?php

require_once("config.php"); // 引入資料庫配置文件

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {

    // 獲取訂單ID
    $eo_id = isset($_GET['EO_ID']) ? $_GET['EO_ID'] : null;
    $u_id = isset($_GET['U_ID']) ? $_GET['U_ID'] : null;

    if (!$eo_id) {
        throw new Exception('訂單ID未提供');
    }

    // 刪除指定的訂單
    $sql = "DELETE FROM EVENT_ORDER WHERE EO_ID = :EO_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':EO_ID' => $eo_id]);

    // 確認刪除是否成功
    if ($stmt->rowCount() > 0) {
        // 刪除成功
        $result = [
            "error" => false,
            "msg" => "訂單已成功刪除"
        ];
    } else {
        // 如果沒有影響行數，則表示沒有找到對應的訂單
        $result = [
            "error" => true,
            "msg" => "找不到指定的訂單"
        ];
    }
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
} catch (Exception $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲其他異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出


?>
