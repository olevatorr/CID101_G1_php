<?php
header("Access-Control-Allow-Origin:*");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {

    require_once("bluealertKey.php");
    $sql = "insert into emp (K_TITLE, K_CONTENT, K_FROM, K_URL, K_DATE) 
    values (:K_TITLE, :K_CONTENT, :K_FROM, :K_URL, :K_DATE)";
    $empStmt = $pdo->prepare( $sql );
    $empStmt->bindValue(":K_TITLE", $_POST["K_TITLE"]);
    $empStmt->bindValue(":K_CONTENT", $_POST["K_CONTENT"]);
    $empStmt->bindValue(":K_FROM", $_POST["K_FROM"]);
    $empStmt->bindValue(":K_URL", $_POST["K_URL"]);
    $empStmt->bindValue(":K_DATE", $_POST["K_DATE"]);
    $empStmt->execute();
    $emp = $_POST;

    echo json_encode(["error" => false, "msg" => "新增資料成功", "emp" => $emp]);
} catch (PDOException $e) {
	$result = ["error" => true, "msg" => $e->getMessage()];
	echo json_encode($result);
}
?>
