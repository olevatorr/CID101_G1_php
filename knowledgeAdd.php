<?php
//以用來提供憑證，以便用戶代理與伺服器進行身份驗證
// header('Access-Control-Allow-Headers: Content-Type, Authorization');

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 從請求中讀取 JSON 輸入數據
    $data = json_decode(file_get_contents('php://input'), true);

    // 檢查是否存在必要的 POST 數據
    if (isset($data["K_TITLE"]) && isset($data["K_CONTENT"]) && isset($data["K_FROM"]) && isset($data["K_URL"]) && isset($data["K_DATE"])) {
        // 準備 SQL 語句，將知識數據插入到數據庫中
        $sql = "INSERT INTO knowledge (K_TITLE, K_CONTENT, K_FROM, K_URL, K_DATE) 
                VALUES (:K_TITLE, :K_CONTENT, :K_FROM, :K_URL, :K_DATE)";
        $knowledgeStmt = $pdo->prepare($sql); // 準備 SQL 語句
        $knowledgeStmt->bindValue(":K_TITLE", $data["K_TITLE"]); // 綁定 K_TITLE 參數
        $knowledgeStmt->bindValue(":K_CONTENT", $data["K_CONTENT"]); // 綁定 K_CONTENT 參數
        $knowledgeStmt->bindValue(":K_FROM", $data["K_FROM"]); // 綁定 K_FROM 參數
        $knowledgeStmt->bindValue(":K_URL", $data["K_URL"]); // 綁定 K_URL 參數
        $knowledgeStmt->bindValue(":K_DATE", $data["K_DATE"]); // 綁定 K_DATE 參數
        $knowledgeStmt->execute(); // 執行 SQL 語句
        $knowledge = $data; // 設置響應數據

        // 返回成功的 JSON 響應
        echo json_encode(["error" => false, "msg" => "新增資料成功", "knowledge" => $knowledge]);
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

