<?php
header('Content-Type: application/json');

$uploadRelativeDir = '/cid101/g1/upload/img/product/';
$currentDir = dirname(__FILE__);
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

try {
    require_once("config.php");

    $pdo->beginTransaction();
    $sql = "UPDATE PRODUCT SET P_NAME = :P_NAME, P_SUBTITLE = :P_SUBTITLE,
    PS_ID = :PS_ID, P_MATERIAL = :P_MATERIAL, P_SIZE = :P_SIZE, P_COLOR = :P_COLOR,
    P_PRICE = :P_PRICE, P_MAIN_IMG = :P_MAIN_IMG, P_IMG1 = :P_IMG1, P_IMG2 = :P_IMG2, P_CONTENT = :P_CONTENT WHERE P_ID = :P_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":P_NAME", $_POST["P_NAME"]);
    $stmt->bindParam(":P_SUBTITLE", $_POST["P_SUBTITLE"]);
    $stmt->bindParam(":PS_ID", $_POST["PS_ID"]);
    $stmt->bindParam(":P_MATERIAL", $_POST["P_MATERIAL"]);
    $stmt->bindParam(":P_SIZE", $_POST["P_SIZE"]);
    $stmt->bindParam(":P_COLOR", $_POST["P_COLOR"]);
    $stmt->bindParam(":P_PRICE", $_POST["P_PRICE"]);
    $stmt->bindParam(":P_CONTENT", $_POST["P_CONTENT"]);
    $stmt->bindParam(":P_ID", $_POST["P_ID"]);

    // Initialize the image fields with their current values
    $currentValues = [
        "P_MAIN_IMG" => $_POST["P_MAIN_IMG"],
        "P_IMG1" => $_POST["P_IMG1"],
        "P_IMG2" => $_POST["P_IMG2"],
    ];

    $images = ['P_MAIN_IMG', 'P_IMG1', 'P_IMG2'];

    foreach ($images as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]["error"] === UPLOAD_ERR_OK) {
            $oldFileQuery = $pdo->prepare("SELECT $field FROM PRODUCT WHERE P_ID = :P_ID");
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
                $currentValues[$field] = $newFilename;
            } else {
                throw new Exception("$field 上傳失敗");
            }
        }
    }

    // Bind the values for the image fields
    $stmt->bindParam(":P_MAIN_IMG", $currentValues["P_MAIN_IMG"]);
    $stmt->bindParam(":P_IMG1", $currentValues["P_IMG1"]);
    $stmt->bindParam(":P_IMG2", $currentValues["P_IMG2"]);

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
