<?php
header('Content-Type: application/json');

try {
    require_once("config.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $sql = "UPDATE ADMIN SET AD_STATUS = :AD_STATUS WHERE AD_ID = :AD_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":AD_STATUS", $data["AD_STATUS"]);
    $stmt->bindValue(":AD_ID", $data["AD_ID"]); 
    $stmt->execute();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>