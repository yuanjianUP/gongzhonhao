<?php
/**
 * Created by PhpStorm.
 * User: jian
 * Date: 2019/8/26
 * Time: 10:32
 */
//上传素材到微信服务器
$pdo = include './db.php';
include './WeChat.php';
$files = $_FILES['pic'];
$ext = pathinfo($files['name'],PATHINFO_EXTENSION);
$name = time().'.'.$ext;
$realpath = __DIR__.'/up/'.$name;
move_uploaded_file($files['tmp_name'],$realpath);
$sql = "insert into material (realpath,ctime,is_forever,media_id) values (?,?,?,?)";
$stmt = $pdo -> prepare($sql);

//上传到公众号平台
$wx = new WeChat();
$media_id = $wx->uploadMaterial($realpath);
var_dump($media_id);

$ret = $stmt -> execute([$realpath,time(),$_POST['is_forever'],$media_id]);