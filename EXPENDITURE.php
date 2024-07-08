<?php
header('Content-Type: application/json');

// 引入數據庫連接配置文件
require_once("config.php");

try {
    // 獲取 DONATE_ORDER 資料
    $sql = "SELECT * FROM EXPENDITURE_LIST";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $EXPENDITURE_LIST = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($EXPENDITURE_LIST);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>