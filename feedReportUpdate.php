<?php
header('Content-Type: application/json');

try {
    require_once("config.php");

    // 获取并解析 POST 数据
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input["ER_ID"]) || !isset($input["F_ID"])) {
        throw new Exception("Missing ER_ID or F_ID");
    }

    // 开始事务
    $pdo->beginTransaction();

    // 准备更新语句
    $sql = "UPDATE FEEDBACK SET F_STATUS = :F_STATUS WHERE F_ID = :F_ID";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":F_STATUS", $input["F_STATUS"]);
    $stmt->bindValue(":F_ID", $input["F_ID"]);

    $stmt->execute();
    $sql2 = "UPDATE EVENT_REPORTS SET ES_STATUS = :ES_STATUS WHERE ER_ID = :ER_ID";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindValue(":ES_STATUS", $input["ES_STATUS"]);
    $stmt2->bindValue(":ER_ID", $input["ER_ID"]);

    // 执行更新
    $stmt2->execute();

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
