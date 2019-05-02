<?php
namespace imcat;

# http://ai.baidu.com/docs#/NLP-API/2759b696

class aisYuyan extends aisBdapi{

    static $ckey = 'yuyan';

    // 标签提取
    static function tags($title, $content, $max=5)
    {
        $url = 'https://aip.baidubce.com/rpc/2.0/nlp/v1/keyword';
        $data['title'] = $title;
        $data['content'] = $content;
        $res = self::remote($url, $data, 1);
        $re['error'] = 0;
        if(!empty($res['error_code'])){
            $re['error'] = 'api:'.$res['error_code'];
            $re['msg'] = $res['error_msg'];
            // log
            return $re;
        }else{
            $tags = ''; $cnt = 0;
            if(!empty($res['items'])){
                foreach($res['items'] as $row){
                    $tags .= ($tags?',':'').$row['tag'];
                    $cnt++; if($cnt==$max) break;
                }
            }
            $re['tags'] = $tags;
        } //dump($res);
        return $re;
    }

    // 摘要提取
    static function sums($title, $content)
    {
        $url = 'https://aip.baidubce.com/rpc/2.0/nlp/v1/news_summary';
        $data['title'] = $title;
        $data['content'] = $content;
        $res = self::remote($url, $data, 1);
        return $res;
    }

    // 文本审核
    static function check($content)
    {
        $url = 'https://aip.baidubce.com/rest/2.0/antispam/v2/spam';
        $data = "content=$content";
        $res = self::remote($url, $data);
        $re['error'] = 0;
        if(!empty($res['error_code'])){
            $re['error'] = 'api:'.$res['error_code'];
            $re['msg'] = $res['error_msg'];
            // log
            return $re;
        }else{
            $tmp = $res['result'];
            $re['error'] = $tmp['spam'];
            $tab = array('reject','review');
            $msg = "";
            if(!empty($tmp['spam'])){
                foreach($tab as $key){
                    $info = "";
                    foreach($tmp[$key] as $row){
                        if(!empty($row['hit'])){
                            $info .= '['.implode(',',$row['hit']).'];';
                        }
                    }
                    $info && $msg .= "$key:($info)\n";
                }
                $msg || $msg = '(含有广告或无意义信息)';
            }
            $re['msg'] = $msg;
        } //dump($res);
        return $re;
    }

    // 文本纠错
    static function ecnet($content)
    {
        $url = 'https://aip.baidubce.com/rpc/2.0/nlp/v1/ecnet';
        $data['text'] = $content;
        $res = self::remote($url, $data, 1);
        $re['error'] = 0;
        $re['itms'] = [];
        if(!empty($res['error_code'])){
            $re['error'] = 'api:'.$res['error_code'];
            $re['msg'] = $res['error_msg'];
            // log
            return $re;
        }else{
            $tmp = $res['item'];
            $re['error'] = $tmp['score'];
            foreach($tmp['vec_fragment'] as $row){
                $ori = $row['ori_frag'];
                $to = $row['correct_frag'];
                $re['itms'][$ori] = $to;
            }
        } //dump($res);
        return $re;
    }

}

/*

// 标签提取
$title = "ttt";
$content = "ccc";
$res = aisYuyan::tags($title, $content);
dump($res);

// 摘要提取
$res = aisYuyan::sums($title, $content);
dump($res);

// 文本审核
$content .= "<br>傻逼，妈的；";
$content .= "<br>加我微信：13512386778；欢迎访问我的网站：xxx_yyy.com；";
$res = aisYuyan::check($content);
dump($res);

// 文本纠错
$ct4 = '今天天汽很好，我想洗洗一服，然后去看大海'; // 汽/气; 一/依/衣 依
$res4 = aisYuyan::ecnet($ct4);
dump($res4);

*/
