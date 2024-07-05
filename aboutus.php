<?php
// 允許所有來源訪問這個API，設置CORS頭
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 連接到MySQL數據庫
$conn = mysqli_connect("localhost", "root", "", "ba");

// 檢查數據庫連接是否成功
if ($conn->connect_error) {
    die("連接失敗: " . $conn->connect_error);
}

// 設置數據庫字符集為UTF-8
mysqli_set_charset($conn, "utf8");

// 從knowledge表中選擇所有數據
$sql = "SELECT * FROM INQUIRY";
$result = mysqli_query($conn, $sql);

// 初始化一個空數組來存儲數據
$data = array();
if (mysqli_num_rows($result) > 0) {
    // 遍歷結果集並將每一行添加到數組中
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

// 設置響應頭為JSON格式
header('Content-Type: application/json');
// 將數據數組編碼為JSON格式並輸出
echo json_encode($data);

// 關閉數據庫連接
mysqli_close($conn);
?>