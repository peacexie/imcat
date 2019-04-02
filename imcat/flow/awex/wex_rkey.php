<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
require __DIR__.'/_wex_cfgs.php';

$types = basLang::ucfg('cfgbase.wx_type');
$cfg = array(
    'sofields'=>array('keyword','detail','picurl'), //'appid',
    'soorders'=>array('keyword' => 'keyword(D)','keyword-a' => 'keyword(A)','top' => 'top(D)','top-a' => 'top(A)'),
    //'soarea'=>array('amount','数量'),
);
$tabid = 'wex_keyword'; //$weapp

if($view=='list'){ 

    $dop = new dopExtra($tabid,$cfg); 
    $dop->so->whrstr .= " AND `appid`='$weapp'";
    $dop->order = $dop->so->order = req('order','top-a'); 
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop'); 
        if(empty($fs) && in_array($fs_do,array('delete','clearact'))) $msg = lang('flow.dops_setitem');
        $cnt = 0; 
        if(empty($msg)){
          if($fs_do=='delete'){ 
              foreach($fs as $id=>$v){ 
                  $cnt += $dop->opDelete($id);
              } 
          }
        }
        $cnt && $msg = ($cnt>0) ? "$cnt ".lang('flow.dops_delok') : lang('flow.dops_opok');
    }
    
    $umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
    $links = $cv->Url(lang('awex.add').'&gt;&gt;',0,"$aurl[1]&view=form",lang('awex.addcfg')); //$links = admPFunc::fileNav('logs','sms');
    $dop->sobar(lang('awex.appid')."[$wekid] : ".lang('awex.kwset')." | $links {$umsg}",40,array(),array('wekid'=>$wekid));
    
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th><th>keyword</th><th>detail</th>"; 
    echo "<th>url</th><th>picurl</th><th>top</th><th>".lang('flow.title_edit')."</tr>\n";
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $kid = $idend = $r['kid'];
          if(empty($idfirst)) $idfirst = $kid;
          $keyword = $r['keyword']; if($keyword=='follow_autoreply_info') $keyword = '('.lang('awex.subscribe').')';
          echo "<tr>\n".$cv->Select($kid);
          echo "<td class='tc'>$keyword</td>\n";
          echo "<td class='tc'><input name='fm_[detail]' type='text' value='$r[detail]' class='txt w300' /></td>\n";
          echo "<td class='tc'><input name='fm_[url]' type='text' value='$r[url]' class='txt w120' /></td>\n";
          echo "<td class='tc'><input name='fm_[picurl]' type='text' value='$r[picurl]' class='txt w120' /></td>\n";
          echo "<td class='tc'>$r[top]</td>\n";
          echo $cv->Url(lang('flow.dops_edit'),1,"$aurl[1]&view=form&kid=$kid",lang('awex.ecfg'));
          //echo "<td class='tc'><a href='?awex-wex_rkey&kid=$r[kid]' target='_blank'>关键字</a></td>\n";
          echo "</tr>";
        }
        $dop->pgbar($idfirst,$idend,"delete|*".lang('awex.delkw')."");
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));
        
}elseif($view=='form'){
    
    $fm['appid'] = $weapp;
    if(!empty($bsend)){    
        if($kid=='is__add'){
            $kid = $fm['kid'];
            if($db->table($tabid)->where("kid='$kid'")->find()){
                $msg = lang('awex.uesed',$kid);
            }else{
                $msg = lang('flow.msg_add'); //$fm['type'] = 'test';
                $db->table($tabid)->data(basReq::in($fm))->insert();
            }
        }else{
            unset($fm['kid']);
            $db->table($tabid)->data(basReq::in($fm))->where("kid='$kid'")->update();
            $msg = lang('flow.msg_upd');
        } 
        basMsg::show($msg);    //,'Redir'?$mkv&mod=$mod
    }else{

        eimp('/~base/cssjs/weixin.js');
        if(!empty($kid)){
            $fm = $db->table($tabid)->where("kid='$kid'")->find();
        }else{
            $def = array('kid'=>'','keyword'=>'','detail'=>'','url'=>'','top'=>'88',);
            foreach($def as $k=>$v){ if(!isset($fm[$k])) $fm[$k] = $v; }        
        }
        $setType = "<label><input type=\"radio\" class=\"radio\" id=\"sence_1\" name='sence_k' onclick='wxKwdsetSence(1)' ".($fm['keyword']==='follow_autoreply_info' ? '' : 'checked').">".lang('awex.kwrep')."</label> &nbsp; &nbsp; \n"
                ."<label><input type=\"radio\" class=\"radio\" id=\"sence_0\" name='sence_k' onclick='wxKwdsetSence(0)' ".($fm['keyword']==='follow_autoreply_info' ? 'checked' : '').">".lang('awex.screp')."</label> \n";
        $itop = " &nbsp; ".lang('flow.title_top')."<input name='fm[top]' type='text' value='$fm[top]' class='txt w40' maxlength='5' reg='n+i' tip='".lang('admin.fad_tip25num')."'  />";
        echo "<div class='h02'>&nbsp;</div>";
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        if(!empty($kid)){
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='$kid' class='txt w150 disc' disabled='disabled' />$itop");
        }else{
            glbHtml::fmae_row(lang('flow.fl_kflag'),"<input name='fm[kid]' type='text' value='".basKeyid::kidTemp()."' class='txt w150' maxlength='24' reg='str:3-24' />$itop");
        }
        glbHtml::fmae_row(lang('awex.rescan'),"$setType");
        $tip = lang('awex.tip_rkey');
        glbHtml::fmae_row(lang('awex.kwd'),"<input name='fm[keyword]' id='fm[keyword]' type='text' value='$fm[keyword]' ".($fm['keyword']==='follow_autoreply_info' ? 'disabled' : '')." class='txt w320' maxlength='96' reg='str:2-96' tip='$tip' />");        
        glbHtml::fmae_row(lang('awex.cont'),"<textarea name='fm[detail]' rows='6' cols='50' wrap='wrap'>$fm[detail]</textarea>");
        glbHtml::fmae_row('Url',"<input name='fm[url]' type='text' value='$fm[url]' class='txt w320' maxlength='96' />");

        glbHtml::fmae_send('bsend',lang('flow.dops_send'),'25');
        glbHtml::fmt_end(array("kid|".(empty($kid) ? 'is__add' : $kid)));
        //echo basJscss::jscode('wxKwdsetSence(1,1);');
    }
    
}
?>