<?php
header('Content-Type: application/json');

$uploadRelativeDir = '/cid101/g1/upload/img/product/';
$currentDir = dirname(__FILE__);
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

try {
    require_once("config.php");

    $pdo->beginTransaction();

    $sql = "UPDATE product SET P_NAME = :P_NAME, P_PRICE = :P_PRICE, P_SUBTITLE = :P_SUBTITLE, P_CONTENT = :P_CONTENT, P_MATERIAL = :P_MATERIAL, P_SIZE = :P_SIZE, P_COLOR = :P_COLOR, P_STATUS = :P_STATUS, P_MAIN_IMG = :P_MAIN_IMG, P_IMG1 = :P_IMG1, P_IMG2 = :P_IMG2 WHERE P_ID = :P_ID";
    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(":P_NAME", $_POST["P_NAME"]);
    $stmt->bindValue(":P_PRICE", $_POST["P_PRICE"]);
    $stmt->bindValue(":P_SUBTITLE", $_POST["P_SUBTITLE"]);
    $stmt->bindValue(":P_CONTENT", $_POST["P_CONTENT"]);
    $stmt->bindValue(":P_MATERIAL", $_POST["P_MATERIAL"]);
    $stmt->bindValue(":P_SIZE", $_POST["P_SIZE"]);
    $stmt->bindValue(":P_COLOR", $_POST["P_COLOR"]);
    $stmt->bindValue(":P_STATUS", $_POST["P_STATUS"]);
    $stmt->bindValue(":P_ID", $_POST["P_ID"]);

    $images = ['P_MAIN_IMG', 'P_IMG1', 'P_IMG2'];

    foreach ($images as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]["error"] === UPLOAD_ERR_OK) {
            $oldFileQuery = $pdo->prepare("SELECT $field FROM product WHERE P_ID = :P_ID");
            $oldFileQuery->execute([':P_ID' => $_POST["P_ID"]]);
            $oldFileName = $oldFileQuery->fetchColumn();

            if ($oldFileName && file_exists($uploadDir . $oldFileName)) {
                unlink($uploadDir . $oldFileName);
            }

            $fileInfo = pathinfo($_FILES[$field]['name']);
            $extension = $fileInfo['extension'];
            
            // 根據字段選擇適當的文件名
            if ($field === 'P_MAIN_IMG') {
                $newFilename = $_POST["P_ID"] . '-1.' . $extension;
            } elseif ($field === 'P_IMG1') {
                $newFilename = $_POST["P_ID"] . '-2.' . $extension;
            } else {
                $newFilename = $_POST["P_ID"] . '-3.' . $extension;
            }

            if (move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $newFilename)) {
                $stmt->bindValue(":$field", $newFilename);
            } else {
                throw new Exception("$field 上傳失敗");
            }
        } else {
            $stmt->bindValue(":$field", $_POST[$field]);
        }
    }

    $stmt->execute();
    $pdo->commit();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>