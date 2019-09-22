<?php
    include './WeChat.php';
    if(!empty($_POST['msg'])){
        $openid = $_POST['openid'];
        $data = $_POST['msg'];
        echo (new WeChat())->kefuMsg($openid,$data);
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>客服借口</title>
</head>
<body>
<form action="" method="post">
    <input type="hidden" name="openid" value="ouVdduPkB4F_t4mjdyRH1hA5jIVQ">
    <input type="text" name="msg">
    <input type="submit" value="发送">
</form>
</body>
</html>