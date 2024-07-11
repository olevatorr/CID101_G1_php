<?php
try {
    require_once("config.php"); 

    $sql = "
    SELECT 
        po.PO_ID, po.PO_NAME, po.PO_ADDR, po.PO_PHONE, po.PO_DATE, po.PO_AMOUNT, po.PM_ID, po.PO_TRANS, po.S_STATUS,
        pod.P_NAME, pod.P_PRICE, pod.PO_QTY
    FROM 
        PRODUCT_ORDER po
    JOIN 
        PRODUCT_ORDER_DETAILS pod 
    ON 
        po.PO_ID = pod.PO_ID";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $prodRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = ["error" => false, "msg" => "", "productOrder" => $prodRows];
} catch (PDOException $e) {
    $result = ["error" => true, "msg" => $e->getMessage()];
}

echo json_encode($result, JSON_NUMERIC_CHECK);

?>
