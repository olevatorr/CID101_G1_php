<?php
header('Content-Type: application/json');

// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/img/news/';

// 獲取當前腳本所在的目錄
$currentDir = dirname(__FILE__);

// 構建絕對路徑
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

try {
    require_once("config.php");

    // 開始事務
    $pdo->beginTransaction();
    // NS_ID = :NS_ID
    // 準備更新語句
    // 語句參數要對應
    $sql = "UPDATE NEWS SET N_TITLE = :N_TITLE, NS_ID = :NS_ID, N_CONTENT = :N_CONTENT, N_IMG = :N_IMG WHERE N_ID = :N_ID";
    $stmt = $pdo->prepare($sql);

    // 綁定基本參數
    $stmt->bindValue(":N_TITLE", $_POST["N_TITLE"]);
    $stmt->bindValue(":N_CONTENT", $_POST["N_CONTENT"]);
    $stmt->bindValue(":NS_ID", $_POST["NS_ID"]); // 添加這行以更新類別 ID
    $stmt->bindValue(":N_IMG", $_POST["N_IMG"]);
    $stmt->bindValue(":N_ID", $_POST["N_ID"]); 



    // 處理文件上傳
    // 檢查資料上傳成功還是失敗，檢查是否存在名為 N_IMG 的上傳檔案，並且檔案上傳是否成功。
    if (isset($_FILES['N_IMG']) && $_FILES['N_IMG']['error'] === UPLOAD_ERR_OK) {
        // 獲取舊文件名
        $oldFileQuery = $pdo->prepare("SELECT N_IMG FROM NEWS WHERE N_ID = :N_ID");
        $oldFileQuery->execute([':N_ID' => $_POST["N_ID"]]);
        $oldFileName = $oldFileQuery->fetchColumn();

        // 刪除舊文件
        if ($oldFileName && file_exists($uploadDir . $oldFileName)) {
            unlink($uploadDir . $oldFileName);
        }

        // 處理新文件
        $fileInfo = pathinfo($_FILES['N_IMG']['name']);
        $extension = $fileInfo['extension'];
        $newFileName = $_POST["N_ID"] . '.' . $extension;

        // 移動新文件
        if (move_uploaded_file($_FILES['N_IMG']['tmp_name'], $uploadDir . $newFileName)) {
            $stmt->bindValue(":N_IMG", $newFileName);
        } else {
            throw new Exception("文件上傳失敗");
        }
    } else {
        // 如果沒有新文件，保留原來的文件名
        $stmt->bindValue(":N_IMG", $_POST["N_IMG"]);
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