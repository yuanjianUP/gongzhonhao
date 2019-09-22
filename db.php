<?php
/**
 * Created by PhpStorm.
 * User: jian
 * Date: 2019/8/26
 * Time: 10:41
 */
return new PDO("mysql:host=127.0.0.1;dbname=wxgzh",'root','root',[
    //有错误抛异常
    PDO::ERRMODE_EXCEPTION,
    //以关联数组的形式输出
    PDO::FETCH_ASSOC,
]);