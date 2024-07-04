<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

try {
    require_once("config.php");
    $sql = "UPDATE events SET E_TITLE = :E_TITLE, E_ADDRESS = :E_ADDRESS, E_AREA = :E_AREA, E_DATE = :E_DATE, E_START = :E_START ,E_DEADLINE = :E_DEADLINE, E_SIGN_UP = :E_SIGN_UP WHERE E_ID = :E_ID";
    $stmt = $pdo->prepare($sql);
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
    $stmt->bindValue(":E_ID", $data["E_ID"]); 
    $stmt->execute();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>
