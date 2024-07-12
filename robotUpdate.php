<?php
header('Content-Type: application/json');

try {
    require_once("config.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $sql = "UPDATE ROBOT_QA SET R_QUESTION = :R_QUESTION, R_ANSWER = :R_ANSWER WHERE R_ID = :R_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":R_QUESTION", $data["R_QUESTION"]);
    $stmt->bindValue(":R_ANSWER", $data["R_ANSWER"]);
    $stmt->bindValue(":R_ID", $data["R_ID"]);
    $stmt->execute();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>