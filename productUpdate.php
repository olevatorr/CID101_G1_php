<?php
header('Content-Type: application/json');

// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/img/product/';

// 獲取當前腳本所在的目錄
$currentDir = dirname(__FILE__);

// 構建絕對路徑
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

try {
    require_once("config.php");

    // 開始事務
    $pdo->beginTransaction();

    // 準備更新語句
    $sql = "UPDATE product SET P_NAME = :P_NAME, P_PRICE = :P_PRICE, P_SUBTITLE = :P_SUBTITLE, P_CONTENT = :P_CONTENT, P_MATERIAL = :P_MATERIAL, P_SIZE = :P_SIZE, P_COLOR = :P_COLOR, P_STATUS = :P_STATUS, P_MAIN_IMG = :P_MAIN_IMG, P_IMG1 = :P_IMG1, P_IMG2 = :P_IMG2 WHERE P_ID = :P_ID";
    $stmt = $pdo->prepare($sql);

    // 綁定基本參數
    $stmt->bindValue(":P_NAME", $_POST["P_NAME"]);
    $stmt->bindValue(":P_PRICE", $_POST["P_PRICE"]);
    $stmt->bindValue(":P_SUBTITLE", $_POST["P_SUBTITLE"]);
    $stmt->bindValue(":P_CONTENT", $_POST["P_CONTENT"]);
    $stmt->bindValue(":P_MATERIAL", $_POST["P_MATERIAL"]);
    $stmt->bindValue(":P_SIZE", $_POST["P_SIZE"]);
    $stmt->bindValue(":P_COLOR", $_POST["P_COLOR"]);
    $stmt->bindValue(":P_STATUS", $_POST["P_STATUS"]);
    $stmt->bindValue(":P_ID", $_POST["P_ID"]);

    // 處理文件上傳
    $images = [
        'P_MAIN_IMG' => 'newMainImage',
        'P_IMG1' => 'newImg1',
        'P_IMG2' => 'newImg2'
    ];

    foreach ($images as $dbField => $fileField) {
        if (isset($_FILES[$fileField]) && $_FILES[$fileField]["error"] === UPLOAD_ERR_OK) {
            // 獲取舊文件名
            $oldFileQuery = $pdo->prepare("SELECT $dbField FROM product WHERE P_ID = :P_ID");
            $oldFileQuery->execute([':P_ID' => $_POST["P_ID"]]);
            $oldFileName = $oldFileQuery->fetchColumn();

            // 刪除舊文件
            if ($oldFileName && file_exists($uploadDir . $oldFileName)) {
                unlink($uploadDir . $oldFileName);
            }

            // 處理新文件
            $fileInfo = pathinfo($_FILES[$fileField]['name']);
            $extension = $fileInfo['extension'];
            $newFileName = $_POST["P_ID"] . "_" . strtolower($dbField) . "." . $extension;

            // 移動新文件
            if (move_uploaded_file($_FILES[$fileField]['tmp_name'], $uploadDir . $newFileName)) {
                $stmt->bindValue(":$dbField", $newFileName);
            } else {
                throw new Exception("$dbField 上傳失敗");
            }
        } else {
            // 如果沒有新文件，保留原來的文件名
            $stmt->bindValue(":$dbField", $_POST[$dbField]);
        }
    }

    // 執行更新
    $stmt->execute();

    // 提交事務
    $pdo->commit();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (Exception $e) {
    // 回滾事務
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>
