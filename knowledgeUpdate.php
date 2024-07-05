<?php
header('Content-Type: application/json');

// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/img/knowledge/';

// 獲取當前腳本所在的目錄
$currentDir = dirname(__FILE__);

// 構建絕對路徑
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

try { 
    require_once("config.php");

    // 開始事務
    $pdo->beginTransaction();

    // 準備更新語句
    $sql = "UPDATE KNOWLEDGE SET K_TITLE = :K_TITLE, K_CONTENT = :K_CONTENT, K_FROM = :K_FROM, K_URL = :K_URL, K_DATE = :K_DATE WHERE K_ID = :K_ID";
    $stmt = $pdo->prepare($sql);

    // 綁定基本參數
    $stmt->bindValue(":K_TITLE", $_POST["K_TITLE"]);
    $stmt->bindValue(":K_CONTENT", $_POST["K_CONTENT"]);
    $stmt->bindValue(":K_FROM", $_POST["K_FROM"]);
    $stmt->bindValue(":K_DATE", $_POST["K_DATE"]);
    $stmt->bindValue(":K_ID", $_POST["K_ID"]);

    // 處理文件上傳
    if (isset($_FILES['K_URL']) && $_FILES['K_URL']['error'] === UPLOAD_ERR_OK) {
        // 獲取舊文件名
        $oldFileQuery = $pdo->prepare("SELECT K_URL FROM KNOWLEDGE WHERE K_ID = :K_ID");
        $oldFileQuery->execute([':K_ID' => $_POST["K_ID"]]);
        $oldFileName = $oldFileQuery->fetchColumn();

        // 刪除舊文件
        if ($oldFileName && file_exists($uploadDir . $oldFileName)) {
            unlink($uploadDir . $oldFileName);
        }

        // 處理新文件
        $fileInfo = pathinfo($_FILES['K_URL']['name']);
        $extension = $fileInfo['extension'];
        $newFileName = $_POST["K_ID"] . '.' . $extension;

        // 移動新文件
        if (move_uploaded_file($_FILES['K_URL']['tmp_name'], $uploadDir . $newFileName)) {
            $stmt->bindValue(":K_URL", $newFileName);
        } else {
            throw new Exception("文件上傳失敗");
        }
    } else {
        // 如果沒有新文件，保留原來的文件名
        $stmt->bindValue(":K_URL", $_POST["K_URL"]);
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

// ------ 純文字版本
// header('Content-Type: application/json');
// $data = json_decode(file_get_contents('php://input'), true);

// try {
//     require_once("config.php");
//     $sql = "UPDATE KNOWLEDGE SET K_TITLE = :K_TITLE, K_CONTENT = :K_CONTENT, K_FROM = :K_FROM, K_URL = :K_URL, K_DATE = :K_DATE WHERE K_ID = :K_ID";
//     $stmt = $pdo->prepare($sql);
//     $stmt->bindValue(":K_TITLE", $data["K_TITLE"]);
//     $stmt->bindValue(":K_CONTENT", $data["K_CONTENT"]);
//     $stmt->bindValue(":K_FROM", $data["K_FROM"]);
//     $stmt->bindValue(":K_URL", $data["K_URL"]);
//     $stmt->bindValue(":K_DATE", $data["K_DATE"]);
//     $stmt->bindValue(":K_ID", $data["K_ID"]); 
//     $stmt->execute();

//     echo json_encode(["error" => false, "msg" => "修改成功"]);
// } catch (PDOException $e) {
//     echo json_encode(["error" => true, "msg" => $e->getMessage()]);
// }
?>
