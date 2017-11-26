<?php
// 模板编译 类
class vopComp{
    
    static $tplCfg = array(); //配置
    
    static function main($tplname) {
        $vob = new self(); 
        $vob->build($tplname); 
        unset($vob); 
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
        $content = comFiles::get($re[0]);
        $content = $this->bcore($content); //获取经编译后的内容
        $shead = "(!defined('RUN_INIT')) && die('No Init'); \n\$this->tagRun('tplnow','$tpl','s');";
        $fptex = '/b_func/tex_base.php'; $spend = '';
        if(file_exists(vopTpls::path().$fptex)){
            $shead .= "\ninclude_once vopTpls::path().'$fptex';";
            $shead .= "\nif(method_exists('tex_base','init')){ tex_base::init(\$this); }";
            $spend = "<?php\nif(method_exists('tex_base','pend')){ tex_base::pend(); }\n?>";
        }
        comFiles::put($re[1], "<?php \n$shead \n?>\n".$content.$spend); //写入缓存
        return $re[1];
    }

    //模板编译核心
    function bcore($stpl=''){
        $stpl = self::impBlock($stpl); // 解析模板继承
        $stpl = self::incTpls($stpl); // 解析{inc},
        $stpl = self::phpBasic($stpl); //基本php语法解析
        $stpl = self::phpFlow($stpl); //流程控制语句
        $stpl = self::incCodes($stpl); // 解析{code}
        $stpl = vopCTag::tagMain($stpl); //系统标签解析
        return $stpl;
    }

    // 解析{inc},
    function incTpls($stpl=''){ 
        preg_match_all("/{inc:\"(.*)\"}/ie", $stpl, $match); 
        if(count($match[1])>0){ //解析模板包含
            $arr = $match[1]; 
            foreach($arr as $tpl){
                $pfile = vopTpls::pinc($tpl,self::$tplCfg['tpl_ext']); 
                $ptpl = $this->incTpls(comFiles::get($pfile)); 
                if(empty($ptpl)) { $ptpl = "<!-- {inc:`$tpl`} -->"; }
                $stpl = str_replace("{inc:\"$tpl\"}", $ptpl, $stpl);
                $stpl = $this->incTpls($stpl);
            }
        }
        return $stpl;
    }

    // 解析{code}
    function incCodes($stpl=''){
        preg_match_all("/{code:\"(.*)\"}/ie", $stpl, $match); 
        if(count($match[1])>0){ //解析模板包含 
            $arr = $match[1]; 
            foreach($arr as $tpl){
                $pfile = "vopTpls::pinc('$tpl','.php')"; 
                $stpl = str_replace("{code:\"$tpl\"}", "<?php include $pfile; ?>", $stpl);
            }
        }
        return $stpl;
    }

    // 模板继承extend,block,layout,parent,inherit
    // {imp:"c_layout/news"] // {block:title]Welcome!{/block:title] // {block:title] {:parent} {:clear} News - Project Name{/block:title]
    function impBlock($stpl=''){
        preg_match("/\{imp:\"([\S]{3,48})\"\}/ie", $stpl, $match);
        if(empty($match[0]) || empty($match[1])) return $stpl; //没有imp,原样返回
        /*if(strpos($match[1],'[-mob]') && !basEnv::isMobile()){
            $match[1] = str_replace('[-mob]','',$match[1]);
        }*/
        $layout = vopTpls::pinc($match[1],self::$tplCfg['tpl_ext']); 
        $layout = comFiles::get($layout);
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
            {outSwplayerPath}/ -=> http://cdn_d/vimp/vendui/swplayer/
        */
        $reps = glbConfig::read('repath', 'ex');
        if(!empty($reps['tpl'])){
            $stpl = str_replace(array_keys($reps['tpl']), array_values($reps['tpl']), $stpl);
        }
        /* 常量/变量解析 ----------------------------
            {=PATH_ROOT} -=> <?php echo PATH_ROOT; ?>
            {=$name} -=> <?php  echo $name; ?> $re1['title'], $this->mod, $this->ucfg['q']
        */
        $stpl = preg_replace ( "/\{\=([A-Z_][A-Z0-9_]*)\}/s", "<?php echo \\1;?>", $stpl );    
        /*将变量{$name}替换成<?php  echo $name; ?>,可以是数组 $name, $re1['title'], $this->mod, $this->ucfg['q'] */
        $stpl = preg_replace("/{\=(\\$[a-zA-Z_][\w\.\"\'\[\]\$\-\>\:]{0,64})}/i", "<?php echo @$1; ?>", $stpl);
        /* php标签 ----------------------------
            {= date('Y-m-d'); }     =>  <?php echo date('Y-m-d'); ?>
            {php echo phpinfo();}    =>    <?php echo phpinfo(); ?>
         */
        $stpl = preg_replace ( "/\{=\s+([^}]+)\}/", "<?php echo \\1?>", $stpl );
        $stpl = preg_replace ( "/\{php\s+([^}]+)\}/", "<?php \\1?>", $stpl );
        return $stpl;
    }
    
    //模板解析 流程控制语句(Flow control statements)
    static function phpFlow($stpl){
        /* if 标签
            {if $name==1}        =>    <?php if ($name==1){ ?>
            {elseif $name==2}    =>    <?php } elseif ($name==2){ ?>
            {else}                =>    <?php } else { ?>
            {/if}                =>    <?php } ?>
        */
        $stpl = preg_replace ( "/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $stpl );
        $stpl = preg_replace ( "/\{else\}/", "<?php } else { ?>", $stpl );
        $stpl = preg_replace ( "/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $stpl );
        $stpl = preg_replace ( "/\{\/if\}/", "<?php } ?>", $stpl );
        // ----------------------------
        /* for 标签
            {for $i=0;$i<10;$i++}    =>    <?php for($i=0;$i<10;$i++) { ?>
            {/for}                    =>    <?php } ?>
        */
        $stpl = preg_replace("/\{for\s+(.+?)\}/","<?php for(\\1) { ?>",$stpl);
        $stpl = preg_replace("/\{\/for\}/","<?php } ?>",$stpl);
        // ----------------------------
        /* loop 标签
            {loop $arr $vo}            =>    <?php $n=1; if (is_array($arr) foreach($arr as $vo){ ?>
            {loop $arr $key $vo}    =>    <?php $n=1; if (is_array($array) foreach($arr as $key => $vo){ ?>
            {/loop}                    =>    <?php $n++;}unset($n) ?>
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
        $tpldir = $_cbase['tpl']['tpl_dir']; 
        $tplFile = vopTpls::path('tpl').'/'.$tpl.self::$tplCfg['tpl_ext'];
        if($retpl) return $tplFile;
        $cacheFile = vopTpls::path('tpc').'/'.$tpl.self::$tplCfg['tpc_ext']; 
        if(!file_exists($tplFile)) glbError::show("$tplFile NOT Exists!"); 
        comFiles::chkDirs($tpldir.'/'.$tpl,'ctpl'); 
        return array($tplFile, $cacheFile);
    }
    
}