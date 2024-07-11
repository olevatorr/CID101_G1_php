<?php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once("config.php");

    $sql = "
    SELECT 
        FEEDBACK.*, 
        EVENTS.E_TITLE,
        EVENTS.E_ADDRESS, 
        EVENTS.E_DATE, 
        USER.U_NAME
    FROM 
        FEEDBACK
    JOIN 
        EVENTS ON FEEDBACK.E_ID = EVENTS.E_ID
    JOIN 
        USER ON FEEDBACK.U_ID = USER.U_ID
    ORDER BY 
        F_DATE DESC
        ";

    $stmt = $pdo->query($sql);
    $feedbackRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countSql = "SELECT COUNT(*) AS count FROM FEEDBACK";
    $countStmt = $pdo->query($countSql);
    $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
    $feedbackCount = $countRow['count'];

    $result = ["error" => false, "msg" => "", "FEEDBACK" => $feedbackRows, "FEEDBACKCount" => $feedbackCount];
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()];
}

echo json_encode($result, JSON_NUMERIC_CHECK);
