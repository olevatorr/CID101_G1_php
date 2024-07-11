<?php
// 启用错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 设置响应头为 JSON
header('Content-Type: application/json');

// 引入数据库配置文件
require_once("config.php");

try {
    // 开始事务
    $pdo->beginTransaction();

    // 从请求中读取 JSON 输入数据
    $data = json_decode(file_get_contents('php://input'), true);

    // 检查是否存在必要的数据
    if (isset($data["U_ID"]) && isset($data["P_ID"])) {
        // 检查是否已存在相同的收藏记录
        $checkSql = "SELECT COUNT(*) FROM PRODUCT_COLLECTION WHERE U_ID = :U_ID AND P_ID = :P_ID";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindValue(":U_ID", $data["U_ID"]);
        $checkStmt->bindValue(":P_ID", $data["P_ID"]);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() > 0) {
            throw new Exception("该商品已被收藏");
        }

        // 准备 SQL 语句，将收藏数据插入到数据库中
        $sql = "INSERT INTO PRODUCT_COLLECTION (U_ID, P_ID) VALUES (:U_ID, :P_ID)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":U_ID", $data["U_ID"]);
        $stmt->bindValue(":P_ID", $data["P_ID"]);
        $stmt->execute();

        // 提交事务
        $pdo->commit();

        echo json_encode([
            "error" => false, 
            "msg" => "收藏成功", 
            "U_ID" => $data["U_ID"],
            "P_ID" => $data["P_ID"]
        ]);
    } else {
        throw new Exception("缺少必要的数据");
    }
} catch (PDOException $e) {
    // 回滚事务
    $pdo->rollBack();
    echo json_encode(['error' => true, 'msg' => '数据库错误: ' . $e->getMessage()]);
} catch (Exception $e) {
    // 回滚事务
    $pdo->rollBack();
    echo json_encode(['error' => true, 'msg' => $e->getMessage()]);
}
?>