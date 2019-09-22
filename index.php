<?php
/**
* 接入效验
*/

include 'req.php';
class Wx
{
    const TOKEN = 'weixin';
    private $pdo;
	function __construct()
	{
		if(!empty($_GET['echostr'])){
			echo $this -> checkSing();
		}else{
            $this->pdo = include 'db.php';
		    $this -> acceptMsg();
        }
	}
	 private function checkSing()
	{
		$input = $_GET;
		//
		$echostr = $input['echostr'];
		$signature = $input['signature'];
		unset($input['echostr'],$input['signature']);
		$input['token'] = Self::TOKEN;
		sort($input, SORT_STRING);
		$tmpStr = implode( $input );
		$tmpStr = sha1( $tmpStr );
		if($tmpStr == $signature){
			return $echostr;
		}
		return '';
	}

    /**
     * 根据不同的条件出发不同的事件
     */
	private function acceptMsg(){
		$xml = file_get_contents('php://input');
		$obj = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
        $MsgType = $obj -> MsgType;
        $fun = $MsgType.'Fun';
        echo $ret = $this -> $fun($obj);
        //写发送日志
        $this -> writeLog($obj,2);
	}

    /**
     * 菜单事件回复
     * @param $obj
     */
	private function eventFun($obj){
	    $event = strtolower($obj -> Event);
	    $fun = $event.'Fun';
	    return $this -> $fun($obj);
    }
    /**
     * 点击事件
     *
     * @param [type] $obj
     * @return void
     */
    private function clickFun($obj){
	    $eventKey = $obj -> EventKey;
        if($eventKey == 'V1001_TODAY_MUSIC'){
            return $this -> createText($obj,'你点击的是今日歌曲');
        }elseif ($eventKey == 'V1001_GOOD'){
            return $this -> createText($obj,'你点击的是赞');
        }
    }
    /**
     * 扫码关注事件
     *
     * @param [type] $obj
     * @return void
     */
    private function subscribeFun($obj){
        $eventKey = $obj->EventKey;
        $eventKey = (string)$eventKey;
        if(empty($eventKey)){//顶级用户
            $sql = "insert into user (openid) values (?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$obj->FromUserName]);
        }else{
            $id = (string)str_replace('qrscene_','',$eventKey);
            $sql = 'select * from user where id='.$id;
            $row = $this->pdo->query($sql)->fetch();
            #添加的本人记录
            $sql = "insert into user (openid,f1,f2,f3) values (?,?,?,?)";
            $stmt = $this->pdo->prepare($sql);
            $openid = $obj->FromUserName;
            $stmt->execute([$openid,$row['openid'],$row['f1'],$row['f2']]);
        }
        return $this->createText($obj,'欢迎关注我们');
    }

    /**
     * 位置坐标入库
     * @param $obj
     */
    private function locationFun($obj){
        $openid = $obj->FromUserName;
        $longitude = $obj->Longitude;
        $latitude = $obj->Latitude;
        $sql = "update user set latitude=?,longitude=? where openid=?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$latitude,$longitude,$openid]);
    }
    /**
     * 语音识别
     * @param $obj
     * @return string
     */
    private function voiceFun($obj){
        $content = (string)$obj->Recognition;
        $content = !empty($content) ? $content : '未能识别';
        return $this->createText($obj,$content);
    }

    /**
     * 文本事件
     *
     * @param [type] $obj
     * @return void
     */
	private function textFun($obj){
        $content = $obj->Content;
        //回复文本判断
        if($content == '音乐'){
            return $this -> musicFun($obj);
        }
        if(strstr($content,'位置-')){
            $openid = $obj->FromUserName;
            $key = '163f5eb0b01ecf361c8d2ffad4c8057e';

            $sql = "select longitude,latitude from user where openid=?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$openid]);
            $data = $stmt->fetch();
            $kw = str_replace('位置-','',$content);
            $kw = trim($kw);

            //types050000（餐饮服务）
            $url = "http://restapi.amap.com/v3/place/around?key=%s&location=%s,%s&keywords=%s&types=050000&radius=1000&offset=20&page=1&extensions=all";
            $url = sprintf($url,$key,$data['longitude'],$data['latitude'],$kw);
            $request = new Requset();
            $result = $request->http_request($url);
            $result = json_decode($result,true);
            if(count($result['pois']) === 0){
                $content = '没有找到相关服务';
            }else{
                $content = "🚩🚩🚩🚩🚩🚩🚩🚩🚩\n";
                $content .= "名称:".$result['pois'][0]['name']."\n";
                $content .= "地址:".$result['pois'][0]['address']."\n";
                $content .= "距离:".$result['pois'][0]['distance']."米\n";
                $content .= "🚩🚩🚩🚩🚩🚩🚩🚩🚩";
            }

        }
        return $this->createText($obj,$content);
    }

    private function imageFun($obj){
        $mediaid = $obj->MediaId;
        return $this -> createImage($obj,$mediaid);
    }

    private function musicFun($obj){
	    //图片媒体图片
	    $mediaid = 'S8Ie7XMfLqbCFYrdp9pZsUXCg-qxDv5FiBV8MEW0uyxTH8-a1wPKMf5jk3NXHP46';
	    $url = "https://wx.1314000.cn/mp3/ykz.mp3";
	    return $this -> createMusic($obj,$url,$mediaid);

    }

    private function createMusic($obj,$url,$mediaid){
        $str = '
        <xml>
          <ToUserName><![CDATA[%s]]></ToUserName>
          <FromUserName><![CDATA[%s]]></FromUserName>
          <CreateTime>%s</CreateTime>
          <MsgType><![CDATA[music]]></MsgType>
          <Music>
            <Title><![CDATA[夜空中最亮的星]]></Title>
            <Description><![CDATA[一首歌]]></Description>
            <MusicUrl><![CDATA[%s]]></MusicUrl>
            <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
            <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
          </Music>
        </xml>';
        $str = sprintf($str,$obj->FromUserName,$obj->ToUserName,time(),$url,$url,$mediaid);
        return $str;
    }

    private function createImage($obj,string $mediaid){
        $str = '<xml>
              <ToUserName><![CDATA[%s]]></ToUserName>
              <FromUserName><![CDATA[%s]]></FromUserName>
              <CreateTime>%s</CreateTime>
              <MsgType><![CDATA[%s]]></MsgType>
              <Image>
                <MediaId><![CDATA[%s]]></MediaId>
              </Image>
            </xml>';
        $str = sprintf($str,$obj->FromUserName,$obj->ToUserName,time(),$obj->MsgType,$mediaid);
        return $str;
    }
    /**
     * @param $obj 生成文本消息
     * @param string $content
     * @return string
     */
    private  function createText($obj,string $content){
        $str = '<xml>
                <ToUserName>
                    <![CDATA[%s]]>
                </ToUserName>
                <FromUserName>
                    <![CDATA[%s]]>
                </FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType>
                    <![CDATA[text]]>
                </MsgType>
                <Content>
                    <![CDATA[%s]]>
                </Content>
            </xml>';
        $str = sprintf($str,$obj->FromUserName,$obj->ToUserName,time(),$content);
        //写日志
        $this -> writeLog($str,2);
        return $str;
    }
    /**
     * @param string $xml 写入的xml
     * @param int $flag 标识 1请求2发送
     *
     */
	private function writeLog(string $xml,int $flag=1){
	    $flagstr = $flag == 1 ? '请求' : '发送';
        $prevstr = '['.$flagstr.']'.date('Y-m-d')."-------------\n";
        $log = $prevstr.$xml."\n--------------\n";
        file_put_contents('wx.xml',$log,FILE_APPEND);
        return true;
	}
}
$wx = new Wx();



?>