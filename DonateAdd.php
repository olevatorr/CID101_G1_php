<?php
// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

// DonateAdd.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $do_amount = filter_input(INPUT_POST, 'DO_AMOUNT', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $do_date = filter_input(INPUT_POST, 'DO_DATE', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $u_id = filter_input(INPUT_POST, 'U_ID', FILTER_SANITIZE_NUMBER_INT);

    // 檢查必要參數是否存在
    if ($do_amount && $do_date && $u_id) {
        // 插入数据
        $sql = "INSERT INTO DONATE_ORDER (DO_AMOUNT, DO_DATE, U_ID) VALUES (:DO_AMOUNT, :DO_DATE, :U_ID)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':DO_AMOUNT', $do_amount);
        $stmt->bindParam(':DO_DATE', $do_date);
        $stmt->bindParam(':U_ID', $u_id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => '捐款資料已儲存']);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['message' => '捐款資料儲存失敗', 'error' => $errorInfo[2]]);
        }
    } else {
        echo json_encode(['message' => '缺少必要的參數']);
    }
}
?>
