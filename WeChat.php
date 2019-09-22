<?php
/**
 * Created by PhpStorm.
 * User: jian
 * Date: 2019/8/25
 * Time: 15:12
 */
include 'req.php';
class WeChat
{
    const APPID = 'wxe6946ae02e57ad73';
    const SECRET = '590e8f9cbfa1efd45f8b4abf911d25c4';
    public function getAccessToken(){
        //缓存文件
        $cacheFile = self::APPID.'_cache.log';
        if(is_file($cacheFile) && filemtime($cacheFile)+7000 > time()){
            return file_get_contents($cacheFile);
        }
        //第一次请求时
        //可看微信文档
        $surl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s";
        $url = sprintf($surl,self::APPID,self::SECRET);
        $request = new Requset();
        $json = $request -> http_request($url);
        $arr = json_decode($json,true);
        $access_token = $arr['access_token'];
        //写缓存
        file_put_contents($cacheFile,$access_token);
        return $access_token;
    }

    /**
     * 创建菜单
     * @param array $menu
     * @return mixed|string
     */
    public function createMenue(array $menu){
        //中文需要设置
        $menu = json_encode($menu,256);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->getAccessToken();
        $request = new Requset();
        $re = $request -> http_request($url,$menu);
        return $re;
    }

    //上传素材
    /**
     * @param string $path
     * @param string $type
     * @return mixed|string
     */
    public function uploadMaterial(string $path,string $type = 'image',$is_forever = '0'){
        if($is_forever == 0){
            $surl = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=%s&type=%s";
        }else{
            $surl = "https://api.weixin.qq.com/cgi-bin/material/add_news?access_token=%s";
        }
        $url = sprintf($surl,$this->getAccessToken(),$type);
        $req = new Requset();
        $json = $req -> http_request($url,'',$path);
        $data = json_decode($json,true);
        return $data['media_id'];
    }

    /**
     * 客服
     * @param $openid
     * @param $msg
     */
    public function kefuMsg($openid,$msg){
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->getAccessToken();
        $request = new Requset();
        $data = '{
            "touser":"'.$openid.'",
            "msgtype":"text",
            "text":
            {
                 "content":"'.$msg.'"
            }
        }';
        $json = $request->http_request($url,$data);
        return $json;
    }
    /**
     * 生成永久和零时二维码
     *
     * @param integer $flag 0临时1永久
     * @param integer $id
     * @return void
     */
    public function createQrcode(int $flag = 0,int $id = 1){
        //第一步获取ticket
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->getAccessToken();
        if($flag === 0){
            $data = '{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
        }else{
            $data = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$id.'}}}';
        }
        $request = new Requset();
        $json = $request->http_request($url,$data);
        $arr = json_decode($json,true);
        $ticket = $arr['ticket'];
        //通过ticket换取二维码
        //提醒：TICKET记得进行UrlEncode
        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
        $img = $request->http_request($url);
        file_put_contents('qrcode.jpg',$img);
        return 'qrcode.jpg';
    }
    /**
     * 获取随机数
     *
     * @return void
     */
    public function getNoncestr(){
        $str = 'abcdksfamdgadafdfadf;';
        $str = md5($str);
        $str = str_shuffle($str);
        return substr($str,0,15);
    }
    /**
     * 获取jsdk签名
     *
     * @return void
     */
    public function getJsdkSign(){
        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token='.$access_token;
        $request = new Requset();
        $result = $request->http_request($url);
        $result = json_decode($result,true);
        $noncestr = $this->getNoncestr();
        $jsapi_ticket = $result['ticket'];
        $timestamp = time();
        if(empty($_SERVER['QUERY_STRING'])){
            $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        }else{
            $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        }
        $str = "jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s";
        $str = sprintf($str,$jsapi_ticket,$noncestr,$timestamp,$url);
        $sign = sha1($str);
        return [
            'appId'=>self::APPID,
            'timestamp'=>$timestamp,
            'nonceStr'=>$noncestr,
            'signature'=>$sign,
        ];
    }
}
// $data = new WeChat();
// $data->getJsdkSign();
// $img = $data->createQrcode();
// echo '<img src="'.$img.'">';
////$data -> getAccessToken();
// $menuList = include './menu.php';
// echo $data -> createMenue($menuList);