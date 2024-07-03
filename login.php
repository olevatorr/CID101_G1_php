<?php

try {
    require_once("config.php");
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data["username"]) || !isset($data['password'])) {
        throw new Exception('Username and password are required');
    }
    
    $username = $data['username'];
    $password = $data['password'];

    $sql = "SELECT * FROM user WHERE U_ACCOUNT = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute(); 


    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debugging: print the user data
    // error_log(print_r($user, true));
    // exit("=====" . print_r($user));
    if ($user) {
        // Check if the U_PSW key exists before using it
        //會檢查用戶數據中是否包含 U_PSW 鍵，並且檢查提供的密碼是否與存儲在數據庫中的密碼匹配
        if (isset($user['U_PSW']) && $password === $user['U_PSW']) {
            echo json_encode(['code' => 1, 'memInfo' => $user]);
        } else {
            echo json_encode(['code' => 0, 'message' => 'Invalid username or password']);
        }
    } else {
        echo json_encode(['code' => 0, 'message' => 'User not found']);
    }
} catch (Exception $e) {
    echo json_encode(['code' => 0, 'message' => $e->getMessage()]);
}
?>
