<?php
header("Access-Control-Allow-Origin:*");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
header('Content-Type: application/json');
require_once("bluealertKey.php");

try {
    // 獲取要删除的 ID
    $data = json_decode(file_get_contents("php://input"));

    $sql = "DELETE FROM knowledge WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $data->id]);

    echo json_encode(["error" => false, "msg" => "資料删除成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>
