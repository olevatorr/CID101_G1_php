<?php
// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/img/EVENTS/';

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

    // 準備 SQL 語句，將知識數據插入到數據庫中
    $sql = "INSERT INTO EVENTS (E_DATE, E_DEADLINE, E_ADDRESS, E_TITLE, E_STATUS) 
                VALUES (:E_DATE, :E_DEADLINE, :E_ADDRESS, :E_TITLE, :E_STATUS)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":E_DATE", $_POST["E_DATE"]); // 綁定 E_DATE 參數
    $stmt->bindParam(":E_DEADLINE", $_POST["E_DEADLINE"]); // 綁定 E_DEADLINE 參數
    $stmt->bindParam(":E_ADDRESS", $_POST["E_ADDRESS"]); // 綁定 E_ADDRESS 參數
    $stmt->bindParam(":E_TITLE", $_POST["E_TITLE"]); // 綁定 E_TITLE 參數
    $stmt->bindParam(":E_STATUS", $_POST["E_STATUS"]); // 綁定 E_STATUS 參數
    $stmt->execute();

    $E_ID = $pdo->lastInsertId();

    // 處理文件上傳
    if (isset($_FILES['E_IMG']) && $_FILES['E_IMG']['error'] === UPLOAD_ERR_OK) {
        $fileInfo = pathinfo($_FILES['E_IMG']['name']);
        $ext = $fileInfo['extension'];
        $newFilename = $E_ID . '.' . $ext;

        // 移動上傳的文件
        if (!move_uploaded_file($_FILES['E_IMG']['tmp_name'], $uploadDir . $newFilename)) {
            throw new Exception('文件上傳失敗');
        }

        // 更新數據庫中的文件名
        $updateSql = "UPDATE EVENTS SET E_IMG = :E_IMG WHERE E_ID = :E_ID";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->bindParam(':E_IMG', $newFilename);
        $updateStmt->bindParam(':E_ID', $E_ID);
        $updateStmt->execute();
    }

    // 提交事務
    $pdo->commit();

    echo json_encode(['error' => false, 'msg' => '新增資料成功', 'E_ID' => $E_ID]);

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

<?php

//  ----------------純文字資料版本如下
// 0777?
// 0: 沒有任何權限
// 1: 執行權限
// 2: 寫入權限
// 3: 執行和寫入權限
// 4: 讀取權限
// 5: 讀取和執行權限
// 6: 讀取和寫入權限
// 7: 讀取、寫入和執行權限
// try {
//     require_once("config.php"); // 引入資料庫配置文件

//     // 從請求中讀取 JSON 輸入數據
//     $data = json_decode(file_get_contents('php://input'), true);

//     // 檢查是否存在必要的 POST 數據
//     if (isset($data["K_TITLE"]) && isset($data["K_CONTENT"]) && isset($data["K_FROM"]) && isset($data["K_URL"]) && isset($data["K_DATE"])) {
//         // 準備 SQL 語句，將知識數據插入到數據庫中
//         $sql = "INSERT INTO KNOWLEDGE (K_TITLE, K_CONTENT, K_FROM, K_URL, K_DATE) 
//                 VALUES (:K_TITLE, :K_CONTENT, :K_FROM, :K_URL, :K_DATE)";

//         $knowledgeStmt = $pdo->prepare($sql); // 準備 SQL 語句
//         $knowledgeStmt->bindValue(":K_TITLE", $data["K_TITLE"]); // 綁定 K_TITLE 參數
//         $knowledgeStmt->bindValue(":K_CONTENT", $data["K_CONTENT"]); // 綁定 K_CONTENT 參數
//         $knowledgeStmt->bindValue(":K_FROM", $data["K_FROM"]); // 綁定 K_FROM 參數
//         $knowledgeStmt->bindValue(":K_URL", $data["K_URL"]); // 綁定 K_URL 參數
//         $knowledgeStmt->bindValue(":K_DATE", $data["K_DATE"]); // 綁定 K_DATE 參數
//         $knowledgeStmt->execute(); // 執行 SQL 語句
//         $knowledge = $data; // 設置響應數據

//         // 返回成功的 JSON 響應
//         echo json_encode(["error" => false, "msg" => "新增資料成功", "knowledge" => $knowledge]);
//     } else {
//         // 返回缺少必要 POST 數據的 JSON 響應
//         echo json_encode(["error" => true, "msg" => "缺少必要的POST數據"]);
//     }
// } catch (PDOException $e) {
//     // 處理 PDO 異常，並返回 JSON 響應
//     $result = ["error" => true, "msg" => $e->getMessage()];
//     echo json_encode($result);
// }
// ?>

