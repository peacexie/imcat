<?php
namespace imcat;

# 题库

class extQadb{

    public $db = null;

    function __construct($xxx=''){
        // 检查
    }

    // --------------------------------------------

    # 随机n道题
    static function viewRndN($type='sin', $n=10){
        //SELECT * FROM xx WHERE catid='sin' AND ORDER BY RAND() LIMIT 5;
        $list = data('qadb.join', "catid='$type'", $n, 'RAND()'); // 效率待提升
        return $list;
    }
    static function viewTabs($case='a10'){
        $cfp = tpath(0,0,1).'/qadb/excfg.php';
        $excfg = include $cfp; $ctab = $excfg['case'];
        $extab = read('qadb.i');
        if(!isset($ctab[$case])){
            die("Error $case!");
        }else{
            $cfg = $ctab[$case];
        }
        if(substr($case,0,1)=='a'){
            $type = req('type','sin');
            if(!isset($extab[$type])){
                die("Error $type!");
            }else{
                $cfg[0] = "$type-".$cfg[0];
            }
        } //dump($cfg);
        $tabs = []; 
        $tmp1 = explode('+', $cfg[0]);
        $tmp2 = explode('+', $cfg[1]);
        foreach ($tmp1 as $ik=>$iv) {
            $itmp = explode('-', $iv);
            $tabs[$itmp[0]]['no'] = $ik;
            $tabs[$itmp[0]]['title'] = $extab[$itmp[0]]['title'];
            $tabs[$itmp[0]]['num'] = $itmp[1]; 
            $tabs[$itmp[0]]['score'] = $tmp2[$ik];
            $tabs[$itmp[0]]['list'] = self::viewRndN($itmp[0], $itmp[1]);
        } //dump($tabs);
        return $tabs;
    }

    static function viewOpts($row){
        $res = self::epreOpts($row);
        $xops = []; 
        foreach ($res['opitm'] as $i=>$itm) {
            if(!empty($itm)){
                $chr = chr(65+$i);
                $xops[$chr] = $itm ; 
            }    
        }
        $res['xops'] = $xops;
        return $res;
    }

    // --------------------------------------------

    // 编辑设置 self::epreOpts()
    static function epreOpts($fmo){
        $opitm = ['','','',''];
        //$opres = '';
        $aresInp = $aresTxt = '';
        if(!isset($fmo['catid'])){
            $fmo['ares'] = '';
        }elseif(strpos(',tf,sin,mul,',$fmo['catid'])>0){
            $opitm = explode('(^ops^)', $fmo['opts'].'(^ops^)'.'(^ops^)'.'(^ops^)'.'(^ops^)'.'(^ops^)');
            //$opres = $fmo['ares'];
        }else{
            $aresInp = $fmo['catid']=='fb' ? $fmo['ares'] : '';
            $aresTxt = $fmo['catid']=='qa' ? $fmo['ares'] : '';
        }
        return ['opitm'=>$opitm, 'aresInp'=>$aresInp, 'aresTxt'=>$aresTxt];
    }

    // 保存设置 self::preSave()
    static function preSave(&$dop){
        //dump($dop); 
        $opitm = basReq::arr('opitm','Html'); $opts = implode('(^ops^)', $opitm); // 过滤
        $opres = basReq::arr('opres');        $ores = implode(',', $opres);
        //dump($opts); dump($ores); dump($dop->fmv); 
        if($dop->fmv['catid']=='tf'){
            $fmv['opts'] = strip_tags($opts);
            $fmv['ares'] = $ores;
        }elseif($dop->fmv['catid']=='sin'){
            $fmv['opts'] = strip_tags($opts);
            $fmv['ares'] = $ores;
        }elseif($dop->fmv['catid']=='mul'){
            $fmv['opts'] = strip_tags($opts);
            $fmv['ares'] = $ores;
        }elseif($dop->fmv['catid']=='fb'){
            $fmv['opts'] = '';
            $fmv['ares'] = basReq::val('aresInp');
        }else{ // qa
            $fmv['opts'] = '';
            $fmv['ares'] = strip_tags(basReq::val('aresTxt'));
            $fmv['ares'] = preg_replace('/^答\s*(：|:)\s*/is', '', $fmv['ares']);
        }
        if(!in_array($dop->fmv['catid'],['qa'])){
            $fmv['title'] = self::fmtTBlank($dop->fmv['title']);
        }
        $dop->fmv = array_merge($dop->fmv,$fmv);
        // opitm->opts opres|aresInp|aresTxt->ares aexp
        // dump($dop->fmv);  die();
    }

    // --------------------------------------------

    static function fmtTBlank($title, $flag=0){
        preg_match('/(\(|（)([　| | |_|\s]*)(\)|）)/', $title, $pts); //dump($pts);
        $title = empty($pts[0]) ? "{$title}（___）" : str_replace($pts[0], '（___）', $title);
        return $title;
    }

}

/*
    '(^ops^)'

    // 单题型
    'a05'=>[5,20],
    'a10'=>[10,10],
    'a20'=>[20,5],
    'a50'=>[50,2],
    'a100'=>[100,1],
    // 20题方案
    'c2a'=>['tf-10+sin-10', '5+5'],
    'c2b'=>['sin-10+mul-10', '4+6'],
    'c2c'=>['tf-5+sin-10+mul-5', '4+5+6'],
    // 40-50题方案
    'c4a'=>['sin-40+mul-5', '2+4'],
    'c4b'=>['tf-10+sin-30+mul-5', '2+2+4'],
    'c4c'=>['tf-10+sin-20+mul-5+bl-5', '2+2+4+4'],
    // 80-90题方案
    'c8a'=>['tf-10+sin-50+mul-20', '1+1+2'],
    'c8b'=>['tf-10+sin-50+mul-10+bl-10', '1+1+2+2'],
    'c8c'=>['tf-10+sin-40+mul-10+bl-5+qa-5', '1+1+2+2+4'],

    // data($mod, $whr='', $lmt='10', $ord='', $ops=[])
    get($mod, $whr='', $lmt='10', $ord='', $ops=[]);
    $mod: news, news.join
    $whr: `show`='all', `show`='0', 
    $lmt: 1, 10, 3,10, 10.page, count
    $ord: atime-1, atime
    $ops: xxx

实测 38万数据，随机取10条 
1 可以随机取值，执行时间1-2秒。 
2 运行超过20秒没反应，试了3次。 
3 取的10条数据是连续的ID，执行0.001秒。 
4 随机取10条，id在1-4000之间，执行0.003秒，试了10次以上。 
5 随机取10条，id在1-4000之间，执行0.003秒，试了10次以上。 
6 取的10条数据是连续的ID，执行0.001秒。

*/
