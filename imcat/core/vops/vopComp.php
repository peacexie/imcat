<?php
namespace imcat;

// 模板编译 类
class vopComp{
    
    static $tplCfg = array(); //配置
    
    static function main($tplname) {
        $vob = new self(); 
        $res = $vob->build($tplname); 
        unset($vob); 
        return $res;
    }
    
    function __construct($tpl='') {
        global $_cbase;
        self::$tplCfg = $_cbase['tpl']; 
        if($tpl) return $this->build($tpl);
    }
    
    //模板编译 ---------- 
    function build($tpl){ 
        global $_cbase;
        $re = self::checkTpls($tpl);
        $_cbase['run']['comp'] = $tpl; //当前编译模板,js标签中使用
        $stpl = comFiles::get($re[0]); 
        $stpl = $this->bcore($stpl); //获取经编译后的内容
        $shead = NSP_INIT."\n\$this->tagRun('tplnow','$tpl','s');";
        $tpfp = vopTpls::tinc('_ctrls/texBase',0); $spend = '';
        if(file_exists($tpfp)){
            $class = '\\imcat\\'.$_cbase['tpl']['vdir'].'\\texBase';
            $shead .= "\ninclude_once '$tpfp';\nif(method_exists('$class','init'))"; 
            $shead .= "{ \$__i_vars=$class::init(\$this); if(!empty(\$__i_vars)){extract(\$__i_vars,EXTR_OVERWRITE);unset(\$__i_vars);} }";
            $spend = "<?php\nif(method_exists('$class','pend')){ $class::pend(); }\n?>";
        }
        $cfp = empty($_cbase['tpl']['fixmkv']) ? $re[1] : $re[1].'.'.$_cbase['tpl']['fixmkv'];
        comFiles::put($cfp, "<?php \n$shead \n?>\n".$stpl.$spend); //写入缓存
        return $cfp;
    }

    //模板编译核心
    function bcore($stpl=''){
        $stpl = self::impBlock($stpl); // 解析模板继承
        $stpl = self::incBlock($stpl); // 解析{code|inc|md}
        $stpl = self::phpBasic($stpl); // 基本php语法解析
        $stpl = self::phpFlow($stpl); // 流程控制语句
        $stpl = vopCTag::tagMain($stpl); // 系统标签解析
        return $stpl;
    }

    // 解析区块(code|inc|md), 如:{inc:"_pub/_head"}
    static function incBlock($stpl=''){ // `{inc:"{mod}_key.md"}`
        global $_cbase;
        preg_match_all("/{(inc|md|code):\"(.*)\"}/i", $stpl, $match); 
        if(count($match[2])>0){
            $arr = $match[2]; 
            foreach($arr as $k0=>$tpl){
                $mstr = $match[0][$k0];
                $mkey = $match[1][$k0];
                if($mkey=='code'){
                    $pfile = "vopTpls::tinc('$tpl',0)";
                    $ptpl = "<?php include $pfile; ?>";
                }else{ 
                    $mkv = [];
                    if(strpos($tpl,'}')){ // 漏洞???
                        $mkv = $_cbase['mkv'];
                        $from = ['{mod}', '{key}', '{view}', '{mkv}'];
                        $to = [$mkv['mod'], $mkv['key'], $mkv['view'], $mkv['mkv']];
                        $tpl = str_replace($from, $to, $tpl);
                    }
                    $ext = strpos($tpl,'.')>0 ? '' : ($mkey=='md'?'md':'htm');
                    $pfile = vopTpls::tinc("$tpl.$ext", 0);
                    $ptpl = comFiles::get($pfile, 1);
                    if(!empty($mkv['mkv'])){
                        if(!$ptpl){ 
                            glbHtml::httpStatus('404'); 
                            $ptpl = "File `{$_cbase['tpl']['vdir']}/$tpl.$ext` NOT Found!";
                        }else{
                            $_cbase['tpl']['fixmkv'] = $mkv['mkv'];
                        }
                    }
                    strpos($ptpl,'"}')>0 && $ptpl = self::incBlock($ptpl);
                    $mkey=='md'          && $ptpl = extMkdown::pdext($ptpl, 0);
                    $ptpl || $ptpl = '<!-- '.str_replace('"','`',$mstr).' -->';
                }
                $stpl = str_replace($mstr, $ptpl, $stpl);
            }
        }
        return $stpl;
    }

    // 模板继承extend,block,layout,parent,inherit
    // {imp:"_dir/layout"] // {block:title]Welcome!{/block:title] 
    // {block:title] {:parent} {:clear} News - Project Name{/block:title]
    static function impBlock($stpl=''){
        preg_match("/\{imp:\"([\S]{3,48})\"\}/i", $stpl, $match);
        if(empty($match[0]) || empty($match[1])) return $stpl; //没有imp,原样返回
        $layfile = vopTpls::tinc($match[1].'.htm', 0); 
        $layout = comFiles::get($layfile);
        $stpl = substr($stpl,strlen($match[0]));
        preg_match_all("/\{block:([a-z][a-z0-9_]{1,17})\}/i", $layout, $match);
        if(empty($match[1])){ return $layout; }//没有block
        foreach($match[1] as $key){ 
            $k1 = "{block:$key}"; $k2 = "{/block:$key}";
            $blk1 = basElm::getPos($stpl,array($k1,$k2));
            $blkp = basElm::getPos($layout,array($k1,$k2));
            if($blk1=='{:clear}'){
                $layout = str_replace("$k1{$blkp}$k2", "", $layout);
            }elseif(!empty($blk1)){ 
                if(strlen($blkp)>6 && strstr($blk1,'{:parent}')) $blk1 = str_replace("{:parent}", $blkp, $blk1);
                $layout = str_replace("$k1{$blkp}$k2", "{$blk1}", $layout);
            }
            $layout = str_replace(array($k1,$k2,'{:parent}'), "", $layout);
        }
        return $layout;
    }
    
    // 基本php语法解析, 常量,变量,函数,php代码
    static function phpBasic($stpl=''){
        /* 路径自定义替换 ----------------------------
            {outSwplayerPath}/ -=> http://cdn_d/ximps/vendui/swplayer/
        */
        $reps = glbConfig::read('repath', 'sy');
        if(!empty($reps['tpl'])){
            $stpl = str_replace(array_keys($reps['tpl']), array_values($reps['tpl']), $stpl);
        }
        /* 常量/变量解析 ----------------------------
            {=PATH_ROOT} -=> <?=PATH_ROOT; ?>
            {=$name} -=> <?=$name; ?> $re1['title'], $this->mod, $this->ucfg['q']
        */
        $stpl = preg_replace ( "/\{\=([A-Z_][A-Z0-9_]*)\}/s", "<?=\\1;?>", $stpl );    
        /*将变量{$name}替换成<?=$name; ?>,可以是数组 $name, $re1['title'], $this->mod, $this->ucfg['q'] */
        $stpl = preg_replace("/{\=(\\$[a-zA-Z_][\w\.\"\'\[\]\$\-\>\:]{0,64})}/i", "<?=@$1; ?>", $stpl); # ?下几个版本移除`@`
        /* php标签 ----------------------------
            {= date('Y-m-d'); }     =>  <?=date('Y-m-d'); ?>
            {php phpinfo();}    =>    <?php phpinfo(); ?>
         */
        $stpl = preg_replace ( "/\{=\s+([^}]+)\}/", "<?=\\1?>", $stpl );
        $stpl = preg_replace ( "/\{php\s+([^}]+)\}/", "<?php \\1?>", $stpl );
        return $stpl;
    }
    
    //模板解析 流程控制语句(Flow control statements)
    static function phpFlow($stpl){
        /* if 标签
            {if $name==1}        =>    <?php if ($name==1){ ?>
            {elseif $name==2}    =>    <?php } elseif ($name==2){ ?>
            {else}               =>    <?php } else { ?>
            {/if}                =>    <?php } ?>
        */
        $stpl = preg_replace ( "/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $stpl );
        $stpl = preg_replace ( "/\{else\}/", "<?php } else { ?>", $stpl );
        $stpl = preg_replace ( "/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $stpl );
        $stpl = preg_replace ( "/\{\/if\}/", "<?php } ?>", $stpl );
        // ----------------------------
        /* for 标签
            {for $i=0;$i<10;$i++}    =>    <?php for($i=0;$i<10;$i++) { ?>
            {/for}                   =>    <?php } ?>
        */
        $stpl = preg_replace("/\{for\s+(.+?)\}/","<?php for(\\1) { ?>",$stpl);
        $stpl = preg_replace("/\{\/for\}/","<?php } ?>",$stpl);
        // ----------------------------
        /* loop 标签
            {loop $arr $vo}          =>    <?php $n=1; if (is_array($arr) foreach($arr as $vo){ ?>
            {loop $arr $key $vo}     =>    <?php $n=1; if (is_array($array) foreach($arr as $key => $vo){ ?>
            {/loop}                  =>    <?php $n++;}unset($n) ?>
        */
        $stpl = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\}/", "<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>", $stpl );
        $stpl = preg_replace ( "/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/", "<?php \$n=1; if(is_array(\\1)) foreach(\\1 as \\2 => \\3) { ?>", $stpl );
        $stpl = preg_replace ( "/\{\/loop\}/", "<?php } ?>", $stpl );
        // ----------------------------
        // {php}{/php} 标签 --- 可在前后添加 <!--, --> 以便在html中显示调试
        $_aorg = array('{php} ','{php}','{/php}','<!--<?php','?>-->');
        $_aobj = array('{php}','<?php ','?>','<?php','?>');
        $stpl = str_replace ( $_aorg, $_aobj, $stpl );
        // ----------------------------
        return $stpl;
    }
    
    // retpl : 直接返回模版文件路径
    static function checkTpls($tpl,$retpl=0){ 
        global $_cbase;
        $tpldir = $_cbase['tpl']['vdir']; 
        $tplFile = vopTpls::path('tpl')."/$tpl.htm";
        if($retpl) return $tplFile;
        $cacheFile = vopTpls::path('tpc').'/'.$tpl.self::$tplCfg['tpc_ext']; 
        if(!file_exists($tplFile)) glbError::show("$tplFile NOT Exists!"); 
        comFiles::chkDirs($tpldir.'/'.$tpl,'ctpl'); 
        return array($tplFile, $cacheFile);
    }
    
}