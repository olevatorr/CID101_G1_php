<?php
header('Content-Type: application/json');

// 確保 K_ID 參數存在
if (!isset($_GET["K_ID"])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => true, "msg" => "缺少 K_ID 參數"]);
    exit;
}
// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/img/knowledge/';

// 獲取當前腳本所在的目錄
$currentDir = dirname(__FILE__);

// 構建絕對路徑
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

try {
    // 引入資料庫連接配置文件
    require_once("config.php");

    // 開始事務
    $pdo->beginTransaction();

    // 首先獲取要刪除的記錄的圖片文件名
    $getImageSql = "SELECT K_URL FROM KNOWLEDGE WHERE K_ID = ?";
    $getImageStmt = $pdo->prepare($getImageSql);
    $getImageStmt->execute([$_GET["K_ID"]]);
    $imageFileName = $getImageStmt->fetchColumn();

    // 定義 SQL 刪除語句，用於刪除 `knowledge` 表中指定 `K_ID` 的記錄
    $deleteSql = "DELETE FROM KNOWLEDGE WHERE K_ID = ?";

    // 使用 PDO 對象準備 SQL 語句
    $deleteStmt = $pdo->prepare($deleteSql);

    // 執行 SQL 語句
    $deleteStmt->execute([$_GET["K_ID"]]);

    // 獲取受影響的行數
    $affectedCount = $deleteStmt->rowCount();

    // 如果數據庫記錄刪除成功，則刪除對應的圖片文件
    if ($affectedCount > 0 && $imageFileName) {
        $fullImagePath = $uploadDir . $imageFileName;
        if (file_exists($fullImagePath)) {
            unlink($fullImagePath);
        }
    }

    // 提交事務
    $pdo->commit();

    // 創建結果數組，包含操作是否成功的標誌和影響的行數
    $result = ["error" => false, "msg" => "成功刪除 {$affectedCount} 筆記錄及其關聯文件"];
} catch (Exception $e) {
    // 如果發生例外，回滾事務
    $pdo->rollBack();

    // 創建結果數組，包含錯誤標誌和錯誤信息
    $result = ["error" => true, "msg" => $e->getMessage()];
}

// 將結果數組編碼為 JSON 格式並輸出
echo json_encode($result, JSON_NUMERIC_CHECK);



// --------純文字如下
// // 使用 try-catch 塊捕捉潛在的例外錯誤
// try {
//     // 引入資料庫連接配置文件
//     require_once("config.php");

//     // 定義 SQL 刪除語句，用於刪除 `knowledge` 表中指定 `K_ID` 的記錄
//     $sql = "delete from KNOWLEDGE where K_ID = ?";

//     // 使用 PDO 對象準備 SQL 語句
//     $knowledge = $pdo->prepare($sql);

//     // 綁定 GET 請求中的 `K_ID` 參數到 SQL 語句中的第一個問號佔位符
//     $knowledge->bindValue(1, $_GET["K_ID"]);

//     // 執行 SQL 語句
//     $knowledge->execute();

//     // 獲取受影響的行數
//     $affectedCount = $knowledge->rowCount();

//     // 創建結果數組，包含操作是否成功的標誌和影響的行數
//     $result = ["error" => false, "msg" => "成功的影響{$affectedCount}筆"];
// } catch (PDOException $e) {
//     // 如果發生例外，創建結果數組，包含錯誤標誌和錯誤信息
//     $result = ["error" => true, "msg" => $e->getMessage()];
// }

// // 將結果數組編碼為 JSON 格式並輸出
// echo json_encode($result, JSON_NUMERIC_CHECK);

?>

