<?php
namespace imcat;

# https://ai.baidu.com/ai-doc/SPEECH/
// 百度TTS

class aisTts extends aisBdapi{

    static $ckey = 'tts';

    // 语音识别
    static function audio2txt($fp, $xxx='xxx'){
        // params
        $cfg = self::cfg();
        $audio = file_get_contents($fp);
        $data64 = base64_encode($audio);
        $data = array(
            "dev_pid" => 1537, // 1537 表示识别普通话，使用输入法模型。1536表示识别普通话，使用搜索模型
            //"lm_id" => $LM_ID, // 测试自训练平台开启此项, dev_pid=8001，lm_id=1234
            "format" => substr($fp, -3), // 语音文件的格式，pcm/wav/amr/(m4a仅支持极速版)。不区分大小写。推荐pcm文件
            "rate" => 16000, // 采样率 16000;  // 固定值
            "token" => $cfg['token'], // access_token
            "cuid"=> $cfg['cuid'], // 
            "speech" => $data64, // 
            "len" => strlen($audio),
            "channel" => 1,
            //'scope' => $SCOPE, // audio_voice_assistant_get, brain_enhanced_asr, false
        ); //dump($data);
        $jsonStr = json_encode($data);
        // remote
        $url = "http://vop.baidu.com/server_api";
        //$url = "http://vop.baidu.com/pro_api"; // 极速版
        $rex = comHttp::curlPost($url, $jsonStr); // , ['type'=>'json']
        $res = json_decode($rex, 1);
        if(strpos($rex,'"result"')){
            $re['result'] = $res['result'];
            $re['err_msg'] = $res['err_msg'];
        }else{ // 错误信息
            $re['err_no'] = 'err:'.$res['err_no'];
            $re['err_msg'] = $res['err_msg'];
            // log
        } //dump($rex);
        return $re;
    }

    // 百度TTS 文字转语音
    static function text2mp3($text, $lang='zh', $per='0'){
        // params
        $cfg = self::cfg();
        $data = array(
            'tex' => urlencode(urlencode($text)), // 必填 合成的文本，使用UTF-8编码，请注意文本长度必须小于1024字节
            'lan' => $lang, // 必填  语言选择,填写zh
            'ctp' => 1, //  必填  客户端类型选择，web端填写1
            'tok' => $cfg['token'], // 必填  开放平台获取到的开发者 access_token
            'cuid'=> $cfg['cuid'], // 必填  用户唯一标识，用来区分用户，填写机器 MAC 地址或 IMEI 码，长度为60以内
            'spd' => 5, // 选填  语速，取值0-9，默认为5中语速
            'pit' => 5, // 选填  音调，取值0-9，默认为5中语调
            'vol' => 5, // 选填  音量，取值0-9，默认为5中音量
            'per' => $per, // 选填  发音人选择, 0为女声，1为男声，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女声
        ); //dump($data);
        $body = "";
        foreach($data as $key => $v){
            $body .= ($body ? '&' : '')."$key=$v";
        }
        $url = 'http://tsn.baidu.com/text2audio';
        // remote
        $rex = comHttp::curlPost($url, $body);
        if(strpos($rex,'"tts_logid"')){ // 如果合成出现错误，则会返回json结果
            $res = json_decode($rex, 1);
            $re['error'] = 'err:'.$res['err_no'];
            $re['msg'] = $res['err_msg'];
            // log
            return $re;
        }else{ // mp3文件内容
            $re['fp'] = "/remote/$cfg[cuid].mp3";
            $re['size'] = strlen($rex);
            file_put_contents(DIR_VARS.$re['fp'], $rex);
        } //dump($rex);
        return $re;
    }

    static function cfg(){
        $toks = self::token();
        $cfg['token'] = $toks['access_token'];
        $cfg['cuid'] = basKeyid::kidTemp().'-'.basKeyid::kidRand('24',4);
        // comHttp::setCache(0.1);
        return $cfg;
    }

}

/*

$res = aisTts::text2mp3('2020你好！');
dump($res); 

$res = aisTts::audio2txt('./orc1/16k.wav');
dump($res); 

*/
