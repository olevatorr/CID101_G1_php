<?php
header('Content-Type: application/json');

// 確保 P_ID 和 U_ID 參數存在
if (!isset($_GET["P_ID"]) || !isset($_GET["U_ID"])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => true, "msg" => "缺少 P_ID 或 U_ID 參數"]);
    exit;
}

try {
    // 引入資料庫連接配置文件
    require_once("config.php");
    
    // 開始事務
    $pdo->beginTransaction();

    // 定義 SQL 刪除語句，用於刪除 `PRODUCT_COLLECTION` 表中指定 `P_ID` 和 `U_ID` 的記錄
    $sql = "DELETE FROM PRODUCT_COLLECTION WHERE P_ID = :P_ID AND U_ID = :U_ID";

    // 使用 PDO 對象準備 SQL 語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數
    $stmt->bindValue(":P_ID", $_GET["P_ID"], PDO::PARAM_INT);
    $stmt->bindValue(":U_ID", $_GET["U_ID"], PDO::PARAM_INT);

    // 執行 SQL 語句
    $stmt->execute();

    // 獲取受影響的行數
    $affectedCount = $stmt->rowCount();

    // 提交事務
    $pdo->commit();

    // 創建結果數組
    if ($affectedCount > 0) {
        $result = ["error" => false, "msg" => "成功取消收藏 {$affectedCount} 筆記錄"];
    } else {
        $result = ["error" => false, "msg" => "未找到匹配的收藏記錄"];
    }

} catch (PDOException $e) {
    // 如果發生例外，回滾事務
    $pdo->rollBack();
    
    // 創建結果數組，包含錯誤標誌和錯誤信息
    $result = ["error" => true, "msg" => "數據庫錯誤: " . $e->getMessage()];
}

// 將結果數組編碼為 JSON 格式並輸出
echo json_encode($result, JSON_NUMERIC_CHECK);
?>