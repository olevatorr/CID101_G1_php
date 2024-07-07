<?php
header('Content-Type: application/json');

// 定義上傳目錄的相對路徑
$uploadRelativeDir = '/cid101/g1/upload/';

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
        $dd_cleaning_scope = floatval(str_replace(',', '', $item['清理範圍(處)']));
        $dd_cleaning_times = floatval(str_replace(',', '', $item['清理次數(次)']));
        $dd_attendance_total = floatval(str_replace(',', '', $item['參與人數(人次)']));
        $dd_sea_drift = floatval(str_replace(',', '', $item['海洋廢棄物來源(噸)_海漂']));
        $dd_sea_bottom = floatval(str_replace(',', '', $item['海洋廢棄物來源(噸)_海底']));
        $dd_beach_cleaning = floatval(str_replace(',', '', $item['海洋廢棄物來源(噸)_淨灘']));
        $dd_ship_crew = floatval(str_replace(',', '', $item['海洋廢棄物來源(噸)_船舶人員產出']));
        $dd_shore_trash_cans = floatval(str_replace(',', '', $item['海洋廢棄物來源(噸)_岸上定點設置垃圾桶']));
        $dd_employees = floatval(str_replace(',', '', $item['清理方式_雇工(人次)']));
        $dd_machinery = floatval(str_replace(',', '', $item['清理方式_機械(輛次)']));
        $dd_total_cleared_tons = floatval(str_replace(',', '', $item['清理數量分類(噸)_總計']));
        $dd_bottle = floatval(str_replace(',', '', $item['清理數量分類(噸)_寶特瓶']));
        $dd_iron_can = floatval(str_replace(',', '', $item['清理數量分類(噸)_鐵罐']));
        $dd_aluminum_can = floatval(str_replace(',', '', $item['清理數量分類(噸)_鋁罐']));
        $dd_glass = floatval(str_replace(',', '', $item['清理數量分類(噸)_玻璃瓶']));
        $dd_paper = floatval(str_replace(',', '', $item['清理數量分類(噸)_廢紙']));
        $dd_wood = floatval(str_replace(',', '', $item['清理數量分類(噸)_竹木']));
        $dd_polystyrene = floatval(str_replace(',', '', $item['清理數量分類(噸)_保麗龍']));
        $dd_fishing_gear = floatval(str_replace(',', '', $item['清理數量分類(噸)_廢漁具漁網']));
        $dd_unclassified_waste = floatval(str_replace(',', '', $item['清理數量分類(噸)_無法分類廢棄物']));
        $dd_incineration = floatval(str_replace(',', '', $item['清理後處理方式(噸)_焚化']));
        $dd_recycle = floatval(str_replace(',', '', $item['清理後處理方式(噸)_回收再利用']));
        $dd_landfill = floatval(str_replace(',', '', $item['清理後處理方式(噸)_掩埋']));


        $sql = "
                INSERT INTO DEBRIS_DATA (
                    DDL_ID,
                    DD_AREA, 
                    DD_CLEANING_SCOPE,
                    DD_CLEANING_TIMES, 
                    DD_ATTENDANCE_TOTAL,
                    DD_SEA_DRIFT,
                    DD_SEA_BOTTOM,
                    DD_BEACH_CLEANING,
                    DD_SHIP_CREW,
                    DD_SHORE_TRASH_CANS,
                    DD_EMPLOYEES,
                    DD_MACHINERY,
                    DD_CLEARED_TOTAL_TONS, 
                    DD_RECYCLE, 
                    DD_INCINERATION,
                    DD_LANDFILL, 
                    DD_BOTTLE, 
                    DD_IRON_CAN,
                    DD_ALUMINUM_CAN, 
                    DD_GLASS, 
                    DD_PAPER,
                    DD_WOOD,
                    DD_POLYSTYRENE,
                    DD_FISHING_GEAR,
                    DD_UNCLASSIFIED_WASTE
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $ddl_id,
            $dd_area,
            $dd_cleaning_scope,
            $dd_cleaning_times,
            $dd_attendance_total,
            $dd_sea_drift,
            $dd_sea_bottom,
            $dd_beach_cleaning,
            $dd_ship_crew,
            $dd_shore_trash_cans,
            $dd_employees,
            $dd_machinery,
            $dd_total_cleared_tons,
            $dd_recycle,
            $dd_incineration,
            $dd_landfill,
            $dd_bottle,
            $dd_iron_can,
            $dd_aluminum_can,
            $dd_glass,
            $dd_paper,
            $dd_wood,
            $dd_polystyrene,
            $dd_fishing_gear,
            $dd_unclassified_waste
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
