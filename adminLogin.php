<?php
header('Content-Type: application/json');


try {
    require_once("config.php");
    $data = json_decode(file_get_contents('php://input'), true);

    $admin_account = $data['acc'];
    $admin_password = $data['psw'];



    $sql = "SELECT * FROM ADMIN WHERE AD_ACCOUNT = :AD_ACCOUNT AND AD_PSW = :AD_PSW";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':AD_ACCOUNT', $admin_account);
    $stmt->bindValue(':AD_PSW', $admin_password);
    $stmt->execute();
    $admin_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin_data) {
        $result = ["error" => false, "msg" => "登錄成功", "admin" => $admin_data];
    } else {
        $result = ["error" => true, "msg" => "帳號或密碼錯誤"];
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $result = ["error" => true, "msg" => "數據庫錯誤，請聯繫管理員"];
} catch (Exception $e) {
    $result = ["error" => true, "msg" => $e->getMessage()];
}

echo json_encode($result, JSON_NUMERIC_CHECK);
?>