<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php"); 

    $sql = "select * from FEEDBACK"; 
    $FEEDBACK = $pdo->query($sql); 
    $prodRows = $FEEDBACK->fetchAll(PDO::FETCH_ASSOC);
    
    $countSql = "SELECT COUNT(*) AS count FROM FEEDBACK";
    $countResult = $pdo->query($countSql);
    $countRow = $countResult->fetch(PDO::FETCH_ASSOC);
    $FEEDBACKCount = $countRow['count'];

    $result = ["error" => false, "msg" => "", "FEEDBACK" => $prodRows, "FEEDBACKCount" => $FEEDBACKCount]; 
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()]; 
}

echo json_encode($result, JSON_NUMERIC_CHECK); 

?>
