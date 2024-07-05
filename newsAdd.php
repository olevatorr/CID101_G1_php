<?php
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');
try {
    require_once("config.php"); // 引入資料庫配置文件

    // 從請求中讀取 JSON 輸入數據
    $data = json_decode(file_get_contents('php://input'), true);

    // 檢查是否存在必要的 POST 數據
    if (isset($data["N_TITLE"]) && isset($data["N_CONTENT"])&& isset($data["NS_ID"])){
        // 準備 SQL 語句，將知識數據插入到數據庫中
        $sql = "INSERT INTO NEWS (N_TITLE, N_CONTENT, NS_ID) 
                VALUES (:N_TITLE, :N_CONTENT, :NS_ID)";

        $NEWSStmt = $pdo->prepare($sql); // 準備 SQL 語句
        $NEWSStmt->bindValue(":N_TITLE", $data["N_TITLE"]); // 綁定 K_TITLE 參數
        $NEWSStmt->bindValue(":N_CONTENT", $data["N_CONTENT"]); // 綁定 K_CONTENT 參數
        $NEWSStmt->bindValue(":NS_ID", $data["NS_ID"]); // 綁定 NS_ID 參數
        $NEWSStmt->execute(); // 執行 SQL 語句
        $NEWS = $data; // 設置響應數據

        // 返回成功的 JSON 響應
        echo json_encode(["error" => false, "msg" => "新增資料成功", "NEWS" => $NEWS]);
    } else {
        // 返回缺少必要 POST 數據的 JSON 響應
        echo json_encode(["error" => true, "msg" => "缺少必要的POST數據"]);
    }
} catch (PDOException $e) {
    // 處理 PDO 異常，並返回 JSON 響應
    $result = ["error" => true, "msg" => $e->getMessage()];
    echo json_encode($result);
}
 ?>