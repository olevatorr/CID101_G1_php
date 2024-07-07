<?php

// 如果是 OPTIONS 请求，返回 HTTP 状态码 204 并退出脚本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php"); // 引入数据库配置文件

    $sql = "SELECT * FROM USER"; // 准备 SQL 查询语句，从数据库中选择所有记录
    $members = $pdo->query($sql); // 执行 SQL 查询
    $prodRows = $members->fetchAll(PDO::FETCH_ASSOC); // 获取所有查询结果行，并以关联数组的形式返回

    $result = ["error" => false, "msg" => "", "members" => $prodRows]; // 准备成功的 JSON 响应数据
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕获 PDO 异常，并准备错误的 JSON 响应数据
}

echo json_encode($result); // 将 PHP 数组转换为 JSON 格式并输出

?>
