<?php

// 刪除活動訂單的邏輯
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    try {
        require_once("config.php"); // 引入資料庫配置文件

        // 獲取要刪除的訂單ID和會員ID
        $eo_id = isset($_GET['EO_ID']) ? $_GET['EO_ID'] : null;
        $u_id = isset($_GET['U_ID']) ? $_GET['U_ID'] : null;

        if (!$eo_id || !$u_id) {
            throw new Exception('訂單ID或會員ID未提供');
        }

        // 確認訂單是否屬於指定會員
        $checkSql = "SELECT COUNT(*) AS count FROM EVENT_ORDER WHERE EO_ID = :eo_id AND U_ID = :u_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['eo_id' => $eo_id, 'u_id' => $u_id]);
        $checkRow = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($checkRow['count'] == 0) {
            throw new Exception('訂單ID不符合指定會員');
        }

        // 執行刪除操作
        $sql = "DELETE FROM EVENT_ORDER WHERE EO_ID = :eo_id AND U_ID = :u_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['eo_id' => $eo_id, 'u_id' => $u_id]);

        // 準備成功的 JSON 響應數據
        $result = [
            "error" => false,
            "msg" => "訂單已成功刪除"
        ];
    } catch (PDOException $e) {
        $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
    } catch (Exception $e) {
        $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲其他異常，並準備錯誤的 JSON 響應數據
    }

    echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出
    exit;
}

// 查詢會員的訂單及其相關活動資訊（包括所有狀態的活動）
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
        WHERE eo.U_ID = :u_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['u_id' => $u_id]);
    $prodRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 計算符合條件的訂單數量（包括所有狀態的活動）
    $countSql = "
        SELECT COUNT(*) AS count
        FROM EVENT_ORDER eo
        INNER JOIN EVENTS e ON eo.E_ID = e.E_ID
        WHERE eo.U_ID = :u_id
    ";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute(['u_id' => $u_id]);
    $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
    $event_orderCount = $countRow['count'];

    // 準備成功的 JSON 響應數據
    $result = [
        "error" => false,
        "msg" => "",
        "data" => $prodRows,
        "event_orderCount" => $event_orderCount
    ];
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
} catch (Exception $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲其他異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

?>
