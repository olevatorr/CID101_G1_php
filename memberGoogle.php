<?php
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

try {
    // Get JSON request data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Ensure JSON data was successfully parsed
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON');
    }
    
    // Get user data from request
    if (!isset($data['email']) || empty($data['email'])) {
        throw new Exception('Email is required');
    }
    
    $email = $data['email'];
    $name = $data['name'] ?? '';
    $account = $data['account'] ?? '';
    
    // 檢查用戶是否存在
    $sql = "SELECT * FROM USER WHERE U_EMAIL = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // User exists
        if ($user['U_STATUS'] == 0) {
           // User is inactive
            echo json_encode(['success' => false, 'message' => '用戶已停權，無法登入']);
        } else {
            // User is active
            echo json_encode(['success' => true, 'message' => '登入成功！', 'user' => $user]);
            
        }
    } else {
        // User doesn't exist, create new user
        $insertSql = "INSERT INTO USER (U_EMAIL, U_NAME, U_ACCOUNT, U_DATE) VALUES (:email, :name, :account, NOW())";
        $insertStmt = $pdo->prepare($insertSql);
        $insertStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $insertStmt->bindParam(':name', $name, PDO::PARAM_STR);
        $insertStmt->bindParam(':account', $account, PDO::PARAM_STR);
        $insertStmt->execute();
        
        // Get the new user's ID
        $newUserId = $pdo->lastInsertId();

        // 獲取新用戶的詳細信息
        $newUserSql = "SELECT * FROM USER WHERE U_ID = :id";
        $newUserStmt = $pdo->prepare($newUserSql);
        $newUserStmt->bindParam(':id', $newUserId, PDO::PARAM_INT);
        $newUserStmt->execute();
        $newUser = $newUserStmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'message' => '註冊成功並登入！', 'user' => $newUser]);
    }
} catch (PDOException $e) {
    // Return error response
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
