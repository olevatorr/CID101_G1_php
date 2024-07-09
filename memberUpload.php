<?php
header('Content-Type: application/json');

$uploadRelativeDir = '/cid101/g1/upload/img/member/';
$currentDir = dirname(__FILE__);
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

try {
    require_once("config.php");

    // 開始事務
    $pdo->beginTransaction();

    // 準備更新語句
    $sql = "UPDATE USER SET U_AVATAR = :imageSrc WHERE U_ID = :U_ID";
    $stmt = $pdo->prepare($sql);

    $newFileName = null; // 初始化變量以存儲新文件名

    // 處理文件上傳
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // 獲取舊文件名
        $oldFileQuery = $pdo->prepare("SELECT U_AVATAR FROM USER WHERE U_ID = :U_ID");
        $oldFileQuery->execute([':U_ID' => $_POST["U_ID"]]);
        $oldFileName = $oldFileQuery->fetchColumn();

        // 刪除舊文件
        if ($oldFileName && file_exists($uploadDir . $oldFileName)) {
            unlink($uploadDir . $oldFileName);
        }

        // 處理新文件
        $fileInfo = pathinfo($_FILES['file']['name']);
        $extension = $fileInfo['extension'];
        $newFileName = $_POST["U_ID"] . '.' . $extension;

        // 移動新文件
        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $newFileName)) {
            $stmt->bindValue(":imageSrc", $newFileName);
        } else {
            throw new Exception("文件上傳失敗");
        }
    } else {
        // 如果沒有新文件，保留原來的文件名
        $oldFileQuery = $pdo->prepare("SELECT U_AVATAR FROM USER WHERE U_ID = :U_ID");
        $oldFileQuery->execute([':U_ID' => $_POST["U_ID"]]);
        $newFileName = $oldFileQuery->fetchColumn();
        $stmt->bindValue(":imageSrc", $newFileName);
    }

    $stmt->bindValue(":U_ID", $_POST["U_ID"]);

    // 執行更新
    $stmt->execute();

    // 提交事務
    $pdo->commit();

    echo json_encode(["error" => false, "msg" => "修改成功", "fileName" => $newFileName]);
} catch (Exception $e) {
    // 回滾事務
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>
