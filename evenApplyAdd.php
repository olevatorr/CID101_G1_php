<?php
// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("config.php");

// 定義上傳目錄的相對路徑
// $uploadRelativeDir = '/cid101/g1/upload/img/events/';


try {
    // 開始事務
    $data = json_decode(file_get_contents('php://input'), true);
    $pdo->beginTransaction();

    // 準備 SQL 語句，將知識數據插入到數據庫中
    $sql = "INSERT INTO EVENT_ORDER (E_ID, U_ID, EO_ATTEND) 
                VALUES (:E_ID, :U_ID, :EO_ATTEND)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(":E_ID", $data["E_ID"]); // 綁定 E_ID 參數
    $stmt->bindValue(":U_ID", $data["U_ID"]); // 綁定 U_ID 參數
    $stmt->bindValue(":EO_ATTEND", $data["EO_ATTEND"]); // 綁定 EO_ATTEND 參數
    $stmt->execute();

    $sql2 = "UPDATE EVENTS SET E_SIGN_UP = E_SIGN_UP + :NUM
                WHERE E_ID = :E_ID";

    $stmt2 = $pdo->prepare($sql2);
    $stmt2->bindValue(":E_ID", $data["E_ID"]); // 綁定 E_ID 參數
    $stmt2->bindValue(":NUM", $data["EO_ATTEND"]); // 綁定 EO_attend 參數
    $stmt2->execute();

    // 提交事務
    $pdo->commit();

    echo json_encode(['error' => false, 'msg' => '新增資料成功', 'E_ID' => $E_ID]);

} catch (PDOException $e) {
    // 回滾事務
    $pdo->rollBack();
    echo json_encode(['error' => true, 'msg' => '數據庫錯誤: ' . $e->getMessage()]);
} catch (Exception $e) {
    // 回滾事務
    $pdo->rollBack();
    echo json_encode(['error' => true, 'msg' => $e->getMessage()]);
}
?>

<?php

//  ----------------純文字資料版本如下
// 0777?
// 0: 沒有任何權限
// 1: 執行權限
// 2: 寫入權限
// 3: 執行和寫入權限
// 4: 讀取權限
// 5: 讀取和執行權限
// 6: 讀取和寫入權限
// 7: 讀取、寫入和執行權限
// try {
//     require_once("config.php"); // 引入資料庫配置文件

//     // 從請求中讀取 JSON 輸入數據
//     $data = json_decode(file_get_contents('php://input'), true);

//     // 檢查是否存在必要的 POST 數據
//     if (isset($data["K_TITLE"]) && isset($data["K_CONTENT"]) && isset($data["K_FROM"]) && isset($data["K_URL"]) && isset($data["K_DATE"])) {
//         // 準備 SQL 語句，將知識數據插入到數據庫中
//         $sql = "INSERT INTO KNOWLEDGE (K_TITLE, K_CONTENT, K_FROM, K_URL, K_DATE) 
//                 VALUES (:K_TITLE, :K_CONTENT, :K_FROM, :K_URL, :K_DATE)";

//         $knowledgeStmt = $pdo->prepare($sql); // 準備 SQL 語句
//         $knowledgeStmt->bindValue(":K_TITLE", $data["K_TITLE"]); // 綁定 K_TITLE 參數
//         $knowledgeStmt->bindValue(":K_CONTENT", $data["K_CONTENT"]); // 綁定 K_CONTENT 參數
//         $knowledgeStmt->bindValue(":K_FROM", $data["K_FROM"]); // 綁定 K_FROM 參數
//         $knowledgeStmt->bindValue(":K_URL", $data["K_URL"]); // 綁定 K_URL 參數
//         $knowledgeStmt->bindValue(":K_DATE", $data["K_DATE"]); // 綁定 K_DATE 參數
//         $knowledgeStmt->execute(); // 執行 SQL 語句
//         $knowledge = $data; // 設置響應數據

//         // 返回成功的 JSON 響應
//         echo json_encode(["error" => false, "msg" => "新增資料成功", "knowledge" => $knowledge]);
//     } else {
//         // 返回缺少必要 POST 數據的 JSON 響應
//         echo json_encode(["error" => true, "msg" => "缺少必要的POST數據"]);
//     }
// } catch (PDOException $e) {
//     // 處理 PDO 異常，並返回 JSON 響應
//     $result = ["error" => true, "msg" => $e->getMessage()];
//     echo json_encode($result);
// }
// ?>

