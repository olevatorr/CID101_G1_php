<?php
//以用來提供憑證，以便用戶代理與伺服器進行身份驗證
// header('Access-Control-Allow-Headers: Content-Type, Authorization');

try {
    require_once("config.php"); // 引入資料庫配置文件

    // 從請求中讀取 JSON 輸入數據
    $data = json_decode(file_get_contents('php://input'), true);

    // 檢查是否存在必要的 POST 數據
    if (isset($data["E_TITLE"]) && isset($data["E_ADDRESS"]) && isset($data["E_AREA"]) && isset($data["E_DATE"]) && isset($data["E_START"]) && isset($data["E_DEADLINE"]) && isset($data["E_SIGN_UP"]) && isset($data["E_CONTENT"]) && isset($data["E_STATUS"])&& isset($data["E_MAX_ATTEND"])) {
        // 準備 SQL 語句，將知識數據插入到數據庫中
        $sql = "INSERT INTO events (E_TITLE, E_ADDRESS, E_AREA, E_DATE,E_START,E_DEADLINE,E_SIGN_UP,E_CONTENT,E_STATUS,E_MAX_ATTEND) 
                VALUES (:E_TITLE, :E_ADDRESS, :E_AREA, :E_DATE, :E_START, :E_DEADLINE, :E_SIGN_UP, :E_CONTENT, :E_STATUS, :E_MAX_ATTEND)";
        $eventsStmt = $pdo->prepare($sql); // 準備 SQL 語句
        $eventsStmt->bindValue(":E_TITLE", $data["E_TITLE"]); // 綁定 E_TITLE 參數
        $eventsStmt->bindValue(":E_ADDRESS", $data["E_ADDRESS"]); // 綁定 E_ADDRESS 參數
        $eventsStmt->bindValue(":E_AREA", $data["E_AREA"]); // 綁定 E_AREA 參數
        $eventsStmt->bindValue(":E_DATE", $data["E_DATE"]); // 綁定 E_DATE 參數
        $eventsStmt->bindValue(":E_START", $data["E_START"]); // 綁定 E_START 參數
        $eventsStmt->bindValue(":E_DEADLINE", $data["E_DEADLINE"]); // 綁定 E_DEADLINE 參數
        $eventsStmt->bindValue(":E_SIGN_UP", $data["E_SIGN_UP"]); // 綁定 E_SIGN_UP 參數
        $eventsStmt->bindValue(":E_CONTENT", $data["E_CONTENT"]); // 綁定 E_CONTENT 參數
        $eventsStmt->bindValue(":E_STATUS", $data["E_STATUS"]); // 綁定 E_STATUS 參數
        $eventsStmt->bindValue(":E_MAX_ATTEND", $data["E_MAX_ATTEND"]); // 綁定 E_STATUS 參數
        $eventsStmt->execute(); // 執行 SQL 語句
        $events = $data; // 設置響應數據

        // 返回成功的 JSON 響應
        echo json_encode(["error" => false, "msg" => "新增資料成功", "events" => $events]);
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

