<?php
// 如果是 OPTIONS 請求，返回 HTTP 狀態碼 204 並退出腳本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json');

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 檢查是否提供了必要的參數
    if (!isset($_GET['U_ID']) || !isset($_GET['P_ID'])) {
        throw new Exception("缺少必要的參數");
    }

    $U_ID = intval($_GET['U_ID']);
    $P_ID = intval($_GET['P_ID']);

    // 準備 SQL 查詢語句，檢查特定用戶是否收藏了特定商品
    $sql = "SELECT COUNT(*) AS count FROM PRODUCT_COLLECTION WHERE U_ID = :U_ID AND P_ID = :P_ID";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':U_ID', $U_ID, PDO::PARAM_INT);
    $stmt->bindParam(':P_ID', $P_ID, PDO::PARAM_INT);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $isCollected = ($row['count'] > 0);

    $result = [
        "error" => false,
        "msg" => "",
        "isCollected" => $isCollected
    ];
} catch (Exception $e) {
    $result = ["error" => true, "msg" => $e->getMessage()];
}

echo json_encode($result);
?>