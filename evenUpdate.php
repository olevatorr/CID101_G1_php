<?php
header('Content-Type: application/json');
// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/img/events/';

// 獲取當前腳本所在的目錄
$currentDir = dirname(__FILE__);

// 構建絕對路徑
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

try {
    require_once("config.php");

    // 開始事務
    $pdo->beginTransaction();

    // 準備更新語句
    $sql = "UPDATE EVENTS SET E_TITLE = :E_TITLE, E_ADDRESS = :E_ADDRESS, E_AREA = :E_AREA, E_DATE = :E_DATE, E_START = :E_START ,E_DEADLINE = :E_DEADLINE, E_SIGN_UP = :E_SIGN_UP ,E_CONTENT = :E_CONTENT ,E_STATUS = :E_STATUS ,E_MAX_ATTEND = :E_MAX_ATTEND , E_ID = :E_ID WHERE E_ID = :E_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":E_TITLE", $_POST["E_TITLE"]); // 綁定 E_TITLE 參數
    $stmt->bindValue(":E_ADDRESS", $_POST["E_ADDRESS"]); // 綁定 E_ADDRESS 參數
    $stmt->bindValue(":E_AREA", $_POST["E_AREA"]); // 綁定 E_AREA 參數
    $stmt->bindValue(":E_DATE", $_POST["E_DATE"]); // 綁定 E_DATE 參數
    $stmt->bindValue(":E_START", $_POST["E_START"]); // 綁定 E_START 參數
    $stmt->bindValue(":E_DEADLINE", $_POST["E_DEADLINE"]); // 綁定 E_DEADLINE 參數
    $stmt->bindValue(":E_SIGN_UP", $_POST["E_SIGN_UP"]); // 綁定 E_SIGN_UP 參數
    $stmt->bindValue(":E_CONTENT", $_POST["E_CONTENT"]); // 綁定 E_CONTENT 參數
    $stmt->bindValue(":E_STATUS", $_POST["E_STATUS"]); // 綁定 E_STATUS 參數
    $stmt->bindValue(":E_MAX_ATTEND", $_POST["E_MAX_ATTEND"]); // 綁定 E_STATUS 參數
    $stmt->bindValue(":E_ID", $_POST["E_ID"]);

    // 處理文件上傳
    if (isset($_FILES['E_IMG']) && $_FILES['E_IMG']['error'] === UPLOAD_ERR_OK) {
        // 獲取舊文件名
        $oldFileQuery = $pdo->prepare("SELECT E_IMG FROM EVENTS WHERE E_ID = :E_ID");
        $oldFileQuery->execute([':E_ID' => $_POST["E_ID"]]);
        $oldFileName = $oldFileQuery->fetchColumn();

        // 刪除舊文件
        if ($oldFileName && file_exists($uploadDir . $oldFileName)) {
            unlink($uploadDir . $oldFileName);
        }

        // 處理新文件
        $fileInfo = pathinfo($_FILES['E_IMG']['name']);
        $extension = $fileInfo['extension'];
        $newFileName = $_POST["E_ID"] . '.' . $extension;

        // 移動新文件
        if (move_uploaded_file($_FILES['E_IMG']['tmp_name'], $uploadDir . $newFileName)) {
            $stmt->bindValue(":E_IMG", $newFileName);
        } else {
            throw new Exception("文件上傳失敗");
        }
    } else {
        // 如果沒有新文件，保留原來的文件名
        $stmt->bindValue(":E_IMG", $_POST["E_IMG"]);
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

