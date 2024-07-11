<?php
header('Content-Type: application/json');

require_once("config.php");

<<<<<<< HEAD
try {
    $data = json_decode(file_get_contents('php://input'), true);
=======

try {
    $data = json_decode(file_get_contents('php://input'), true);

>>>>>>> 93863d730fd83529042ced6b7edd14bf816c09f1

    $pdo->beginTransaction();
    $sql = "UPDATE USER SET U_STATUS = :U_STATUS WHERE U_ID = :U_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':U_STATUS', $data['U_STATUS']);
    $stmt->bindValue(':U_ID', $data['U_ID']);
    $stmt->execute();
    $user = $data;

    echo json_encode(["error" => false, "msg" => "新增管理員成功", "user" => $user]);
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()];
    echo json_encode($result);
}
?>
