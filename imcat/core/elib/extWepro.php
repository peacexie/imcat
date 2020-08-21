<?php
namespace imcat;

class extWepro{
    
    static $api = 'https://api.weixin.qq.com';

    public $reqs = [];
    public $wxcpt = null;
    public $acfg = [];

    # ================================ 

    static function getAdtab()
    {
        $list = data('wxadmin',"",120);
        $res = [];
        if(!empty($list)){
            foreach ($list as $row) {
                $res[$row['auser']] = ['mtel'=>$row['mtel'],'mname'=>$row['mname']];
            }
        }
        return $res;

    }

    // ================================================

    static function sendMsg($idrow, $aflag='new', $to=''){
        $row = is_array($idrow) ? $idrow : data('wxalerm',"cid='$idrow'",1);
        $wecfg = read('wepro','ex');
        $tpl = $wecfg['ucfg']['tpl']; // 'mTVIba1oLeF4SJXsFL6dstHh5CNXUaTidZ0Um6W40HE';
        if($aflag=='new' && !$to){
            $to = $wecfg['ucfg']['acget']; // 'obI4S5fijnbqq6_yHskPxct6bqSo';
        }
        if(empty($row) || empty($to)){
            return;
        }
        $time = date('m-d H:i');
        $tab1 = [
            'new'  => '新任务分派通知：', //'新登记'; 
            'sent' => '新任务处理通知：', //'已出警'; 
            'help' => '任务增援请求', //'需增援'; 
            'join' => '已增援通知', //'已增援'; 
            'done' => '任务已完成通知', //'已完成'; 
        ];
        $mpid = $wecfg['ucfg']['mpid']; // 'wx1a43a0f7860a75c5'; // mp
        $proid = $wecfg['AppID']; // pro
        $page = '/pages/home/index';
        $data = [
            'first' => ['value'=>"[$time] ".$tab1[$aflag]], // {{first.DATA}}
            'keyword1' => ['value'=>$row['cid']], // 工单号：{{keyword1.DATA}}
            'keyword2' => ['value'=>$row['detail']], // 任务目的地：{{keyword2.DATA}}
            'keyword3' => ['value'=>$row['title']], // 任务内容：{{keyword3.DATA}}
            'remark' => ['value'=>"{$row['mname']} : {$row['mtel']}"], // {{remark.DATA}}
        ];
        $re2 = self::uniMsg1($to, $tpl, $mpid, $proid, $page, $data);
        return $re2;
    }

    # ================================ 

    /**
     * @brief GetAccessToken : 获取 accesstoken，不用主动调用
     *
     * @return : string accessToken
     */
    static function getAccessToken()
    {
        $wecfg = read('wepro','ex');
        $tkKey = "wepro_{$wecfg['AppID']}";
        $tkarr = extCache::tkGet($tkKey); // 取缓存
        if(!empty($tkarr['token'])){
            return $tkarr['token'];
        }
        $url = "/cgi-bin/token?grant_type=client_credential&appid={$wecfg['AppID']}&secret={$wecfg['AppSecret']}";
        $data = comHttp::doGet(self::$api.$url); //dump($data);
        if(!$data){ return ''; }
        $tmp = json_decode($data,1);
        $tkarr = ['token'=>$tmp["access_token"], 'exp'=>$tmp["expires_in"]-600];
        extCache::tkSet($tkKey, $tkarr, 30);
        return $tkarr['token'];
    }

    static function qrcode($path='/', $width=280)
    {
        $takon = self::getAccessToken(); dump($takon);
        $wecfg = read('wepro','ex');
        $params = ['access_token'=>$takon, 'path'=>$path, 'width'=>$width]; dump($params);
        $fp = '/weixin/'.extCache::fName("$path--$width--".$wecfg['AppID']).'.png';
        if(file_exists(DIR_DTMP.$fp)){
            return ['url'=>PATH_DTMP.$fp];
        } 
        $url = "/wxa/getwxacode?access_token=$takon"; 
        //$data = comHttp::curlCrawl(self::$api.$url, json_encode($params), ['type'=>'json']); dump($data);
        $json = $params;//json_encode($params); //dump($json);
        $data = self::httpPost(self::$api.$url, $json); dump($data);
        if(!$data){ return ['url'=>'']; }
        if(strpos($data,'"errcode":')>0){
            $tmp = json_decode($data,1);
            $tmp['url'] = '';
            return $tmp;
        }else{
            comFiles::put(DIR_DTMP.$fp, $data);
            return ['url'=>PATH_DTMP.$fp];
        }
    }

    static function uniMsg1($to, $tplid, $mpid, $proid, $page, $data)
    {
        $takon = self::getAccessToken(); //dump($takon);
        $msg = [
            'touser' => $to,
            /*
            'weapp_template_msg' => [
                'template_id' => $tplid,
                'page' => $page,
                'form_id' => $from,
                'data' => $data,
                'emphasis_keyword' => $emkey,
            ],*/
            'mp_template_msg' => [
                'appid' => $mpid,
                'template_id' => $tplid,
                'url' => '#',
                'miniprogram' => [
                    'appid' => $proid,
                    //'pagepath' => $page, // pagepath
                ],
                'data' => $data,
            ],
        ];
        $url = "/cgi-bin/message/wxopen/template/uniform_send?access_token=$takon"; 
        $json = json_encode($msg); //dump($json);
        $data = self::httpPost(self::$api.$url, $json); //dump($data);
        if(strpos($data,'"errcode":')>0){
            $tmp = json_decode($data,1);
            $tmp['url'] = '';
            return $tmp;
        }else{
            comFiles::put(DIR_DTMP.$fp, $data);
            return ['url'=>PATH_DTMP.$fp];
        }
    }

    static function subMsg1($from, $to, $tplid, $page, $data, $emkey)
    {
        $takon = self::getAccessToken(); //dump($takon);
        $msg = [
            'touser' => $to,
            'template_id' => $tplid,
            'page' => $page,
            "miniprogram_state" => "formal", // 跳转小程序类型：developer为开发版；trial为体验版；formal为正式版；默认为正式版
            "lang" => "zh_CN",
            'data' => $data,
        ];
        $url = "/cgi-bin/message/subscribe/send?access_token=$takon"; 
        $json = json_encode($msg); //dump($json);
        $data = self::httpPost(self::$api.$url, $json); dump($data);
        //if(!$data){ return ['url'=>'']; }
        if(strpos($data,'"errcode":')>0){
            $tmp = json_decode($data,1);
            $tmp['url'] = '';
            return $tmp;
        }else{
            comFiles::put(DIR_DTMP.$fp, $data);
            return ['url'=>PATH_DTMP.$fp];
        }
    }

/*
{
    "touser":"OPENID",
    "weapp_template_msg":{
        "template_id":"TEMPLATE_ID",
        "page":"page/page/index",
        "form_id":"FORMID",
        "data":{
            "keyword1":{
                "value":"339208499"
            },
            "keyword2":{
                "value":"2015年01月05日 12:30"
            },
            "keyword3":{
                "value":"腾讯微信总部"
            },
            "keyword4":{
                "value":"广州市海珠区新港中路397号"
            }
        },
        "emphasis_keyword":"keyword1.DATA"
    },
}
*/

    static function httpPost($url, $postData)
    {
        //self::__checkDeps();
        $ch = curl_init();

        self::__setSSLOpts($ch, $url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        
        return self::__exec($ch);
    }
    static private function __setSSLOpts($ch, $url)
    {
        if (stripos($url,"https://") !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        }
    }
    static private function __exec($ch)
    {
        $output = curl_exec($ch);
        $status = curl_getinfo($ch); //dump($status);
        curl_close($ch);

        if ($output === false) {
            throw new Exception("network error");
        }
        
        if (intval($status["http_code"]) != 200) {
            throw new Exception(
                "unexpected http code ". intval($status["http_code"]));
        }

        return $output;
    }

}

/*


*/
