<?php
// 允許所有來源訪問這個API，設置CORS頭
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 如果是 OPTIONS 請求，直接返回
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 連接到MySQL數據庫
$conn = mysqli_connect("localhost", "root", "", "bluealert");

// 檢查數據庫連接是否成功
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => '數據庫連接失敗: ' . $conn->connect_error]));
}

// 設置數據庫字符集為UTF-8
mysqli_set_charset($conn, "utf8");

// 獲取POST數據
$postData = json_decode(file_get_contents('php://input'), true);

// 驗證並清理輸入數據
$name = mysqli_real_escape_string($conn, $postData['name']);
$phone = mysqli_real_escape_string($conn, $postData['phone']);
$email = mysqli_real_escape_string($conn, $postData['email']);
$message = mysqli_real_escape_string($conn, $postData['message']);
$date = date('Y-m-d H:i:s');

// 插入數據到INQUIRY表
$sql = "INSERT INTO INQUIRY (I_NAME, I_PHONE, I_EMAIL, I_CONTENT, I_DATE, I_STATUS) 
        VALUES ('$name', '$phone', '$email', '$message', '$date', 0)";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => '表單提交成功']);
} else {
    echo json_encode(['success' => false, 'message' => '表單提交失敗: ' . mysqli_error($conn)]);
}

// 關閉數據庫連接
mysqli_close($conn);
?>