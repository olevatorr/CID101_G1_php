<?php

// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 獲取會員ID
    $u_id = isset($_GET['U_ID']) ? $_GET['U_ID'] : null;

    if (!$u_id) {
        throw new Exception('會員ID未提供');
    }

    // 查詢會員的訂單及其相關活動資訊（包括所有狀態的活動）
    $sql = "
        SELECT eo.*, e.*
        FROM EVENT_ORDER eo
        INNER JOIN EVENTS e ON eo.E_ID = e.E_ID
        WHERE eo.U_ID = :U_ID
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':U_ID' => $u_id]);
    $prodRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 計算符合條件的訂單數量（包括所有狀態的活動）
    $countSql = "
        SELECT COUNT(*) AS COUNT
        FROM EVENT_ORDER eo
        INNER JOIN EVENTS e ON eo.E_ID = e.E_ID
        WHERE eo.U_ID = :U_ID
    ";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute([':U_ID' => $u_id]);
    $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
    $event_orderCount = $countRow['COUNT'];

    // 準備成功的 JSON 響應數據
    $result = [
        "error" => false,
        "msg" => "",
        "data" => $prodRows,
        "EVENT_ORDERCOUNT" => $event_orderCount
    ];
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
} catch (Exception $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲其他異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

?>
