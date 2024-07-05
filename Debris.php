<?php
header('Content-Type: application/json');

// 引入數據庫連接配置文件
require_once("config.php");

try {
    // 獲取 DEBRIS_DATA_LIST 資料
    $sql = "SELECT * FROM DEBRIS_DATA_LIST";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $dataList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 獲取 DEBRIS_DATA 資料
    $sql = "SELECT * FROM DEBRIS_DATA";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 合併數據
    foreach ($dataList as &$listItem) {
        $listItem['data'] = array_filter($data, function ($dataItem) use ($listItem) {
            return $dataItem['DDL_ID'] == $listItem['DDL_ID'];
        });
    }

    echo json_encode($dataList);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
