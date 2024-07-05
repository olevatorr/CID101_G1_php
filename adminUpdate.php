<?php
header('Content-Type: application/json');

try {
    require_once("config.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $sql = "UPDATE ADMIN SET AD_NAME = :AD_NAME, AD_ACCOUNT = :AD_ACCOUNT, AD_PSW = :AD_PSW, AD_LEVEL = :AD_LEVEL WHERE AD_ID = :AD_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":AD_NAME", $data["AD_NAME"]);
    $stmt->bindValue(":AD_ACCOUNT", $data["AD_ACCOUNT"]);
    $stmt->bindValue(":AD_PSW", $data["AD_PSW"]);
    $stmt->bindValue(":AD_LEVEL", $data["AD_LEVEL"]);
    $stmt->bindValue(":AD_ID", $data["AD_ID"]); 
    $stmt->execute();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>