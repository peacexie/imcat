<?php
(!defined('RUN_INIT')) && die('No Init');
require(dirname(__FILE__).'/_wex_cfgs.php');
$_cbase['run']['sobarnav'] = '';

$types = basLang::ucfg('cfgbase.wx_type');
$cnav = basLang::ucfg('cfgbase.wx_nav');

$cfg = array(
    'sofields'=>array('kid','type','appid','api'),
    'soorders'=>array('kid' => 'kid(D)','kid-a' => 'kid(A)'),
    //'soarea'=>array('amount','数量'),
);
$tabid = 'wex_apps';

if($view=='list'){ 

    $dop = new dopExtra($tabid,$cfg); 
    $dop->order = $dop->so->order = req('order','kid-a'); 
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop'); 
        if(empty($fs) && in_array($fs_do,array('delete','clearact'))) $msg = lang('flow.dops_setitem');
        $cnt = 0; 
        if(empty($msg)){
          if($fs_do=='delete'){ 
              foreach($fs as $id=>$v){ 
                  wysBasic::clearCache($id);
                  $cnt += $dop->opDelete($id);
              } 
          }elseif($fs_do=='clearact'){ 
              foreach($fs as $id=>$v){
                  wysBasic::clearCache($id);
              } 
          }elseif($fs_do=='clrqrtik'){ //atime<'".($_cbase['run']['stamp']-86400)."'
              $db->table("wex_qrcode")->where("1=1")->delete(); //atime<'".($_cbase['run']['stamp']-5*60*144)."'
              $cnt--;
          }elseif(in_array($fs_do,array('locate','msgget','msgsend'))){ //432000=5day
              $db->table("wex_$fs_do")->where("atime<'".($_cbase['run']['stamp']-432000)."'")->delete(); 
              $cnt--;
          } 
          
        }
        $cnt && $msg = ($cnt>0) ? "$cnt ".lang('flow.dops_delok') : lang('flow.dops_opok');
    }
    
    $umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
    $links = $cv->Url(' | '.lang('awex.add').'&gt;&gt;',0,"$aurl[1]&view=form",lang('awex.addcfg'),480,360); //$links = admPFunc::fileNav('logs','sms');
    $dop->sobar(lang('awex.pids')." $links {$umsg}",50,array());

    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>ID</th><th>".lang('flow.title_type')."</th><th>".lang('awex.state')."</th><th>appid:".lang('awex.cfgs')."</th>"; //<th>活动时间</th>
    echo "<th>$cnav[menu]</th><th>$cnav[user]</th><th>$cnav[msg]</th><th>$cnav[kw]</th><th>$cnav[debug]</tr>\n"; //
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $kid = $idend = $r['kid'];
          if(empty($idfirst)) $idfirst = $kid;
          $typeu = $types[$r['type']]; 
          echo "<tr>\n".$cv->Select($kid);
          echo "<td class='tc'>$kid</td>\n";
          echo "<td class='tc' title='{$r['type']}'>$typeu</td>\n";
          echo "<td class='tc'>".glbHtml::null_cell($r['enable'])."</td>\n";
          #echo "<td class='tc'>".date('Y-m-d H:i',$r['acexp'])."</td>\n";
          // 配置
          echo $cv->Url($r['appid'],1,"$aurl[1]&view=form&kid=$kid",lang('awex.ecfg'),480,360);
          echo "<td class='tc'><a href='?file=awex/wex_menu&wekid=$r[kid]' target='_blank'>$cnav[menu]</a></td>\n";
          echo "<td class='tc'><a href='?file=awex/wex_user&wekid=$r[kid]' target='_blank'>$cnav[user]</a></td>\n";
          echo "<td class='tc'><a href='?file=awex/wex_msg3&wekid=$r[kid]' target='_blank'>$cnav[msg]</a></td>\n";
          echo "<td class='tc'><a href='?file=awex/wex_rkey&wekid=$r[kid]' target='_blank'>$cnav[kw]</a></td>\n";
          echo "<td class='tc'><a href='".PATH_ROOT."/a3rd/weixin_pay/wedebug.php?kid=$kid' target='_blank'>$cnav[debug]</a></td>\n";
          echo "</tr>";
        }
        $dop->pgbar($idfirst,$idend,lang('awex.ops_appid'));
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));
        
}elseif($view=='form'){

    if(!empty($bsend)){
        $appid = $fm['appid'];
        
        if($kid=='is__add'){
            $kid = $fm['kid'];
            if($db->table($tabid)->where("appid='$appid' OR kid='$kid'")->find()){
                $msg = lang('awex.uesed',"$appid/$fmkid");
            }else{
                $msg = lang('flow.msg_add'); //$fm['type'] = 'test';
                $db->table($tabid)->data(basReq::in($fm))->insert();
            }
        }else{
            unset($fm['kid']);
            $db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
            $msg = lang('flow.msg_upd');
        } 
        basMsg::show($msg);    //,'Redir'?file=$file&mod=$mod
    }else{

        if(!empty($kid)){
            $fm = $db->table($tabid)->where("kid='$kid'")->find();
        }else{
            $def = array('kid'=>'','type'=>'test','enable'=>'1','token'=>'','appid'=>'','appsecret'=>'','qrcode'=>'',);
            foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }        
        }
        $ienable = " &nbsp; <input name='fm[enable]' type='hidden' value='0' /><input name='fm_enable' type='hidden' value='$fm[enable]' />";
        $ienable .= lang('flow.title_enable')."<input name='fm[enable]' type='checkbox' class='rdcb' value='1' ".($fm['enable']=='1' ? 'checked' : '')." />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!empty($kid)){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$ienable");
        }else{
            $vstr = "url='".PATH_ROOT."/plus/api/wechat.php?actys=kidExists' tip='".lang('flow.fad_tip31245')."'";
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150' maxlength='12' reg='key:3-12' $vstr />$ienable");
        }
        
        glbHtml::fmae_row(lang('flow.title_type'),"<select id='fm[type]' name='fm[type]' type='text'>".basElm::setOption($types,$fm['type'])."</select>");
        glbHtml::fmae_row('token',"<input name='fm[token]' type='text' value='$fm[token]' class='txt w320' maxlength='96' reg='str:3-96' tip='".lang('awex.seewepf')."' />");
        $vstr = "url='".PATH_ROOT."/plus/api/wechat.php?actys=appidExists&oldval=".@$fm['appid']."' tip='eg:wx???,".lang('awex.seewepf')."'";
        glbHtml::fmae_row('appid',"<input name='fm[appid]' type='text' value='$fm[appid]' class='txt w320' maxlength='96' reg='str:3-96' $vstr/>");
        glbHtml::fmae_row('appsecret',"<input name='fm[appsecret]' type='text' value='$fm[appsecret]' class='txt w320' maxlength='96' reg='str:3-96' tip='".lang('awex.seewepf')."' />");
        glbHtml::fmae_row(lang('awex.qrcode'),"<input name='fm[qrcode]' type='text' value='$fm[qrcode]' class='txt w320' maxlength='96' />");

        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("kid|".(empty($kid) ? 'is__add' : $kid)));
    }
    
}
?>
