<?php
//--------------------開發階段
$allowed_origins = [
    "http://localhost:5173",//前台或後台網址
    "http://localhost:5174",
    "http://localhost:5175",
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
}
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
//---
$dbname = "bluealert";
$user = "root";
$password = "";

//--------------------prod階段
// $dbname = "tibamefe_cid101g1";
// $user = "tibamefe_since2021";
// $password = "vwRBSb.j&K#E";

$dsn = "mysql:host=localhost;port=3306;dbname=$dbname;charset=utf8";

$options = array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE=>PDO::CASE_NATURAL);

//建立pdo物件
$pdo = new PDO($dsn, $user, $password, $options);	
?>