<?php
namespace imcat;
use OSS\OssClient;

// 阿里直播类
class aliLive extends aliBase
{

    private $pushhost = '';
    private $livehost = '';
    public $permKey = ''; // 鉴权 KEY
    private $apkey = ''; // test,dg,sz

    //
    public function __construct(){
        // 读配置
        $appkeys = read('aliyun', 'ex');
        $this->accessKeyId = $appkeys['live_ak'];
        $this->accessKeySecret = $appkeys['live_as'];
        $this->permKey = $appkeys['live_pk'];
        $this->pushhost = $appkeys['pushhost'];
        $this->livehost = $appkeys['livehost'];
        $this->apkey = 'live'; // live
    }

    // 获取`推流/播放`url, Stream='dg_1234'
    public function liveUrls($Stream, $apkey='', $exp=3600){
        $AppName = $apkey ? $apkey : $this->apkey;
        $time = time() + $exp; // 时间戳，有效时间
        $uuid = md5(uniqid(mt_rand(), true)); //str_replace('-','',$this->uuid());
        $strpush = "/$AppName/$Stream-$time-$uuid-0-$this->permKey";
        // 里面的直播推流中心服务器域名、vhost域名可根据自身实际情况进行设置
        $pushurl = "rtmp://$this->pushhost/$AppName/$Stream?vhost=$this->livehost&auth_key=$time-$uuid-0-".md5($strpush);
        $strviewrtmp = "/$AppName/$Stream-$time-$uuid-0-$this->permKey";
        $strviewflv = "/$AppName/$Stream.flv-$time-$uuid-0-$this->permKey";
        $strviewm3u8 = "/$AppName/$Stream.m3u8-$time-$uuid-0-$this->permKey";
        $rtmpurl = "rtmp://$this->livehost/$AppName/$Stream?auth_key=$time-$uuid-0-".md5($strviewrtmp);
        $flvurl  = "http://$this->livehost/$AppName/$Stream.flv?auth_key=$time-$uuid-0-".md5($strviewflv);
        $m3u8url = "http://$this->livehost/$AppName/$Stream.m3u8?auth_key=$time-$uuid-0-".md5($strviewm3u8);
        // /video/standard/1K.html-1444435200-0-0-aliyuncdnexp1234
        return [
            'pushurl' => $pushurl,
            'rtmpurl' => $rtmpurl,
            'flvurl' => $flvurl,
            'm3u8url' => $m3u8url,
        ];
    }

}

/*

*/
