<?php
require 'config.php';

// 允許所有來源訪問這個API，設置CORS頭
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 如果是 OPTIONS 請求，直接返回
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 獲取POST數據
$postData = file_get_contents('php://input');

if (!$postData) {
    echo json_encode(['success' => false, 'message' => '無效的輸入數據']);
    exit;
}

// 驗證並清理輸入數據
$name = $postData['name'] ?? '';
$phone = $postData['phone'] ?? '';
$email = $postData['email'] ?? '';
$message = $postData['message'] ?? '';
$date = date('Y-m-d H:i:s');

// 驗證必填字段
if (empty($name) || empty($phone) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => '所有字段都是必填的']);
    exit;
}

try {
    // 使用PDO進行數據庫操作
    $sql = "INSERT INTO INQUIRY (I_NAME, I_PHONE, I_EMAIL, I_CONTENT, I_DATE, I_STATUS) 
            VALUES (:name, :phone, :email, :message, :date, 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $name,
        ':phone' => $phone,
        ':email' => $email,
        ':message' => $message,
        ':date' => $date,
    ]);

    echo json_encode(['success' => true, 'message' => '表單提交成功']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => '表單提交失敗: ' . $e->getMessage()]);
}
?>
