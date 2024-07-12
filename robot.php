<?php
try {
    require_once("config.php"); // 引入資料庫配置文件

    $sql = "select * from ROBOT_QA"; // 準備 SQL 查詢語句，從資料庫中選擇所有知識數據
    $ROBOT = $pdo->query($sql); // 執行 SQL 查詢調用pdo裡的函式
    $prodRows = $ROBOT->fetchAll(PDO::FETCH_ASSOC); // 獲取所有查詢結果行，並以關聯數組的形式返回

    $result = ["error" => false, "msg" => "", "ROBOT" => $prodRows]; // 準備成功的 JSON 響應數據
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; // 捕獲 PDO 異常，並準備錯誤的 JSON 響應數據
}

echo json_encode($result, JSON_NUMERIC_CHECK); // 將 PHP 數組轉換為 JSON 格式並輸出

?>
