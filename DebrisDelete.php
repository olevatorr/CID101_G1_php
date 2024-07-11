<?php
header('Content-Type: application/json');

try {
    // 引入資料庫連接配置文件
    require_once("config.php");

    // 開始一個事務
    $pdo->beginTransaction();

    // 定義 SQL 刪除語句，用於刪除 `debris_data` 表中指定 `DDL_ID` 的記錄
    $sql1 = "DELETE FROM DEBRIS_DATA WHERE DDL_ID = ?";
    $debris_data = $pdo->prepare($sql1);
    $debris_data->bindValue(1, $_GET["DDL_ID"]);
    $debris_data->execute();
    $affectedCount1 = $debris_data->rowCount();

    // 定義 SQL 刪除語句，用於刪除 `debris_data_list` 表中指定 `DDL_ID` 的記錄
    $sql2 = "DELETE FROM DEBRIS_DATA_LIST WHERE DDL_ID = ?";
    $debris_data_list = $pdo->prepare($sql2);
    $debris_data_list->bindValue(1, $_GET["DDL_ID"]);
    $debris_data_list->execute();
    $affectedCount2 = $debris_data_list->rowCount();

    // 提交
    $pdo->commit();

    // 創建結果數組，包含操作是否成功的標誌和影響的行數
    $result = ["error" => false, "msg" => "成功的影響{$affectedCount1}筆 debris_data 和 {$affectedCount2}筆 debris_data_list"];
} catch (PDOException $e) {
    // 如果發生例外
    $pdo->rollBack();
    // 創建結果數組，包含錯誤標誌和錯誤信息
    $result = ["error" => true, "msg" => $e->getMessage()];
}

// 將結果數組編碼為 JSON 格式並輸出
echo json_encode($result, JSON_NUMERIC_CHECK);
?>


