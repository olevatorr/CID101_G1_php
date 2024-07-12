<?php
// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/img/product/';

// 獲取當前腳本所在的目錄
$currentDir = dirname(__FILE__);

// 構建絕對路徑
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
    $inTransaction = true;

    // 準備 SQL 語句，將商品數據插入到數據庫中
    $sql = "INSERT INTO PRODUCT (P_NAME, P_SUBTITLE, PS_ID, P_MATERIAL, P_SIZE, P_COLOR, P_PRICE, P_MAIN_IMG, P_IMG1, P_IMG2, P_CONTENT)
            VALUES (:P_NAME, :P_SUBTITLE, :PS_ID, :P_MATERIAL, :P_SIZE, :P_COLOR, :P_PRICE, :P_MAIN_IMG, :P_IMG1, :P_IMG2, :P_CONTENT)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":P_NAME", $_POST["P_NAME"]);
    $stmt->bindParam(":P_SUBTITLE", $_POST["P_SUBTITLE"]);
    $stmt->bindParam(":PS_ID", $_POST["PS_ID"]);
    $stmt->bindParam(":P_MATERIAL", $_POST["P_MATERIAL"]);
    $stmt->bindParam(":P_SIZE", $_POST["P_SIZE"]);
    $stmt->bindParam(":P_COLOR", $_POST["P_COLOR"]);
    $stmt->bindParam(":P_PRICE", $_POST["P_PRICE"]);
    $stmt->bindParam(":P_MAIN_IMG", $_POST["P_MAIN_IMG"]);
    $stmt->bindParam(":P_IMG1", $_POST["P_IMG1"]);
    $stmt->bindParam(":P_IMG2", $_POST["P_IMG2"]);
    $stmt->bindParam(":P_CONTENT", $_POST["P_CONTENT"]);
    $stmt->execute();

    $P_ID = $pdo->lastInsertId();

    // 處理文件上傳（如果有的話）
    $imageFields = ['P_MAIN_IMG', 'P_IMG1', 'P_IMG2'];
    foreach ($imageFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $fileInfo = pathinfo($_FILES[$field]['name']);
            $ext = $fileInfo['extension'];
            $newFilename;
            if ($field === 'P_MAIN_IMG') {
                $newFilename = $P_ID . '-1.' . $ext;
            } elseif ($field === 'P_IMG1') {
                $newFilename = $P_ID . '-2.' . $ext;
            } else {
                $newFilename = $P_ID . '-3.' . $ext;
            }

            // 移動上傳的文件
            if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $newFilename)) {
                throw new Exception($field . ' 文件上傳失敗');
            }

            // 更新數據庫中的文件名
            $updateSql = "UPDATE PRODUCT SET $field = :$field WHERE P_ID = :P_ID";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindParam(":$field", $newFilename);
            $updateStmt->bindParam(':P_ID', $P_ID);
            $updateStmt->execute();
        }
    }

    // 提交事務
    $pdo->commit();
    $inTransaction = false;

    echo json_encode(['error' => false, 'msg' => '新增資料成功', 'P_ID' => $P_ID]);

} catch (PDOException $e) {
    // 回滾事務
    if (isset($inTransaction) && $inTransaction) {
        $pdo->rollBack();
    }
    echo json_encode(['error' => true, 'msg' => '數據庫錯誤: ' . $e->getMessage()]);
} catch (Exception $e) {
    // 回滾事務
    if (isset($inTransaction) && $inTransaction) {
        $pdo->rollBack();
    }
    echo json_encode(['error' => true, 'msg' => $e->getMessage()]);
}
?>
