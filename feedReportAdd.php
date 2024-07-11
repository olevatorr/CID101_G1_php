<?php
// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

// 定義上傳目錄的相對路徑
// $uploadRelativeDir = '/cid101/g1/upload/img/events/';


try {
    // 開始事務
    $pdo->beginTransaction();

    // 準備 SQL 語句，將知識數據插入到數據庫中
    $sql = "INSERT INTO EVENT_ORDER (F_ID, U_ID, ER_Origin) 
                VALUES (:F_ID, :U_ID, :ER_Origin)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":F_ID", $_POST["F_ID"]); // 綁定 F_ID 參數
    $stmt->bindParam(":U_ID", $_POST["U_ID"]); // 綁定 U_ID 參數
    $stmt->bindParam(":ER_Origin", $_POST["ER_Origin"]); // 綁定 ER_Origin 參數
    $stmt->execute();
    
    $sql2 = "UPDATE FEEDBACK SET F_STATUS = 1
                WHERE F_ID = :F_ID";

    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindParam(":F_ID", $_POST["F_ID"]); // 綁定 F_ID 參數
    $stmt2->execute();

    // 提交事務
    $pdo->commit();

    echo json_encode(['error' => false, 'msg' => '新增資料成功', 'F_ID' => $F_ID]);

} catch (PDOException $e) {
    // 回滾事務
    $pdo->rollBack();
    echo json_encode(['error' => true, 'msg' => '數據庫錯誤: ' . $e->getMessage()]);
} catch (Exception $e) {
    // 回滾事務
    $pdo->rollBack();
    echo json_encode(['error' => true, 'msg' => $e->getMessage()]);
}
?>

