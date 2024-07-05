<?php
header('Content-Type: application/json');

try {
    require_once("config.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 解析 JSON 請求內容
        $data = json_decode(file_get_contents('php://input'), true);

        // 檢查所有必要的參數是否存在
        if (isset($data['U_NAME'], $data['U_ACCOUNT'], $data['U_EMAIL'], $data['U_PHONE'], $data['U_ADDRESS'])) {
            // 獲取會員資料
            $name = $data['U_NAME'];
            $account = $data['U_ACCOUNT'];
            $email = $data['U_EMAIL'];
            $phone = $data['U_PHONE'];
            $address = $data['U_ADDRESS'];

            // 開始事務
            $pdo->beginTransaction();

            // 準備更新語句
            $stmt = $pdo->prepare("UPDATE USER SET U_NAME = ?, U_EMAIL = ?, U_PHONE = ?, U_ADDRESS = ? WHERE U_ACCOUNT = ?");
            $stmt->execute([$name, $email, $phone, $address, $account]);

            // 提交事務
            $pdo->commit();

            echo json_encode(["status" => "success", "message" => "會員資料更新成功"]);
        } else {
            echo json_encode(["status" => "error", "message" => "缺少必要的參數"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "無效的請求方法"]);
    }
} catch (PDOException $e) {
    // 回滾事務
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(["status" => "error", "message" => "更新會員資料時出錯：" . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
