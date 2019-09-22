<?php
#网页授权获取用户信息
$appid = 'wxe6946ae02e57ad73';
$codeUrl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_userinfo&state=100#wechat_redirect";
$rUrl = urlencode('http://7pasus.natappfree.cc/shop.php');
$url = sprintf($codeUrl,$appid,$rUrl);

header('location:'.$url);
