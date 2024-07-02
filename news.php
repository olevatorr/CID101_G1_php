<?php
header("Access-Control-Allow-Origin:*");


//錯誤處理：使用 try-catch 結構來捕獲可能的 PDO 異常。
try {
    require_once("bluealertKey.php");
    $sql = "select * from news";
    //$pdo->query適用於不需要參數綁定的簡單查詢
    $products = $pdo->query($sql);
    // 使用 fetchAll() 方法獲取所有結果行，並以關聯陣列形式儲存
    $prodRows = $products->fetchAll(PDO::FETCH_ASSOC);
    //準備返回資料
    $result = ["error" => false, "msg" => "", "news" => $prodRows];
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()];
}
echo json_encode($result, JSON_NUMERIC_CHECK);//要是數值型別
?>