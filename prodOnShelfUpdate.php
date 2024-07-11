<?php
header('Content-Type: application/json');

try {
    require_once("config.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $sql = "UPDATE PRODUCT SET P_STATUS = :P_STATUS WHERE P_ID = :P_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":P_STATUS", $data["P_STATUS"]);
    $stmt->bindValue(":P_ID", $data["P_ID"]); 
    $stmt->execute();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>