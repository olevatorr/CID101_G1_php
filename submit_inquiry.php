<?php
require "config.php";

try {
    // 獲取POST數據
    $postData = json_decode(file_get_contents('php://input'), true);
    // 使用PDO進行數據庫操作
    $sql = "INSERT INTO INQUIRY (I_NAME, I_PHONE, I_EMAIL, I_CONTENT, I_DATE)
            VALUES (:I_NAME, :I_PHONE, :I_EMAIL, :I_CONTENT, :I_DATE)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":I_NAME", $postData["name"]);
    $stmt->bindValue(":I_PHONE", $postData["phone"]);
    $stmt->bindValue(":I_EMAIL", $postData["email"]);
    $stmt->bindValue(":I_CONTENT", $postData["message"]);
    $stmt->bindValue(":I_DATE", date("Y-m-d H:i:s"));
    $stmt->execute();

    echo json_encode(["success" => true, "message" => "表單提交成功"]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "表單提交失敗: " . $e->getMessage()]);
}
?>