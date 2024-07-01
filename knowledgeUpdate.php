<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

try {
    require_once("config.php");
    $sql = "UPDATE knowledge (K_TITLE, K_CONTENT, K_FROM, K_URL, K_DATE) 
    VALUES (:K_TITLE, :K_CONTENT, :K_FROM, :K_URL, :K_DATE)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":K_TITLE", $data["K_TITLE"]);
    $stmt->bindValue(":K_CONTENT", $data["K_CONTENT"]);
    $stmt->bindValue(":K_FROM", $data["K_FROM"]);
    $stmt->bindValue(":K_URL", $data["K_URL"]);
    $stmt->bindValue(":K_DATE", $data["K_DATE"]);
    $stmt->execute();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>
