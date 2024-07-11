<?php
// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

// 添加日誌函數
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'collect_error.log');
}

try {
    // 開始事務
    $pdo->beginTransaction();

    // 從請求中讀取 JSON 輸入數據
    $input = file_get_contents('php://input');
    logError("Received input: " . $input);
    $data = json_decode($input, true);

    // 檢查是否存在必要的數據
    if (isset($data["U_ID"]) && isset($data["P_ID"])) {
        logError("U_ID: " . $data["U_ID"] . ", P_ID: " . $data["P_ID"]);

        // 檢查是否已存在相同的收藏記錄
        $checkSql = "SELECT COUNT(*) FROM PRODUCT_COLLECTION WHERE U_ID = :U_ID AND P_ID = :P_ID";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindValue(":U_ID", $data["U_ID"], PDO::PARAM_INT);
        $checkStmt->bindValue(":P_ID", $data["P_ID"], PDO::PARAM_INT);
        $checkStmt->execute();
        
        $count = $checkStmt->fetchColumn();
        logError("Existing collection count: " . $count);

        if ($count > 0) {
            throw new Exception("該商品已被收藏");
        }

        // 準備 SQL 語句，將收藏數據插入到數據庫中
        $sql = "INSERT INTO PRODUCT_COLLECTION (U_ID, P_ID) VALUES (:U_ID, :P_ID)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":U_ID", $data["U_ID"], PDO::PARAM_INT);
        $stmt->bindValue(":P_ID", $data["P_ID"], PDO::PARAM_INT);
        $result = $stmt->execute();
        logError("Insert result: " . ($result ? "success" : "fail"));

        // 提交事務
        $pdo->commit();

        echo json_encode([
            "error" => false, 
            "msg" => "收藏成功"
        ]);
    } else {
        throw new Exception("缺少必要的數據");
    }
} catch (PDOException $e) {
    // 回滾事務
    $pdo->rollBack();
    logError("PDO Exception: " . $e->getMessage());
    echo json_encode(['error' => true, 'msg' => '數據庫錯誤: ' . $e->getMessage()]);
} catch (Exception $e) {
    // 回滾事務
    $pdo->rollBack();
    logError("Exception: " . $e->getMessage());
    echo json_encode(['error' => true, 'msg' => $e->getMessage()]);
}
?>