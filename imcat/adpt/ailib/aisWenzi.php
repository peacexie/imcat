<?php
namespace imcat;

class aisWenzi extends aisBdapi{

    static $ckey = 'shiwen';

    // 文字识别
    static function ocrgb($img, $url='', $ops='')
    {
        $api = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic';
        if(!empty($img)){
            $img = file_get_contents($img);
            $data = "image=".urlencode(base64_encode($img));
        }else{
            $data = "url=".urlencode($url);
        }
        if(empty($ops)){
            $data .= "&language_type=CHN_ENG"; // CHN_ENG, ENG
        }elseif(is_array($ops)){
            $ext = http_build_query($ops);
            $data .= "&$ext";
        }else{
            $data .= $ops; // CHN_ENG,ENG
        } //dump($data);
        $res = self::remote($api, $data);
        $re['error'] = $re['cnt'] = 0; 
        if(!empty($res['error_code'])){
            $re['error'] = 'api:'.$res['error_code'];
            $re['msg'] = $res['error_msg'];
            // log
            return $re;
        }else{
            $re['cnt'] = $res['words_result_num'];
            $lines = []; $cnt = 0;
            if(!empty($res['words_result'])){
                foreach($res['words_result'] as $row){
                    $lines[] = $row['words'];
                }
            }
            $re['lines'] = $lines;
        } //dump($res);
        return $re;
    }

    // 手写文字识别
    static function shou($img, $ops='')
    {
        $api = 'https://aip.baidubce.com/rest/2.0/ocr/v1/handwriting';
        $img = file_get_contents($img);
        $data = "image=".urlencode(base64_encode($img));
        if(!empty($ops)){
            $ext = http_build_query($ops);
            $data .= "&$ext";
        } //dump($data);
        $res = self::remote($api, $data);
        #return $res; 
        $res = self::remote($api, $data);
        $re['error'] = $re['cnt'] = 0; 
        if(!empty($res['error_code'])){
            $re['error'] = 'api:'.$res['error_code'];
            $re['msg'] = $res['error_msg'];
            // log
            return $re;
        }else{
            $re['cnt'] = $res['words_result_num'];
            $lines = []; $cnt = 0;
            if(!empty($res['words_result'])){
                foreach($res['words_result'] as $row){
                    $lines[] = $row['words'];
                }
            }
            $re['lines'] = $lines;
        } //dump($res);
        return $re;
    }

    // 数字识别
    static function numbs($img, $ops='')
    {
        $api = 'https://aip.baidubce.com/rest/2.0/ocr/v1/numbers';
        $img = file_get_contents($img);
        $data = "image=".urlencode(base64_encode($img));
        if(!empty($ops)){
            $ext = http_build_query($ops);
            $data .= "&$ext";
        } //dump($data);
        $res = self::remote($api, $data);
        return $res; 
    }

    // 二维码识别
    static function qrcode($img, $ops='')
    {
        $api = 'https://aip.baidubce.com/rest/2.0/ocr/v1/qrcode';
        $img = file_get_contents($img);
        $data = "image=".urlencode(base64_encode($img));
        if(!empty($ops)){
            $ext = http_build_query($ops);
            $data .= "&$ext";
        } //dump($data);
        $res = self::remote($api, $data);
        return $res; 
    }


}

/*

$img = '../orc1/login2.png'; // 0;// index1.gif, login1.png, baidu.jpg
$url = 'https://ss0.bdstatic.com/-0U0bnSm1A5BphGlnYG/tam-ogel/7a97feeed7bbf53175e280654ccf47e0_259_194.jpg';
#$img = 0;
$ops['language_type'] = 'ENG'; // CHN_ENG, ENG
$res = aisWenzi::ocrgb($img, $url, $ops);
dump($res);

*/
