<?php

require_once("config.php"); // 引入資料庫配置文件

// 獲取前端傳來的數據
$data = json_decode(file_get_contents('php://input'), true);
// 檢查數據是否存在
if (isset($data['E_ID']) && isset($data['E_SIGN_UP']) && isset($data['EO_STATUS'])) {
    $E_ID = $data['E_ID'];
    $E_SIGN_UP = $data['E_SIGN_UP'];
    $EO_STATUS = $data['EO_STATUS'];
    // 開始交易
    $pdo->beginTransaction();

    try {
        // 更新報名人數
        $sqlUpdateSignUp = "UPDATE EVENTS SET E_SIGN_UP = :E_SIGN_UP WHERE E_ID = :E_ID";
        $stmtUpdateSignUp = $pdo->prepare($sqlUpdateSignUp);
        $stmtUpdateSignUp->bindParam(':E_SIGN_UP', $E_SIGN_UP, PDO::PARAM_INT);
        $stmtUpdateSignUp->bindParam(':E_ID', $E_ID, PDO::PARAM_INT);
        $stmtUpdateSignUp->execute();

        // 更新活動狀態
        $sqlUpdateStatus = "UPDATE EVENT_ORDER SET EO_STATUS = :EO_STATUS WHERE E_ID = :E_ID";
        $stmtUpdateStatus = $pdo->prepare($sqlUpdateStatus);
        $stmtUpdateStatus->bindParam(':EO_STATUS', $EO_STATUS, PDO::PARAM_INT);
        $stmtUpdateStatus->bindParam(':E_ID', $E_ID, PDO::PARAM_INT);
        $stmtUpdateStatus->execute();

        // 提交交易
        $pdo->commit();
        echo json_encode(["error" => false, "msg" => "更新成功"]);
    } catch (PDOException $e) {
        // 回滾交易並捕獲異常
        $pdo->rollBack();
        echo json_encode(["error" => true, "msg" => $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => true, "msg" => "無效的數據"]);
}


?>
