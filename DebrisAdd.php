<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 開啟錯誤日誌
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log'); // 請替換為實際的日誌路徑

header('Content-Type: application/json');

// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/json/knowledge/';
// 獲取當前腳本所在的目錄
$currentDir = dirname(__FILE__);
// 構建絕對路徑
$uploadDir = realpath($currentDir . '/../../..') . $uploadRelativeDir;

// 引入數據庫連接配置文件
require_once("config.php");

try {
    // 檢查是否設置了文件和日期
    if (!isset($_FILES['file']) || !isset($_POST['DDL_DATE'])) {
        throw new Exception('缺少必要的參數');
    }

    error_log("接收到的 DDL_DATE: " . $_POST['DDL_DATE']);
    error_log("文件信息: " . print_r($_FILES['file'], true));

    $file = $_FILES['file'];
    $DDL_DATE = $_POST['DDL_DATE'];
    $DDL_DATA_DATE = date('Y-m-d');

    // 檢查文件類型是否為 JSON
    if ($file['type'] !== 'application/json') {
        throw new Exception('文件格式不正確');
    }

    // 生成唯一文件名並移動上傳文件
    $filename = uniqid() . '.json';
    $filePath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('文件上傳失敗');
    }

    // 讀取文件內容
    $data = file_get_contents($filePath);
    $jsonData = json_decode($data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON 解析錯誤: ' . json_last_error_msg());
    }

    // 插入 DEBRIS_DATA_LIST 資料
    $sql = "INSERT INTO DEBRIS_DATA_LIST (DDL_DATE, DDL_DATA_DATE) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$DDL_DATE, $DDL_DATA_DATE]);
    $ddl_id = $pdo->lastInsertId();

    // 解析並插入 DEBRIS_DATA 資料
    foreach ($jsonData as $item) {
        $dd_area = $item['縣市別'];
        $dd_cleaning_times = intval(str_replace(',', '', $item['清理次數(次)']));
        $dd_attendance_total = intval(str_replace(',', '', $item['參與人數(人次)']));
        $dd_cleared_total_tons = floatval(str_replace(',', '', $item['清理數量分類(噸)_總計']));
        $dd_recycle = floatval(str_replace(',', '', $item['清理後處理方式(噸)_回收再利用']));
        $dd_incineration = floatval(str_replace(',', '', $item['清理後處理方式(噸)_焚化']));
        $dd_landfill = floatval(str_replace(',', '', $item['清理後處理方式(噸)_掩埋']));
        $dd_bottle = floatval(str_replace(',', '', $item['清理數量分類(噸)_寶特瓶']));
        $dd_aluminum_can = floatval(str_replace(',', '', $item['清理數量分類(噸)_鋁罐']));
        $dd_glass = floatval(str_replace(',', '', $item['清理數量分類(噸)_玻璃瓶']));
        $dd_fishing_gear = floatval(str_replace(',', '', $item['清理數量分類(噸)_廢漁具漁網']));

        $sql = "
        INSERT INTO DEBRIS_DATA (
            DDL_ID,
            DD_AREA, 
            DD_CLEANING_TIMES, 
            DD_ATTENDANCE_TOTAL,
            DD_CLEARED_TOTAL_TONS, 
            DD_RECYCLE, 
            DD_INCINERATION,
            DD_LANDFILL, 
            DD_BOTTLE, 
            DD_ALUMINUM_CAN, 
            DD_GLASS, 
            DD_FISHING_GEAR
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $ddl_id,
            $dd_area, 
            $dd_cleaning_times, 
            $dd_attendance_total,
            $dd_cleared_total_tons, 
            $dd_recycle, 
            $dd_incineration,
            $dd_landfill, 
            $dd_bottle, 
            $dd_aluminum_can, 
            $dd_glass, 
            $dd_fishing_gear
        ]);
    }

    // 刪除臨時文件
    unlink($filePath);

    echo json_encode(['success' => true, 'message' => '數據上傳成功']);
} catch (Exception $e) {
    error_log('File upload error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => '資料庫錯誤，請聯繫管理員']);
}
?>
