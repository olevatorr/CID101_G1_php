<?php
// ------ 純文字版本
 header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

try {
    require_once("config.php");
    $sql = "UPDATE NEWS SET N_TITLE = :N_TITLE, N_CONTENT = :N_CONTENT WHERE N_ID = :N_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":N_TITLE", $data["N_TITLE"]);
    $stmt->bindValue(":N_CONTENT", $data["N_CONTENT"]);
    $stmt->bindValue(":NS_ID", $data["NS_ID"]); // 添加這行以更新類別 ID
    $stmt->bindValue(":N_ID", $data["N_ID"]); 
    $stmt->execute();

    echo json_encode(["error" => false, "msg" => "修改成功"]);
} catch (PDOException $e) {
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>