<?php
(!defined('RUN_INIT')) && die('No Init');

/**
    // re=stop,mark

data,tab,rule,plan
plan:subject,content
rule:单词至少N个,无中文字，有特殊字符N个,常用词少于N个，含非法字1，含非法字2，含非法字3
     words,      nochr,    spen,         topn,          keyn
tab:特殊字符1，特殊字符2，特殊字符3，
    常用词表en,常用词表cn,常用词表u1,
    常用词表en,常用词表cn,常用词表u1,
    广告,色情,暴力,反动,
    自定义1,自定义2,自定义3,
type:关键字类别
标记,N个处理

tab:特殊字符1，特殊字符2，特殊字符3，
    常用词表en,常用词表cn,常用词表u1,
    广告,色情,暴力,反动,
    自定义1,自定义2,自定义3,

----------------------------------------------------------------------------
safBase : 数据-安全过滤(Safil=Safety Filter)
  formAuto,formRev,formCheck,
  urlFrom,urlScan,urlStamp
/**

$content = safData::Check($content,'cont01',$mark=0);

ruleCheck()
rule*() ---- Stop,Mark
tab*()

*/
class safData{ // extends safBase
  
  // 常用单词,常用短语
  public static $top_en0 = 'about;after;all;also;an;and;another;any;are;as;at;be;because;been;before;being;between;both;but;by;came;can;come;could;did;do;each;for;from;get;got;had;has;have;he;her;here;him;himself;his;how;if;in;into;is;it;like;make;many;me;might;more;most;much;must;my;never;now;of;on;only;or;other;our;out;over;said;same;see;should;since;some;still;such;take;than;that;the;their;them;then;there;these;they;this;those;through;to;too;under;up;very;was;way;we;well;were;what;where;which;while;who;with;would;you;your';
  public static $top_cn0 = '怎么;任何;连同;开外;再有;哪些;甚至于;又及;当然;就是;遵照;以来;赖以;否则;此间;后者;按照;才是;自身;再则;就算;即便;有些;例如;它们;虽然;为此;以免;别处;我们;依据;趁着;就要;各位;别的;前者;不外乎;虽说;除此;个别;的话;甚而;那般;譬如;作为;谁人;进而;那边;首先;因此;怎么样;果然;除非;以上;为何;要么;随时;如果说;诸如;还是;一旦;基于;本人;因而;继而;不单;此时;等等;截至;不但;故而;全体;从此;对于;朝着;怎样;以为;那儿;或是;本身;况且;处在;不至于;那个;诸位;从而;各自;针对;此外;何处;为了;这般;仍旧;既然;反而;关于;较之;不管;彼时;这边;不光;宁可;要是;其他;其它;由于;还要;经过;不过;来说;除了;既是;的确;说来;据此;只限于;什么的;还有;只怕;不尽;多会;正巧;为什么;以至;以致;某个;与否;凭借;不仅;两者;另外;一来;正如;那里;不尽然;毋宁;这儿;嘿嘿;就是说;正是;既往;随着;于是;那么;而后;似的;不料;其余;或者;介于;别人;这个;受到;只是;即使;不论;本着;及至;加以;多么;其中;别说;这会;依照;人们;如此;个人;出来;另一方面;唯有;接着;何况;加之;至今;凡是;他们;一切;那时;只限;不然;许多;在于;某某;除外;来自;便于;同时;只消;只需;不如;只要;并不;不仅仅;这里;总之;因为;固然;不是;或者说;然而;假如;如何;这么;可见;如果;简言之;多少;光是;非但;呵呵;只有;只因;连带;正值;沿着;哪儿;他人;若非;怎么办;她们;而且;与其;如同下;有的;那些;甚至;为止;无论;鉴于;嘻嘻;哪个;然后;直到;并非;对比;为着;一些;何时;而是;自从;比如;之所以;你们;那样;所以;得了;当地;有关;所有;因之;用来;所在;对待;而外;分别;某些;对方;不只;但是;全部;尽管;大家;以便;自己;可是;反之;这些;什么;由此;万一;而已;何以;咱们;值此;向着;哪怕;倘若;出于;如上;如若;替代;什么样;如是;照着;此处;这样;每当;此次;至于;此地;要不然;逐步;格里斯;本地;要不;其次;尽管如此;遵循;乃至;若是;并且;如下;可以;才能;以及;彼此;根据;随后;有时';
  //const CHARSET = "中国";
  // $key = "top_en0"; //die($key2);
  // echo self::$$key; //$key='top_en0'
  
  
  // === plans ==================================================================================
  
  // plan: xxx -------------------------- 
  static function planMain($str,$plan='',$nmax=0){ 
      $_safil = read('sfdata','ex');
      if(empty($str) || empty($_safil['plan'][$plan])) return;
      $plan = $_safil['plan'][$plan];
      $n = 0; //Words,4,mark
      foreach($plan as $rule){
          $t = explode(',',"$rule,,,,");
          $func = "rule$t[0]";
          $n += self::$func($t[1],$t[2],$t[3],$t[4]); 
      }
      if($nmax && $n>$nmax){
          safBase::Stop('dataPlan');    
      }
      return $n;
  }
    
    // === rules ==================================================================================

    // rules: 单词>=N个 -------------------------- 
    static function ruleWords($str,$n=1,$re='mark'){ 
        $str = strip_tags($str); //$str = htmlspecialchars($str);
        $str = trim($str); //去掉开始和结束的空白 
        if(empty($str)) return $re=='stop' ? safBase::Stop('dataWords') : 0;
        $str = preg_replace('/\s(?=\s)/', '', $str); //去掉根随别的挤在一块的空白 
        $str = preg_replace('/[\n\r\t]/', ' ', $str); //最后，去掉非space 的空白，用一个空格代替 
        if(empty($str)) return $re=='stop' ? safBase::Stop('dataWords') : 0;
        $cnt = count(explode(' ',$str));
        if($cnt<$n && $re=='stop'){
             safBase::Stop('dataWords');
        }else{
            return $cnt<$n ? 0 : 1;
        }
    }
    
    // rules: 中文字>=N个 -------------------------- 
    static function ruleCNChr($str,$n=1,$re='mark'){ 
        $cset = $_cbase['sys']['cset'];
        $str = strip_tags($str);
        $str = trim($str);
        //preg_match_all("/[\x{4e00}-\x{9fa5}]/u",$str,$m); 
        //$cnt = count($m[0]);
        $n0 = strlen($str);
        $str = preg_replace('/[\x{4e00}-\x{9fa5}]/u',"",$str);
        $cnt = ($n0 - strlen($str))/($cset=='utf-8' ? 3 : 2); 
        if($cnt<$n && $re=='stop'){
             safBase::Stop('dataCNChr');
        }else{
            return $cnt<$n ? 0 : 1;
        } //preg_replace 比 preg_match_all 快
    }
    
    // rules: 常用词>N个 -------------------------- 
    static function ruleNCom($str,$n=1,$re='mark',$tab='cn'){ 
        $_safil = read('sfdata','ex');
        $str = strip_tags($str);
        $str = trim($str);
        if($tab=='cn'){
            $tab = self::$top_cn0;
        }else{
            $tab = self::$top_en0;
            $flag = '[^a-zA-Z]{1,2}';
            $tab = str_replace(';',"$flag;$flag",$tab); 
            $tab = "$flag$tab$flag";
        }
        $tab = $tab.";".$_safil['tab_sys'];
        $tab = str_replace(';','|',$tab);
        preg_match_all("/$tab/i",$str,$m); echo '<br>'; print_r($m);
        $cnt = count($m[0]);
        if($cnt<$n && $re=='stop'){
             safBase::Stop('dataNCom');
        }else{
            return $cnt<$n ? 0 : 1;
        } 
    }
    
    // rules: 含有特殊字符 -------------------------- 
    static function ruleNSpe($str,$re='mark',$tab='(all)'){ 
        $str = strip_tags($str); 
        $str = trim($str); 
        $rk = self::_ruleTab('spe',$tab); 
        if(empty($rk)) return 0;
        $rk = str_replace(';','|',$rk);
        preg_match("/$rk/i",$str,$m); echo '<br>'; 
        if($m && $re=='stop'){
             safBase::Stop('dataNSpe');
        }else{
            return $m ? 1 : 0;
        } 
    }
    
    // rules: 含有非法字 -------------------------- 
    static function ruleNKey($str,$re='stop',$tab='(all)'){ 
        $str = trim($str); 
        $rk = self::_ruleTab('key',$tab); 
        if(empty($rk)) return 0;
        $rk = str_replace(';;',';',$rk); 
        $rk = str_replace(array("?"),array("[^\1]{0,3}"),$rk); 
        $rk = str_replace(';','|',$rk);
        preg_match("/$rk/i",$str,$m); echo '<br>'; 
        if($m && $re=='stop'){
             safBase::Stop('dataNKey');
        }else{
            return $m ? 1 : 0;
        } 
    }
    
    // rules: 含有广告连接 -------------------------- 
    static function ruleNAdv($str,$re='stop',$tab='(all)'){ 
        $str = trim($str); 
        $rk = self::_ruleTab('adv',$tab); 
        if(empty($rk)) return 0;
        $rk = str_replace(';;',';',$rk); // ?,+ <a*</a>;[url]*[/url];[link]*[script];
        $rk = str_replace(array("[","]","<",">","/"),array("\\[","\\]","\\<","\\>","\\/"),$rk); 
        $rk = str_replace(array("*"),array("[^\1]{8,255}"),$rk); 
        $rk = str_replace(';','|',$rk);
        preg_match("/$rk/i",$str,$m); echo '<br>'; 
        if($m && $re=='stop'){
             safBase::Stop('dataNAdv');
        }else{
            return $m ? 1 : 0;
        } 
    }
    
    // rules-tab: 得到关键字表 -------------------------- 
    static function _ruleTab($key='key',$tab='(all)'){ 
        $_safil = read('sfdata','ex');
        $rk = '';
        if($tab=='(all)'){
            $t = $_safil["tab_$key"];
            foreach($t as $k=>$v) $rk .= ";$v[1]";
        }else{
            $rk = $_safil["tab_$key"][$tab][1];    
        }
        return empty($rk) ? '' : substr($rk,1);    
    }
    
    // === ... ======================================================================================
    
    

    // XXX
    static function XXXXXX($act='init'){  
        $safix = cfg('safe.safix');
        if($act=='init'){

        }else{
            ;//
        }
        //echo self::$top_cn0;
    }
        
    // === End ====================================================================================
    
}

