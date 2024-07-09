<?php
header('Content-Type: application/json');

require_once("config.php");

function respond($statusCode, $message) {
    http_response_code($statusCode);
    echo json_encode(["error" => $statusCode >= 400, "message" => $message]);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'));

    if (json_last_error() !== JSON_ERROR_NONE) {
        respond(400, "無效的 JSON 數據");
    }

    if (!isset($data['U_ID'], $data['U_STATUS'])) {
        respond(400, "數據不完整");
    }

    $id = (int)$data['U_ID'];
    $status = (int)$data['U_STATUS'];

    if ($status !== 0 && $status !== 1) {
        respond(400, "無效的狀態值");
    }

    $pdo->beginTransaction();
    $sql = "UPDATE USER SET U_STATUS = :status WHERE U_ID = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $pdo->commit();
        respond(200, "停權狀態已更新");
    } else {
        $pdo->rollBack();
        respond(500, "無法更新停權狀態");
    }
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    respond(500, "數據庫錯誤: " . $e->getMessage());
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    respond(500, $e->getMessage());
}
?>
