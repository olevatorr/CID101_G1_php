<?php
header('Content-Type: application/json');

// 引入數據庫連接配置文件
require_once("config.php");

try {
    // 使用 JOIN 來獲取 DONATE_ORDER 和對應的 USER 資料
    $sql = "SELECT DO.*, U.U_NAME 
            FROM DONATE_ORDER DO
            LEFT JOIN USER U ON DO.U_ID = U.U_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $donateOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $donateOrders]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}