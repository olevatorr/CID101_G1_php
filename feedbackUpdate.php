<?php
header('Content-Type: application/json');

try {
    require_once("config.php");

    // 获取并解析 POST 数据
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input["F_STATUS"]) || !isset($input["F_ID"])) {
        throw new Exception("Missing F_STATUS or F_ID");
    }

    // 开始事务
    $pdo->beginTransaction();

    // 准备更新语句
    $sql = "UPDATE FEEDBACK SET F_STATUS = :F_STATUS WHERE F_ID = :F_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":F_STATUS", $input["F_STATUS"]);
    $stmt->bindValue(":F_ID", $input["F_ID"]);

    // 执行更新
    $stmt->execute();

    // 提交事务
    $pdo->commit();

    echo json_encode(["error" => false, "msg" => "更新成功"]);
} catch (Exception $e) {
    // 回滚事务
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["error" => true, "msg" => $e->getMessage()]);
}
?>
