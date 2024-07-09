<?php
try {
    require_once("config.php"); // 引入資料庫配置文件

    // 從請求中讀取 JSON 輸入數據
    $data = json_decode(file_get_contents('php://input'), true);

    // 檢查是否存在必要的 POST 數據
    if (isset($data["AD_ACCOUNT"]) && isset($data["AD_PSW"]) && isset($data["AD_NAME"]) && isset($data["AD_LEVEL"])) {
        $today = date('Y-m-d');
        // 準備 SQL 語句，將知識數據插入到數據庫中
        $sql = "INSERT ADMIN SET AD_NAME = :AD_NAME, AD_ACCOUNT = :AD_ACCOUNT, AD_PSW = :AD_PSW, AD_LEVEL = :AD_LEVEL, AD_DATE = :AD_DATE";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":AD_NAME", $data["AD_NAME"]);
        $stmt->bindValue(":AD_ACCOUNT", $data["AD_ACCOUNT"]);
        $stmt->bindValue(":AD_PSW", $data["AD_PSW"]);
        $stmt->bindValue(":AD_LEVEL", $data["AD_LEVEL"]);
        $stmt->bindParam(':AD_DATE', $today); 
        $stmt->execute();
        $admin = $data; // 設置響應數據

        // 返回成功的 JSON 響應
        echo json_encode(["error" => false, "msg" => "新增管理員成功", "admin" => $admin]);
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