<?php
header("Access-Control-Allow-Origin:*");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
try {
	require_once("bluealertKey.php");

	$sql = "select * from knowledge";
	$knowledge = $pdo->query($sql);
	$prodRows = $knowledge->fetchAll(PDO::FETCH_ASSOC);
	$result = ["error" => false, "msg" => "", "knowledge" => $prodRows];
} 
catch (PDOException $e) {
	$result = ["error" => true, "msg" => $e->getMessage()];
}
echo json_encode($result, JSON_NUMERIC_CHECK);

?>