<?php
include 'req.php';
$appid = 'wxe6946ae02e57ad73';
$appsecret = '590e8f9cbfa1efd45f8b4abf911d25c4';
$code = $_GET['code'];
$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';
$url = sprintf($url,$appid,$appsecret,$code);
$request = new Requset();
#获取accesstoken openid
$two = $request->http_request($url);
$two = json_decode($two,true);
#拉取用户信息
$url = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN';
$url = sprintf($url,$two['access_token'],$two['openid']);
$three = $request->http_request($url);
$three = json_decode($three,true);
var_dump($three);


