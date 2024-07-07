<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); // 允許的來源
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // 允許的 HTTP 方法
header('Access-Control-Allow-Headers: Content-Type, Authorization'); // 允許的頭部

// 如果是 OPTIONS 请求，返回 HTTP 状态码 204 并退出脚本
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 記錄原始輸入
$raw_input = file_get_contents("php://input");
error_log("Raw input: " . $raw_input);

// 取得前端數據
$data = json_decode($raw_input);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(["error" => true, "message" => "無效的 JSON 數據: " . json_last_error_msg()]);
    exit;
}

error_log("Decoded data: " . print_r($data, true));

// 確保數據不為空
if (isset($data->U_ID) && isset($data->U_STATUS)) {
    // Include database configuration file
    try {
        require_once("config.php");
        
        // 開始事務
        $pdo->beginTransaction();
        
        // 準備SQL語句
        $id = (int)$data->U_ID;
        $status = (int)$data->U_STATUS;
        
        // 驗證狀態值
        if ($status !== 0 && $status !== 1) {
            throw new Exception("無效的狀態值");
        }
        
        $sql = "UPDATE USER SET U_STATUS = :status WHERE U_ID = :id";
        $stmt = $pdo->prepare($sql);
        
        // 綁定參數
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // 執行語句
        if ($stmt->execute()) {
            // 提交事務
            $pdo->commit();
            http_response_code(200);
            echo json_encode(["error" => false, "message" => "停權狀態已更新"]);
        } else {
            throw new Exception("無法更新停權狀態");
        }
    } catch (Exception $e) {
        // 回滾事務
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(500);
        echo json_encode(["error" => true, "message" => "數據庫錯誤: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => true, "message" => "無法更新停權狀態。數據不完整"]);
}
?>
