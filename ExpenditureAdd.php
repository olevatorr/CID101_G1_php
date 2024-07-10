<?php


// 設置響應頭為 JSON
header('Content-Type: application/json');

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 從請求中讀取 JSON 輸入數據
    $data = json_decode(file_get_contents('php://input'), true);

    // 檢查是否存在必要的 POST 數據
    if (isset($data["EL_NAME"]) && isset($data["EL_TITLE"]) && isset($data["EL_OUTLAY"])) {
        // 準備 SQL 語句，將知識數據插入到數據庫中
        $sql = "INSERT INTO EXPENDITURE_LIST (EL_NAME, EL_TITLE, EL_OUTLAY) 
                VALUES (:EL_NAME, :EL_TITLE, :EL_OUTLAY)";

        $EXPENDITURE_LISTStmt = $pdo->prepare($sql); // 準備 SQL 語句
        $EXPENDITURE_LISTStmt->bindValue(":EL_NAME", $data["EL_NAME"]); // 綁定 EL_NAME 參數
        $EXPENDITURE_LISTStmt->bindValue(":EL_TITLE", $data["EL_TITLE"]); // 綁定 EL_TITLE 參數
        $EXPENDITURE_LISTStmt->bindValue(":EL_OUTLAY", $data["EL_OUTLAY"]); // 綁定 EL_OUTLAY 參數
        $EXPENDITURE_LISTStmt->execute(); // 執行 SQL 語句
        $EXPENDITURE_LIST = $data; // 設置響應數據

        // 返回成功的 JSON 響應
        echo json_encode(["error" => false, "msg" => "新增資料成功", "EXPENDITURE_LIST" => $EXPENDITURE_LIST]);
    } else {
        // 返回缺少必要 POST 數據的 JSON 響應
        echo json_encode(["error" => true, "msg" => "缺少必要的POST數據"]);
    }
} catch (PDOException $e) {
    // 處理 PDO 異常，並返回 JSON 響應
    $result = ["error" => true, "msg" => $e->getMessage()];
    echo json_encode($result);
}
// ?>

