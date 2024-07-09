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

    // 準備 SQL 語句，將商品數據插入到數據庫中
    $sql = "INSERT INTO PRODUCT (P_NAME, P_PRICE, P_SUBTITLE, P_CONTENT, P_MATERIAL, P_SIZE, P_COLOR, P_STATUS)
            VALUES (:P_NAME, :P_PRICE, :P_SUBTITLE, :P_CONTENT, :P_MATERIAL, :P_SIZE, :P_COLOR, :P_STATUS)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":P_NAME", $_POST["P_NAME"]);
    $stmt->bindValue(":P_PRICE", $_POST["P_PRICE"]);
    $stmt->bindValue(":P_SUBTITLE", $_POST["P_SUBTITLE"]);
    $stmt->bindValue(":P_CONTENT", $_POST["P_CONTENT"]);
    $stmt->bindValue(":P_MATERIAL", $_POST["P_MATERIAL"]);
    $stmt->bindValue(":P_SIZE", $_POST["P_SIZE"]);
    $stmt->bindValue(":P_COLOR", $_POST["P_COLOR"]);
    $stmt->bindValue(":P_STATUS", $_POST["P_STATUS"]);
    $stmt->execute();

    $P_ID = $pdo->lastInsertId();

    // 處理文件上傳（如果有的話）
    $imageFields = ['P_MAIN_IMG', 'P_IMG1', 'P_IMG2'];
    foreach ($imageFields as $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $fileInfo = pathinfo($_FILES[$field]['name']);
            $ext = $fileInfo['extension'];
            $newFilename;
            if($field === 'P_MAIN_IMG'){
                $newFilename = $P_ID  . '-1.' . $ext;
            } elseif ($field === 'P_IMG1') {
                $newFilename = $P_ID  . '-2.' . $ext;
            } else {
                $newFilename = $P_ID  . '-3.' . $ext;
            }

            // 移動上傳的文件
            if (!move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $newFilename)) {
                throw new Exception($field . ' 文件上傳失敗');
            }

            // 更新數據庫中的文件名
            $updateSql = "UPDATE product SET $field = :$field WHERE P_ID = :P_ID";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindParam(":$field", $newFilename);
            $updateStmt->bindParam(':P_ID', $P_ID);
            $updateStmt->execute();
        }
    }

    // 提交事務
    $pdo->commit();

    echo json_encode(['error' => false, 'msg' => '新增資料成功', 'P_ID' => $P_ID]);

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
//以用來提供憑證，以便用戶代理與伺服器進行身份驗證
// header('Access-Control-Allow-Headers: Content-Type, Authorization');

//try {
    require_once("config.php"); // 引入資料庫配置文件

    // 從請求中讀取 JSON 輸入數據
    //$data = json_decode(file_get_contents('php://input'), true);

    // 檢查是否存在必要的 POST 數據  //共八個
    if (isset($data["P_NAME"]) && isset($data["P_PRICE"]) && isset($data["P_SUBTITLE"]) && isset($data["P_CONTENT"]) && isset($data["P_MATERIAL"]) && isset($data["P_SIZE"]) && isset($data["P_COLOR"]) && isset($data["P_STATUS"])) {
        // 準備 SQL 語句，將商品數據插入到數據庫中
        $sql = "INSERT INTO product (P_NAME, P_PRICE, P_SUBTITLE, P_CONTENT, P_MATERIAL, P_SIZE, P_COLOR, P_STATUS) 
                VALUES (:P_NAME, :P_PRICE, :P_SUBTITLE, :P_CONTENT, :P_MATERIAL, :P_SIZE, :P_COLOR, :P_STATUS)";
        $productStmt = $pdo->prepare($sql); // 準備 SQL 語句
        $productStmt->bindValue(":P_NAME", $POST["P_NAME"]); // 綁定 P_NAME 參數
        $productStmt->bindValue(":P_PRICE", $POST["P_PRICE"]); // 綁定 P_PRICE 參數
        $productStmt->bindValue(":P_SUBTITLE", $POST["P_SUBTITLE"]); // 綁定 P_SUBTITLE 參數
        $productStmt->bindValue(":P_CONTENT", $POST["P_CONTENT"]); // 綁定 P_CONTENT 參數
        $productStmt->bindValue(":P_MATERIAL", $POST["P_MATERIAL"]); // 綁定 P_MATERIAL 參數
        $productStmt->bindValue(":P_SIZE", $POST["P_SIZE"]); // 綁定 P_SIZE 參數
        $productStmt->bindValue(":P_COLOR", $POST["P_COLOR"]); // 綁定 P_COLOR 參數
        $productStmt->bindValue(":P_STATUS", $POST["P_STATUS"]); // 綁定 P_STATUS 參數
        $productStmt->execute(); // 執行 SQL 語句
        $product = $POST; // 設置響應數據

        // 返回成功的 JSON 響應
        echo json_encode(["error" => false, "msg" => "新增資料成功", "product" => $product]);
    } else {
        // 返回缺少必要 POST 數據的 JSON 響應
        echo json_encode(["error" => true, "msg" => "缺少必要的POST數據"]);
    }
//} catch (PDOException $e) {
    // 處理 PDO 異常，並返回 JSON 響應
    //$result = ["error" => true, "msg" => $e->getMessage()];
    //echo json_encode($result);
//}
?>

