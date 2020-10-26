<?php
namespace imcat;

// extCrawl
class extCrawl{

    # 取多个选择器(<pqs1>,<pqs2>)中一个的 值/属性
    # 用于一个列表中，获取某一项值可能有两种或多中规范
    static function getDomVals($dom, $pqs, $attr = 'text')
    {
        if (empty($pqs)) {
            return '';
        }
        if (strpos($pqs, ',') > 0) {
            $tab = explode(',', $pqs);
            for ($i = 0; $i < count($tab); $i++) {
                $res = self::getDomOne($dom, $tab[$i], $attr);
                if ($res) {
                    return $res;
                }
            }
        } else {
            return self::getDomOne($dom, $pqs, $attr);
        }
        return '';
    }

    # 取一个(<pqs>)选择器的 值/属性
    static function getDomOne($dom, $pqs, $attr = 'text')
    {
        $e = pq($dom)->find($pqs);
        if ($attr == 'text') {
            return pq($e)->text();
        } elseif ($attr == 'html') {
            return pq($e)->html();
        } else {
            return pq($e)->attr($attr);
        }
    }

    // src: 链接url
    // base1: 当前页url
    // bext: 额外附加url(手动设置)
    static function urlJoin($src, $base1, $bext=''){
        $base = $bext ?: $base1;
        $binfo = parse_url($base);
        if(strpos($src,'://')>0){ // 完整地址
            return $src; // `?rul=ftp://d/f.htm` ?这种地址先不考虑吧
        }elseif(substr($src,0,2)=='//'){ // `//`开头
            return $binfo['scheme'].':'.$src;
        }elseif(substr($src,0,1)=='/'){ // `/`开头 `$bext.$src` ?两个//可修改bext参数解决
            return $bext ? $bext.$src : ($binfo['scheme'].'://'.$binfo['host'].$src);
        }else{ // `./`, `../`, `file.ext` 开头(可能有多个)
            $base = empty($binfo['scheme']) ? '' : $binfo['scheme'].'://'.$binfo['host'];
            if(isset($binfo['path'])){
                $path = $binfo['path'].(substr($binfo['path'],-1)=='/' ? 'file.ext' : '');
            }else{
                $path = '/';
            }
            $url = substr($src,0,1)=='.' ? $path.$src : "$path./$src";
            $url = preg_replace("/\/([^\/]+)?(\w+)\.\//i", "/", $url, 1); // /xxx./
            $url = preg_replace("/\/([^\/]+)\/([^\/]+)?(\w+)\.\.\//i", "/", $url, 1); // /xxx/yyy../
            $url = preg_replace("/\/([^\/]+)?(\w+)\.\.\//i", "/", $url, 1); // /xxx../
            $cnt = 0; // 万一有死循环呢?
            while(strpos($url,'../')>0) { // /../
                $url = preg_replace("/\/([^\/]+)\/\.\.\//i", "/", $url, 1); // /xxx/../
                $url = preg_replace("/^(\/\.\.\/)+/i", "/", $url); // /../ 开头
                $cnt++; if($cnt>12) break;
            }
            return $base.$url;
        }
    }

    // 
    static function testUrls(){

        $ta = [
            'dir0/dir1/dir2/dir3/',
            '/dir0/dir1/dir2/dir3/',
            '/dir0/dir1/dir2/dir3/file4',
            '/dir0/dir1/dir2/dir3/file4.ext',
        ];
        $tb = [
            './dira/fileb.ext',
            '../dira/fileb.ext',
            '../../dira/fileb.ext',
            './../../../dira/fileb.ext',
        ];

        $base = 'http://sub1.domain.com/d1/d2/d3/f4.ext';
        $bex1 = 'http://sub2.domain.com/d1/d2/d3/';
        $bex2 = 'http://sub3.domain.com/';

        $tab[] = [
            'http://sub.domain.com/aa/bb/cc1.ex1',
            $base, ''
        ];
        $tab[] = [
            '//sub.domain.com/aa/bb/cc2.ex1',
            $base, ''
        ];
        $tab[] = [
            '/aa/bb/cc3.ex1',
            $base, ''
        ];
        $tab[] = [
            '/aa/bb/cc3.ex1',
            '', substr($bex1,0,strlen($bex1)-1)
        ];
        $tab[] = [
            '/aa/bb/cc3.ex1',
            '', substr($bex2,0,strlen($bex2)-1)
        ];
        foreach ($tab as $ki => $row) {
            $url = Tools::urlJoin($row[0], $row[1], $row[2]);
            echo "org: $row[0],<br> base: ".($row[1] ?: '(null)').",<br> bext: ".($row[2] ?: '(null)')." 
                <br> -=> $url<br><br>\n";
        } echo "<hr>\n";

        $exts1 = [$base, $bex1];
        foreach ($exts1 as $urlb) {
            foreach ($tb as $path) {
                $url = Tools::urlJoin($path, $urlb, '');
                echo "org: $path,<br> base: $urlb,<br> bext: (null) 
                <br> -=> $url<br><br>\n";
            }
        } echo "<hr>\n";

        $exts2 = [$bex1, $bex2];
        foreach ($exts2 as $urlb) {
            foreach ($tb as $path) {
                $url = self::urlJoin($path, '', $urlb);
                echo "org: $path,<br> base: (null),<br> bext: $urlb 
                <br> -=> $url<br><br>\n";
            }
        } echo "<hr>\n";

    }

}
