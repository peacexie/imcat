<?php
namespace imcat;

// 标签编译 类

class vopCTag{

    static $tag_fix = "tag:[a-zA-Z][\w]{1,17}";
    static $tag_func = array(
        'surl'  => 'vopUrl::fout',
        'lang'  => 'basLang::show',
        'spic'  => 'vopCell::cPic',
        'title' => 'vopCell::cTitle',
        'html'  => 'vopCell::cHtml',
        'text'  => 'vopCell::cText',
        'stime' => 'vopCell::cTime',
        'sopt'  => 'vopCell::cOpt',
        'show'  => 'vopCell::cShow',
    ); // \imcat\
    
    // vop:cecho 标签
    // {stime($re4['atime']);}                 =>  [?=vopCell::cTime($re4['atime']);;?]
    // {stime(1234657890,Y-m-d H:i,a=va\nb=vb)}
    // {surl(0)}  {surl("demo.$re4[did]",.)}  {surl("about-$re1[kid]",-)}  {surl(chn:info-nav)}
    // {sopt(c0769+c0735,china)}
    static function cecho($tpl='',$func='show'){
        preg_match_all("/\{{$func}[\(]([^}]{1,240})[\)];?\}/",$tpl,$_m); 
        if(!empty($_m[1])){
            foreach($_m[1] as $k=>$tiem){  
                $sorg = $_m[0][$k];
                $a = explode(',',$tiem);
                $s = '';
                foreach($a as $v){ 
                    $v2 = self::_1Para($v);
                    $s .= ",$v2";    
                }
                if($s){
                    $s = substr($s,1); 
                    // like as : cbase配置:run.stamp
                    if($func=='show' || !isset(self::$tag_func[$func])){
                        $funu = "vopCell::cShow($s,\$this)"; 
                    }else{
                        $clsm = self::$tag_func[$func];
                        $funu = "$clsm($s)";     
                    }
                    $tpl = str_replace($sorg,"<?=$funu?>",$tpl);
                }
            }
        }
        return $tpl;
    }
    
    // tags - 可嵌套
    static function tagMain($tpl=''){
        foreach(self::$tag_func as $ftag=>$tofunc){
            $tpl = self::cecho($tpl,$ftag);    
        }
        preg_match_all("/\{".self::$tag_fix."/i", $tpl, $_m);
        $tags = array();
        if(!empty($_m[0])){
            for($i=count($_m[0])-1;$i>=0;$i--){ 
                $tpl = self::tagOne($tpl, $_m[0][$i]);
            }
        }
        return $tpl;
    }
    
    // 解析一个标签
    // $tag1 like {tag:flag3
    static function tagOne($tpl, $tag1){
        global $_cbase; 
        $tag2 = str_replace('{tag:','{/tag:',$tag1).'}'; // 结束符 {tag:flag3
        $p1 = strpos($tpl, $tag1);
        $p2 = strpos($tpl, $tag2);
        $data = substr($tpl, $p1, $p2-$p1+strlen($tag2)); 
        $tag0 = substr($tag1,5);
        $varid = '$T_'.$tag0;
        preg_match("/\{tag:$tag0\=([^\n]{12,1200}\])\}/i", $data, $_m);
        // $_m[0] : {tag:flag2=[...]}
        // $_m[1] : [Type,re2][modid,demo][idfix,top]
        if($p2>$p1 & $_m){ 
            $pt = self::tagParas($_m[1]);
            $type = $pt[0]; $re = $pt[1]; $ps = $pt[2]; $dstr = $data;  
            $unv = in_array($type,array('One','jsOne')) ? '$'.$re : $varid;
            $dstr = self::tagRows($dstr, $_m, $tag2, $varid, $re, $type);
            if(substr($type,0,2)=='js'){ //js标签
                //$dstr = str_replace('$this->show(', '$vop->show(', $dstr);
                $tplnow = $_cbase['run']['comp']; 
                $jsfile = vopTpls::path('tpc')."/$tplnow.$tag0.comjs.php";
                $dstr = str_replace($_m[0], '<!-- start('.$unv.'); -->', $dstr);
                comFiles::put($jsfile, "<?php ".NSP_INIT." ?>".$dstr); //写入缓存
                $ps = self::_1Pjs($_m[1]);
                $dstr = "<div id='jsid_tags_".str_replace('/','_',$tplnow)."_$tag0'><!--".str_replace('[js','['.$tplnow.','.$tag0.'][',$ps)."--></div>";
            }else{
                $dstr = str_replace($_m[0], '<?php '.$unv.' = $this->tagParse("'.$tag0.'","'.$type.'",'.$ps.'); ?>', $dstr);
            }
            $tpl = str_replace($data, $dstr, $tpl);
        }
        return $tpl;
    }
    
    // 分析一个标签的参数
    // $str = '[listtype,re1][modid,$mod][ids,top][china,area,in.get]';
    // re = ('listtype','re1',array(array('modid',$mod),array('ids','top'),array('china','area','in.get')));
    static function tagParas($str,$ret='str'){ 
        preg_match_all("/\[\s*(.+?)\s*\]/is",$str, $m0); 
        $m1 = $m0[1]; $a1 = $m1[0]; unset($m1[0]);
        $a2 = explode(',',"$a1,");
        $type = $a2[0]; $re = $a2[1]; 
        $paras = "array("; $pare1 = array();
        $re || $re = 'v';
        $js = (substr($type,0,2)=='js') ? 1 : 0; 
        foreach($m1 as $k=>$v){
            $a2 = explode(',',"$v");    
            $paras .= "array("; $pare2 = array();
            foreach($a2 as $k2=>$v2){
                $pare2[] = $v2;
                $v2 = self::_1Para($v2,$js);
                // [modid,$ucfg('mod')] -=> $ucfg['mod']
                //$v2 = str_replace(array("('","')"),array("['","']"),$v2); 
                $paras .= "$v2,";
            }
            $paras .= "),";
            $pare1[] = $pare2;
        }
        $paras .= ")";
        $a1 = array(',),',);
        $a2 = array('),',);
        $patmp = $ret=='str' ? $paras : $pare1; 
        $paras = str_replace($a1,$a2,$patmp);
        return array($type,$re,$paras);
    }
    
    // 分析一个标签的{:row} 
    // extract, if()
    static function tagRows($dstr, $_m, $tag2, $varid, $re, $type=''){
        $unv = in_array($type,array('One','jsOne')) ? '$'.$re : $varid;
        if(in_array($type,array('One','jsOne'))){ 
            $for = '<?php if(!empty('.$unv.')){ extract($'.$re.',EXTR_PREFIX_ALL,\'t\'); ?>';
            $dstr = str_replace($_m[0], $_m[0].$for, $dstr);
            $dstr = str_replace($tag2, $tag2.'<?php } ?>', $dstr);    
        }else{ //'Type','List','Page','jsType','jsList','jsPage'
            if(!strstr($dstr,'{:row}')){
                $dstr = str_replace($_m[0], $_m[0].'{:row}', $dstr);
                $dstr = str_replace($tag2, $tag2.'{/row}', $dstr);
            }
            $for = '<?php if(!empty('.$varid.')){ foreach('.$varid.' as $i_'.$re.' => $'.$re.'){ extract($'.$re.',EXTR_PREFIX_ALL,\'t\'); ?>';
            $dstr = str_replace('{:row}', $for, $dstr);
            $dstr = str_replace('{/row}', '<?php } } ?>', $dstr);
        }
        $dstr = str_replace($tag2, '<?php unset('.$unv.'); ?>', $dstr);
        return $dstr;
    }
    
    // 分析一个参数
    // class::func(@$re1['h'])
    static function _1Para($v,$js=0){
        if(empty($v)) return $v;
        // array('w'=>250\n'h'=>@$re1['h'])
        if(substr($v,0,6)=='array('){ 
            $v = str_replace("\\n",",",$v);
            return $v;
        // class::func(@$re1['h']) / tex('texCargo')->expwhr()
        }elseif(strpos($v,'::') || strpos($v,'->')){ 
            return $v;
        // $re1['kid'], "about-$re1[kid]"
        }elseif(strstr($v,'$')){ // || strstr($v,'"')
            //$v = $js ? '[?php echo '.$v.'; ?]' : $v;
            return $v;
        // did='2004-33-2ycx'
        }elseif(strstr($v,"'")){
            return '"'.$v.'"';
        }else{
            return "'$v'";
        }
        // [where,tex('texCargo')->expwhr()]
    }
    
    // [c_page/test_tjs,flag1][Type,re1][modid,$_modx1][idfix,top]
    static function _1Pjs($s){
        $t = str_replace(array('([','][','])',),array('',',','',),"($s)");
        $a = explode(',',$t);
        foreach($a as $k=>$v){
            if(strstr($v,'$')){
                $s = str_replace($v,'<?='.$v.'; ?>',$s);    
            }
        }
        return $s;
    }
        
}