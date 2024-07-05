<?php
try {
    require_once("config.php");

    $data = json_decode(file_get_contents('php://input'));

    if (
        isset($data->account) &&
        isset($data->email) &&
        isset($data->newPassword)
    ) {
        $account = $data->account;
        $email = $data->email;
        $newPassword = $data->newPassword;

        // 確認帳號和信箱是否存在
        $sql = "SELECT * FROM USER WHERE U_ACCOUNT = ? AND U_EMAIL = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$account, $email]);

        if ($stmt->rowCount() > 0) {
            // 更新密碼
            $sql = "UPDATE USER SET U_PSW = ? WHERE U_ACCOUNT = ? AND U_EMAIL = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$newPassword, $account, $email])) {
                echo json_encode(["message" => "密碼已重設成功"]);
            } else {
                echo json_encode(["message" => "密碼重設失敗"]);
            }
        } else {
            echo json_encode(["message" => "帳號或信箱不正確"]);
        }
    } else {
        echo json_encode(["message" => "無效的輸入"]);
    }
} catch (Exception $e) {
    echo json_encode(['code' => 0, 'message' => $e->getMessage()]);
}
?>