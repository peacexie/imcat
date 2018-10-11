<?php
namespace imcat;
// 用于测试

class wysTester{
    
    public $post = NULL;

    // showUrl
    static function showUrl($url){     
        //$url = urldecode($url);
        $p1 = strpos($url,'redirect_uri='); $p1 = $p1+strlen('redirect_uri=');
        $p2 = strpos($url,'&response_type');
        $url = substr($url,$p1,$p2-$p1);
        $url = str_replace(array("%26","%23","?&",),array("&","#","?",),$url);
        return $url;
    }


    // 组随机测试信息
    static function showInfo($info){     
        //$info = preg_replace("/\s+/", '', $info);
        $info = str_replace("><",">\n<",$info);
        $info = str_replace(array(">\n<![CDATA[","]]>\n</"),array("><![CDATA[","]]></"),$info); 
        $info = str_replace(array("<",">"),array("&lt;","&gt;"),$info);
        $info = preg_replace("/\s(?=\s)/","\\1",$info);
        return "\n$info\n";
    }
    
    // 组随机测试事件
    static function getEvent($ext,$val=''){    
        $post = self::getPost();
        $weMsg = new wmpMsgresp($post,$cfg);
        $data = $weMsg->rebHead('event');
        if($ext=='Subscribe' || $ext=='Unsubscribe'){
            $data .= "<Event><![CDATA[".strtolower($ext)."]]></Event>";
        }elseif($ext=='Scan'){
            $ekey = empty($val) ? mt_rand(100001,2147483123) : $val;
            $data .= "<Event><![CDATA[SCAN]]></Event>";
            $data .= "<EventKey><![CDATA[1".mt_rand(100123,999123)."]]></EventKey>";
            $data .= "<Ticket><![CDATA[Ticket_".basKeyid::kidRand()."]]></Ticket>";
        }elseif($ext=='Location'){ 
            $data .= "<Event><![CDATA[LOCATION]]></Event>";
            $data .= "<Latitude>".(mt_rand(250012,400987)/10000)."</Latitude>";
            $data .= "<Longitude>".(mt_rand(990125,1209876)/10000)."</Longitude>";
            $data .= "<Precision>".mt_rand(80,120)."</Precision>";
        }elseif($ext=='Click'){
            $ekey = empty($val) ? "M".mt_rand(80,120) : $val;
            $data .= "<Event><![CDATA[CLICK]]></Event>";
            $data .= "<EventKey>$ekey</EventKey>";
        }elseif($ext=='View'){
            $data .= "<Event><![CDATA[VIEW]]></Event>";
            $data .= "<EventKey><![CDATA[www.".basKeyid::kidRand().".com]]></EventKey>";
        }else{ 
            
        }
        $data = $weMsg->rebEnd($data); 
        return $data;    
    }    
    
    // 组随机测试信息
    static function getMessage($mtxt=''){    
        $post = self::getPost();
        $weMsg = new wmpMsgresp($post);
        //$func = 'msg'.ucfirst($ext);
        //$parr = array('x'=>mt_rand(250012,400987)/10000, 'y'=>mt_rand(990125,1209876)/10000, 'scale'=>mt_rand(10,15), 'lable'=>'(lable位置信息)', 'title'=>'标题'.basKeyid::kidRand(), 'desc'=>'简述', 'url'=>'http://www.domain-'.basKeyid::kidRand().'.com/');
        $mtxt = empty($mtxt) ? '你好:'.basKeyid::kidRand() : $mtxt; 
        //$para = $func=='remText' ? $mtxt : $parr;
        $data = $weMsg->remText($mtxt);
        return $data;    
    }
    
    // 组随机Post信息
    static function getPost(){
        $from = "from_".basKeyid::kidRand();
        $to = "to_".basKeyid::kidRand();
        $data = "<xml><ToUserName>$to</ToUserName><FromUserName>$from</FromUserName></xml>";
        $post = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        return $post;
    }

    // 组测试signature
    static function getSignurl($wecfg=array(), $apiurl=''){
        $timestamp = $_SERVER["REQUEST_TIME"];
        $nonce = mt_rand(100001,2147483123);
        $tmpArr = array($wecfg['token'], $timestamp, $nonce);
        sort($tmpArr, SORT_STRING); // use SORT_STRING rule
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        $signature = $tmpStr;
        $echostr = basKeyid::kidRand(32);
        $url = $apiurl ? $apiurl : "{root}/plus/api/wechat.php";
        if(!strpos($url,'?')) $url .= '?';
        $url .= "&signature=$signature&timestamp=$timestamp&nonce=$nonce&echostr=$echostr";
        $url = wysBasic::fmtUrl($url);
        //$url = str_replace($from, $to, $url);
        return $url;
    }
    
}

