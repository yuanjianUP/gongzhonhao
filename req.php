<?php
/**
 * Created by PhpStorm.
 * User: jian
 * Date: 2019/8/24
 * Time: 23:10
 */
class Requset
{
    /**
     * 没有传data则为get方法
     * @param $url
     * @param $data
     * @param string $file 文件路径
     */
    public function http_request($url,$data='',$file=''){
        if(!empty($file)){
            //必须用media标识
            $data['media'] = new CURLFile($file);
        }
        $ch = curl_init();
        //相关配置
        //设置请求的url
        curl_setopt($ch,CURLOPT_URL,$url);
        //设置请求头
        curl_setopt($ch,CURLOPT_HEADER,0);
        //请求的得到的结果不直接输出，而是以字符串返回 0为默认1是不直接输出
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //设置超时
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        //设置浏览器型号
        curl_setopt($ch,CURLOPT_USERAGENT,'MSIE001');
        if($data){
            //设置Post请求
            curl_setopt($ch,CURLOPT_POST,1);
            //请求的数据
            curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        }
        //证书不检查
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

        //发起请求
        $data = curl_exec($ch);

        //如果有错误则输出错误
        if(curl_errno($ch)>0){
            echo curl_errno($ch);
            $data = '';

        }
        //关闭资源
        curl_close($ch);
        return $data;
    }
}