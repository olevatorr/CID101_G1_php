<?php
// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件


try {
    require_once("config.php");
    // 開始事務
    $pdo->beginTransaction();
    $data = json_decode(file_get_contents('php://input'), true);
    // 準備 SQL 語句，將數據插入到數據庫中
    $sql = "INSERT INTO EVENT_REPORTS (F_ID, U_ID,UR_ID, ER_ORIGIN) 
                VALUES (:F_ID, :U_ID, :UR_ID,:ER_ORIGIN)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":F_ID", $data["F_ID"]); // 綁定 F_ID 參數
    $stmt->bindValue(":U_ID", $data["U_ID"]); // 綁定 U_ID 參數
    $stmt->bindValue(":UR_ID", $data["UR_ID"]); // 綁定 U_ID 參數
    $stmt->bindValue(":ER_ORIGIN", $data["ER_ORIGIN"]); // 綁定 ER_Origin 參數
    $stmt->execute();
    
    $sql2 = "UPDATE FEEDBACK SET F_STATUS = 1
                WHERE F_ID = :F_ID";

    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindValue(":F_ID", $data["F_ID"]); // 綁定 F_ID 參數
    $stmt2->execute();

    // 提交事務
    $pdo->commit();

    echo json_encode(['error' => false, 'msg' => '新增資料成功', 'F_ID' => $_POST["F_ID"]]);

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

