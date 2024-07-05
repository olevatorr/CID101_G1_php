<?php

// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/img/news/';

// 獲取當前腳本所在的目錄
$currentDir = dirname(__FILE__);

// 構建絕對路徑
// 自己包的結構到要接上的地方
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

// 確保上傳目錄存在
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        die(json_encode(['error' => true, 'msg' => '無法創建上傳目錄']));
    }
}

// 檢查目錄是否可寫
if (!is_writable($uploadDir)) {
    die(json_encode(['error' => true, 'msg' => '目錄不可寫']));
}

try {
    // 開始事務
    $pdo->beginTransaction();

    //一般的資料
    // 準備 SQL 語句，將知識數據插入到數據庫中
    $sql = "INSERT INTO NEWS (N_TITLE, N_CONTENT, NS_ID) 
            VALUES (:N_TITLE, :N_CONTENT, :NS_ID)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':N_TITLE', $_POST['N_TITLE']);
    $stmt->bindParam(':N_CONTENT', $_POST['N_CONTENT']);
    $stmt->bindParam(':NS_ID', $_POST['NS_ID']);
    $stmt->execute();

    $N_ID = $pdo->lastInsertId();

    // 圖片的處理
    // 處理文件上傳
    if (isset($_FILES['N_IMG']) && $_FILES['N_IMG']['error'] === UPLOAD_ERR_OK) {
        $fileInfo = pathinfo($_FILES['N_IMG']['name']);
        $ext = $fileInfo['extension'];
        //根據某個ID和副檔名來生成唯一的檔案名稱，以防止檔案名稱衝突
        // $ext，存儲了檔案的副檔名
        $newFilename = $N_ID . '.' . $ext;

        // 移動上傳的文件
        if (!move_uploaded_file($_FILES['N_IMG']['tmp_name'], $uploadDir . $newFilename)) {
            throw new Exception('文件上傳失敗');
        }

        // 更新數據庫中的文件名
        $updateSql = "UPDATE NEWS SET N_IMG = :N_IMG WHERE N_ID = :N_ID";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':N_IMG', $newFilename);
        $updateStmt->bindParam(':N_ID', $N_ID);
        $updateStmt->execute();
    }

    // 提交事務
    $pdo->commit();

    echo json_encode(['error' => false, 'msg' => '新增資料成功', 'N_ID' => $N_ID]);

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