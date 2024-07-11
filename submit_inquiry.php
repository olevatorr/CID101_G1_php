<?php
require_once("config.php");

// 獲取POST數據
$postData = file_get_contents('php://input');

if ($postData === false) {
    echo json_encode(['success' => false, 'message' => '無法獲取輸入數據']);
    exit;
}

$postData = json_decode($postData, true);

if ($postData === null) {
    echo json_encode(['success' => false, 'message' => '無效的JSON格式']);
    exit;
}

// 驗證並清理輸入數據
$name = $postData['name'] ?? '';
$phone = $postData['phone'] ?? '';
$email = $postData['email'] ?? '';
$message = $postData['message'] ?? '';
$date = date('Y-m-d H:i:s');

// 檢查必填字段
if (empty($name) || empty($phone) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => '所有字段都是必填的']);
    exit;
}

// 使用預處理語句插入數據到INQUIRY表
$stmt = $conn->prepare("INSERT INTO INQUIRY (I_NAME, I_PHONE, I_EMAIL, I_CONTENT, I_DATE, I_STATUS) VALUES (?, ?, ?, ?, ?, 0)");
$stmt->bind_param('sssss', $name, $phone, $email, $message, $date);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => '表單提交成功']);
} else {
    echo json_encode(['success' => false, 'message' => '表單提交失敗: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
