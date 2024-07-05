<?php

try {
    require_once("config.php");

    $data = json_decode(file_get_contents('php://input'));

    if (
        isset($data->account) &&
        isset($data->name) &&
        isset($data->password) &&
        isset($data->phone) &&
        isset($data->email) &&
        isset($data->address)
    ) {
        $account = $data->account;
        $name = $data->name;
        $password = $data->password;
        $phone = $data->phone;
        $email = $data->email;
        $address = $data->address;

        $sql = "INSERT INTO USER (U_ACCOUNT, U_NAME, U_PSW, U_PHONE, U_EMAIL, U_ADDRESS) VALUES (:account, :name, :password, :phone, :email, :address)";
        $stmt = $pdo->prepare($sql);

        // 使用 bindValue 方法綁定參數
        $stmt->bindValue(':account', $account);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':address', $address);

        if ($stmt->execute()) {
            echo json_encode(["message" => "註冊成功"]);
        } else {
            echo json_encode(["message" => "註冊失敗"]);
        }
    } else {
        echo json_encode(["message" => "無效的輸入"]);
    }
} catch (Exception $e) {
    echo json_encode(['code' => 0, 'message' => $e->getMessage()]);
}
?>
