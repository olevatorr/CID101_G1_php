<?php
// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

try {
    // 從請求中讀取 JSON 輸入數據
    $data = json_decode(file_get_contents('php://input'), true);

    // 檢查是否存在必要的數據
    if (!isset($data["U_ID"]) || !isset($data["P_ID"])) {
        throw new Exception("缺少 U_ID 或 P_ID 參數");
    }

    // 開始事務
    $pdo->beginTransaction();

    // 定義 SQL 刪除語句，用於刪除 `PRODUCT_COLLECTION` 表中指定 `P_ID` 和 `U_ID` 的記錄
    $sql = "DELETE FROM PRODUCT_COLLECTION WHERE P_ID = :P_ID AND U_ID = :U_ID";

    // 使用 PDO 對象準備 SQL 語句
    $stmt = $pdo->prepare($sql);

    // 綁定參數
    $stmt->bindValue(":P_ID", $data["P_ID"], PDO::PARAM_INT);
    $stmt->bindValue(":U_ID", $data["U_ID"], PDO::PARAM_INT);

    // 執行 SQL 語句
    $stmt->execute();

    // 獲取受影響的行數
    $affectedCount = $stmt->rowCount();

    // 提交事務
    $pdo->commit();

    // 創建結果數組
    if ($affectedCount > 0) {
        $result = ["error" => false, "msg" => "成功取消收藏"];
    } else {
        $result = ["error" => false, "msg" => "未找到匹配的收藏記錄"];
    }

} catch (PDOException $e) {
    // 如果發生例外，回滾事務
    $pdo->rollBack();
    $result = ["error" => true, "msg" => "數據庫錯誤: " . $e->getMessage()];
} catch (Exception $e) {
    // 如果發生其他例外，回滾事務
    $pdo->rollBack();
    $result = ["error" => true, "msg" => $e->getMessage()];
}

// 將結果數組編碼為 JSON 格式並輸出
echo json_encode($result, JSON_NUMERIC_CHECK);
?>