<?php
try {
    require_once("config.php"); // 引入資料庫配置文件

    // 從請求中讀取 JSON 輸入數據
    $data = json_decode(file_get_contents('php://input'), true);

    // 檢查是否存在必要的 POST 數據
    if (isset($data["R_QUESTION"]) && isset($data["R_QUESTION"])) {
        $today = date('Y-m-d');
        // 準備 SQL 語句，將知識數據插入到數據庫中
        $sql = "INSERT ROBOT_QA SET R_QUESTION = :R_QUESTION, R_ANSWER = :R_ANSWER";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":R_QUESTION", $data["R_QUESTION"]);
        $stmt->bindValue(":R_ANSWER", $data["R_ANSWER"]);
        $stmt->execute();
        $robot = $data; // 設置響應數據

        // 返回成功的 JSON 響應
        echo json_encode(["error" => false, "msg" => "新增管理員成功", "robot" => $robot]);
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