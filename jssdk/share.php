<?php
    include '../WeChat.php';
    $wx = new wechat();
    $data = $wx->getJsdkSign();
    $url = 'http://'.$_SERVER['HTTP_HOST'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>分享页面</title>
</head>
<script src="jssdk.js"></script>
<body>
  <img src="<?php echo $url;?>/qrcode.jpg" alt="" style='with:60%'>  
<script>
    wx.config({
        debug: false, // 开启调试模式
        appId: '<?php echo $data['appId']; ?>', // 必填，公众号的唯一标识
        timestamp: '<?php echo $data['timestamp']; ?>', // 必填，生成签名的时间戳
        nonceStr: '<?php echo $data['nonceStr']; ?>', // 必填，生成签名的随机串
        signature: '<?php echo $data['signature']; ?>',// 必填，签名
        jsApiList: [
            '',

        ] // 必填，需要使用的JS接口列表
    });
    wx.ready(function(){
        wx.updateTimelineShareData({ 
            title: '分享二维码', // 分享标题
            link: '<?php echo $url; ?>/jssdk/share.php', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: '<?php echo $url;?>/qrcode.jpg', // 分享图标
            success: function () {
            // 设置成功
                console.log('成功');
            }
        })
    });
</script>
</body>
</html>