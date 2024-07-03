<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

try {
    require_once("config.php");
    $sql = "UPDATE product SET P_NAME = :P_NAME, P_PRICE = :P_PRICE, P_SUBTITLE = :P_SUBTITLE, P_CONTENT = :P_CONTENT, P_MATERIAL = :P_MATERIAL , P_SIZE = :P_SIZE , P_COLOR = :P_COLOR, P_STATUS = :P_STATUS WHERE P_ID = :P_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":P_NAME", $data["P_NAME"]);
    $stmt->bindValue(":P_PRICE", $data["P_PRICE"]);
    $stmt->bindValue(":P_SUBTITLE", $data["P_SUBTITLE"]);
    $stmt->bindValue(":P_CONTENT", $data["P_CONTENT"]);
    $stmt->bindValue(":P_MATERIAL", $data["P_MATERIAL"]);
    $stmt->bindValue(":P_SIZE", $data["P_SIZE"]);
    $stmt->bindValue(":P_COLOR", $data["P_COLOR"]);
    $stmt->bindValue(":P_STATUS", $data["P_STATUS"]);
    $stmt->bindValue(":P_ID", $data["P_ID"]); 
    $stmt->execute();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>
