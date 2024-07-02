<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>綠界交易測試</title>
</head>
<body>
    <h1>綠界交易測試</h1>
    <form action="ecpay_test.php" method="post">
        <label for="itemName">商品名稱：</label>
        <input type="text" id="itemName" name="itemName" value="測試商品" required><br><br>
        <label for="itemPrice">商品價格：</label>
        <input type="number" id="itemPrice" name="itemPrice" value="1000" required><br><br>
        <label for="itemQuantity">數量：</label>
        <input type="number" id="itemQuantity" name="itemQuantity" value="1" required><br><br>
        <button type="submit">提交交易</button>
    </form>
</body>
</html>
