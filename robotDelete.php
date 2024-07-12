<?php
header('Content-Type: application/json');
try {
    // 引入資料庫連接配置文件
        require_once("config.php");
    
        // 定義 SQL 刪除語句，用於刪除 `knowledge` 表中指定 `K_ID` 的記錄
        $sql = "delete from ROBOT_QA where R_ID = ?";
    
        // 使用 PDO 對象準備 SQL 語句
        $robot = $pdo->prepare($sql);
    
        // 綁定 GET 請求中的 `K_ID` 參數到 SQL 語句中的第一個問號佔位符
        $robot->bindValue(1, $_GET["R_ID"]);
    
        // 執行 SQL 語句
        $robot->execute();
    
        // 獲取受影響的行數
        $affectedCount = $robot->rowCount();
    
        // 創建結果數組，包含操作是否成功的標誌和影響的行數
        $result = ["error" => false, "msg" => "成功的影響{$affectedCount}筆"];
    } catch (PDOException $e) {
        // 如果發生例外，創建結果數組，包含錯誤標誌和錯誤信息
        $result = ["error" => true, "msg" => $e->getMessage()];
    }
    
    // 將結果數組編碼為 JSON 格式並輸出
    echo json_encode($result, JSON_NUMERIC_CHECK);
?>