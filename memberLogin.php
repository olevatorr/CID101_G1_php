<?php

// 引入數據庫配置文件
require_once("config.php");


try {
    // 獲取並解碼 JSON 請求數據
    $data = json_decode(file_get_contents('php://input'), true);
    
    
    if (!isset($data["username"]) || !isset($data['password'])) {
        throw new Exception('Username and password are required');
    }
    $username = $data['username'];
    $password = $data['password'];

    // 準備 SQL 語句，從數據庫中查找用戶
    $sql = "SELECT * FROM USER WHERE U_ACCOUNT = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // 獲取查詢結果
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // 檢查用戶是否存在並驗證密碼
    if ($userRow) {
        echo json_encode(['error' => false, 'msg' => '登錄成功', 'user' => $userRow]);
    } else {
        echo json_encode(['error' => true, 'msg' => '用戶名或密碼錯誤']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => true, 'msg' => '數據庫錯誤: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'msg' => $e->getMessage()]);
}


?>
