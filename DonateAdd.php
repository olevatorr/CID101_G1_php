<?php
// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

// DonateAdd.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 直接读取原始的 POST 数据
    $raw_post_data = file_get_contents('php://input');
    parse_str($raw_post_data, $post_data);

    $do_amount = filter_var($post_data['DO_AMOUNT'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $u_id = filter_var($post_data['U_ID'], FILTER_SANITIZE_NUMBER_INT);

    // 調試信息
    $debug_info = [
        'do_amount' => $do_amount,
        'do_date' => $do_date,
        'u_id' => $u_id,
    ];

    // 檢查必要參數是否存在
    if ($do_amount && $u_id) {
        // 插入数据
        $sql = "INSERT INTO DONATE_ORDER (DO_AMOUNT, U_ID) VALUES (:DO_AMOUNT, :U_ID)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':DO_AMOUNT', $do_amount);
        $stmt->bindParam(':U_ID', $u_id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => '捐款資料已儲存']);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['message' => '捐款資料儲存失敗', 'error' => $errorInfo[2], 'debug' => $debug_info]);
        }
    } else {
        echo json_encode(['message' => '缺少必要的參數', 'debug' => $debug_info]);
    }
}
?>
