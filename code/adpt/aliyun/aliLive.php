<?php
namespace imcat;
use OSS\OssClient;

// 阿里直播类
class aliLive extends aliBase
{

    private $pushhost = '';
    private $livehost = '';
    public $permKey = ''; // 鉴权 KEY

    private $city = ''; // test,dg,sz

    //
    public function __construct()
    {
        // 读配置
        $appkeys = config('appkeys');
        $this->accessKeyId = $appkeys['live_ak'];
        $this->accessKeySecret = $appkeys['live_as'];
        $this->permKey = $appkeys['live_pk'];
        $this->pushhost = $appkeys['pushhost'];
        $this->livehost = $appkeys['livehost'];
        $this->city = config('site_cid');
    }

    // 获取`推流/播放`url
    public function liveUrls($StreamName, $exp=3600, $city='')
    {
        $AppName = $city ? $city : $this->city;
        $time = time() + $exp; // 时间戳，有效时间
        $uuid = md5(uniqid(mt_rand(), true)); //str_replace('-','',$this->uuid());
        $strpush = "/$AppName/$StreamName-$time-$uuid-0-$this->permKey";
        // 里面的直播推流中心服务器域名、vhost域名可根据自身实际情况进行设置
        $pushurl = "rtmp://$this->pushhost/$AppName/$StreamName?vhost=$this->livehost&auth_key=$time-$uuid-0-".md5($strpush);
        $strviewrtmp = "/$AppName/$StreamName-$time-$uuid-0-$this->permKey";
        $strviewflv = "/$AppName/$StreamName.flv-$time-$uuid-0-$this->permKey";
        $strviewm3u8 = "/$AppName/$StreamName.m3u8-$time-$uuid-0-$this->permKey";
        $rtmpurl = "rtmp://$this->livehost/$AppName/$StreamName?auth_key=$time-$uuid-0-".md5($strviewrtmp);
        $flvurl  = "http://$this->livehost/$AppName/$StreamName.flv?auth_key=$time-$uuid-0-".md5($strviewflv);
        $m3u8url = "http://$this->livehost/$AppName/$StreamName.m3u8?auth_key=$time-$uuid-0-".md5($strviewm3u8);
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
