<?php
(!defined('RUN_INIT')) && die('No Init'); 

include(DIR_CODE.'/lang/pubs/fcfgs.php'); 
$navStr = $msg = ''; $tbexd = 'topic_items';
$part = req('part'); 
$isadd = req('isadd');
$dno = req('dno');
$order = req('order'); 
if(strpos($order,'-a')){
    $order = str_replace('-a','',$order);
}elseif($order){
    $order = $order.' DESC';
}else{ $order = 'top,dno'; }

if($did){
    $fmo = $db->table($dop->tbid)->where("did='$did'")->find(); 
    $fme = $db->table($dop->tbext)->where("did='$did'")->find(); 
    if($view){
        $fcfg = devTopic::cfg2arr(@$fme[$view]); 
    }
    if(!in_array($view,array('form','list'))){
        $navStr = devTopic::navBar($fcfgs,$fme,$view,$part); 
        echo "<div style='max-width:960px; margin:auto;'>\n";
        $link = dopFunc::vgetLink($fmo['title'],'topic',$did);
        echo "<h3 class='tc fB pa10' style='margin:auto;'>专题:$link</h3>\n";
    }
}

if($view=='clear'){
    $msg = lang('flow.dops_clearok');
    /*if($mod=='coitem'){
        $pids = glbDBExt::getKids('xxxcorder','title','1=1'); 
        $db->table($dop->tbid)->where("ordid NOT IN($pids)")->delete(); 
    }else{
        $db->table($dop->tbid)->where("atime<'".($_cbase['run']['stamp']-3*86400)."'")->delete(); 
    }*/
    $view = 'list';
}

if($act=='iform'){

    if(!empty($bsend)){
        $fmv = $_POST['fm'];
        $tags = basReq::arr('tags','Html');
        if(!empty($tags)){
            $fmv['tags'] = comParse::jsonEncode($tags);
        }
        $fmv = devTopic::moveTmpFiles($fmv,'topic',$did);
        if(!empty($isadd)){ // basReq::in()
            $fmv['did'] = $did; 
            $fmv['dno'] = in_array($view,array('clist','cvote')) ? devTopic::getDno($did) : $part; 
            $fmv['part'] = $part; 
            $db->table($tbexd)->data(in($fmv))->insert('e'); 
            $actm = lang('flow.dops_add');
        }else{ // replace
            $db->table($tbexd)->data(in($fmv))->where("did='$did' AND dno='$dno'")->update('e');
            $actm = lang('flow.dops_edit');
        }
        $dop->svEnd($did); //静态情况等
        basMsg::show("$actm".lang('flow.dops_ok'),'Redir');
    }else{
        if(!empty($did) && !empty($dno)){
            $fmv = $db->table($tbexd)->where("did='$did' AND dno='$dno'")->find();
            $isadd = 0;
        }else{
            $fmv = array();
            $isadd = 1;
        }
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        devTopic::fmlist($fmv,$fme,$view,$part,$fmv); // fields
        glbHtml::fmae_send('bsend',lang('flow.dops_send'));
        glbHtml::fmt_end(array("mod|$mod","did|$did","isadd|$isadd"));
    }

}elseif($view=='formd'){

    echo $navStr; 
    $cfg = array(
        'sofields'=>array('mname','mtel','detail'),
        'soorders'=>array(
            'atime' => '操作时间(降)',
            'atime-a' => '操作时间(升)',
        ),
        'soarea'=>array(),
        'kid'=>$mod,
    );
    $dop = new dopExtra('topic_form',$cfg); 
    //dump($dop->so->whrstr);
    $dop->so->whrstr .= " AND `did`='$did'";
    // 删除操作
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.dops_setitem');
        $cnt = 0; 
        if(empty($msg)){
            foreach($fs as $id=>$v){ //echo "did='$did' AND kid='$id'<br>";
                if($fs_do=='del'){ 
                    $db->table('topic_form')->where("did='$did' AND kid='$id'")->delete(); 
                }elseif($fs_do=='upd'){ // ?
                    $db->table('topic_form')->data(basReq::in($fm[$id]))->where("did='$did' AND kid='$id'")->update(0);
                }elseif($fs_do=='chk1' || $fs_do=='chk0'){ 
                    $data = array('show'=>$fs_do=='chk0'?0:1);
                    $db->table('topic_form')->data($data)->where("did='$did' AND kid='$id'")->update(0);
                }
                $cnt++;
            } 
        }        
    }
    $umsg = $msg ? "<br><span class='cF00'>$msg</span>" : '';
    $dop->sobar("表单数据$umsg",40,array());
    // 清理操作
    if(!empty($bsend)&&$fs_do=='dnow'){
        $msg = $dop->opDelnow();
        basMsg::show($msg,'Redir',"?mkv=$mkv&mod=topic&did=$did&view=formd&flag=v1");
    }
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>".lang('flow.title_select')."</th>";
    echo "<th>mname</th><th>mtel</th><th>time</th><th>ip</th><th>V</th><th>Edit</th>";
    echo "</tr>\n";
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
          $kid = $idend = $r['kid'];
          if(empty($idfirst)) $idfirst = $kid;
          echo "<tr>\n".$cv->Select($kid); 
              $detail = $r['detail'];
              $chk = $r['show'] ? 'Y' : 'N';
              echo "<td class='tl'>$r[mname]</td>\n";
              echo "<td class='tc'>$r[mtel]</td>\n";
              echo "<td class='tc'>".date('m-d H:i:s',$r['atime'])."</td>\n";
              echo "<td class='tc'><input type='text' value='$r[aip]' class='txt w120'/></td>\n";
              echo "<td class='tc'>$chk</td>\n"; // edit
              echo "<td class='tc'>Edit</td>\n";
          echo "</tr>\n<tr>\n<td colspan=6><textarea name='fm[$kid][detail]' class='wp100' rows=2>$detail</textarea></td></tr>\n";
        }
        $dop->pgbar($idfirst,$idend,"del|删除\nupd|更新\nchk1|显示\nchk0|隐藏"); // 
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }    
    glbHtml::fmt_end(array("mod|$mod","part|$part"));

}elseif($view=='list'){

    if(!empty($bsend)){
        require dopFunc::modAct($_scdir,'list_do',$mod,$dop->type);
        // ext-del:topic_items
        $msg = "$cnt ".lang('flow.dops_okn',$msgop);
        if(empty($fs) && $fs_do=='del'){
          foreach($fs as $id=>$v){ 
            $this->db->table($tbexd)->where("pid='$id'")->delete();
          }
        }
    } 
    require dopFunc::modAct($_scdir,'list_show',$mod,$dop->type);
    // ext-set:data
    $link = "<a href='?mkv=dops-a&mod=topic&did={id}&view=cfgs' target='_blank'>资料管理</a>";
    echo basJscss::jscode("setColstr('tblist',-2,'资料管理',\"$link\");");
    
}elseif($view=='form'){

    if(!empty($bsend)){
        require dopFunc::modAct($_scdir,'form_do',$mod,$dop->type);
    }else{
        require dopFunc::modAct($_scdir,'form_show',$mod,$dop->type);
    }

}elseif($view=='tpls'){

    $tab = devTopic::tplLists();
    $list = "\n<ul class='mh10' id='tplists'>\n"; $pid = "-";
    foreach ($tab as $fp) {
        if(strpos($fp,'~') || !strpos($fp,'.htm')) continue;
        $tmp = explode('/',$fp);
        if($tmp[0]!=$pid){
          $pid = $tmp[0];
          $list .= "<li class='fB pv5'>{$_cbase['topic']['tpldir']}/$pid/</li>\n";
        }
        $tpl = str_replace('.htm','',$fp);
        $list .= "<li class='f14 pv5 hand'><span class='c00F'>$tpl</span>.htm</li>\n";
    }
    echo $list.'</ul>';
    $js = "\nvar wid = parent.layer.getFrameIndex(window.name);\n";
    $js .= "$('#tplists li').on('click',function(){\n";
    $js .= "  parent.\$('#cfgs_tplname_').val($(this).find('span:first').html());\n";
    $js .= "  parent.layer.close(wid);\n";
    $js .= "});\n";
    echo basJscss::jscode($js);
    $_cbase['debug']['err_mode'] = 0;

}elseif($view=='cfgs'){

    echo $navStr; 
    if(!empty($bsend)){
      $cfgs = basReq::arr('cfgs','Html'); //dump($cfgs);
      $db->table($dop->tbext)->data(basReq::in($cfgs))->where("did='$did'")->update(0);
      basMsg::show('设置成功!','Redir');  
    }else{
        glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
        $inp = "<a class='right' href='?mkv=dops-a&mod=topic&view=tpls' onclick='return winOpen(this,\"模板浏览\",300,450);'>模板</a>";
        $inp .= "<input id='cfgs_tplname_' name='cfgs[tplname]' type='text' value='{$fme['tplname']}' class='txt' />";
        echo "<tr ><td class='tc'>模板</td><td class='tl'>$inp</td></tr>"; // <br>(tplname)
        foreach ($fcfgs as $key => $tim) {
            $cfg = str_replace(array("\t",' '), '', $tim[0]); // ,"'",'"'
            $val = empty($fme[$key]) ? '' : $fme[$key]; // y1617
            echo "<tr ><td class='tc'>{$tim['title']}<br>({$key})</td>
              <td class='tl'><textarea style='width:100%'; rows='5' wrap='off'
              id='cfgs[{$key}]' name='cfgs[{$key}]' placeholder='{$cfg}' 
              />$val</textarea></td></tr>";
        }
        glbHtml::fmae_send('bsend',lang('flow.dops_send'));
        glbHtml::fmt_end(array("mod|$mod","did|$did"));
    }

}elseif($view=='crels'){

    if(!empty($bsend)){
        $crels = $_POST['crels'];
        foreach ($crels as $k2 => $rows) {
            $fmv = array();
            $fmv['did'] = $did;
            $fmv['dno'] = $fmv['part'] = $k2;
            $fmv['title'] = $fcfg[$k2];
            $data = array();
            foreach ($rows as $k3 => $row) {
                $data[] = $row;
            }
            $fmv['detail'] = empty($data) ? '' : comParse::jsonEncode($data);
            $db->table($tbexd)->data(in($fmv))->replace(0); 
        }
    }

    echo $navStr; 
    echo "<script src='".PATH_SKIN."/adm/b_jscss/finps.js'></script>";
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
    echo "<tr><th>项目</th><th>设置</th></tr>";
    $jsb = $jsc = "";
    foreach ($fcfg as $k2=>$title) {
        if(devTopic::skip($k2)) continue;
        echo "<tr><td class='tc'>$title<br>($k2)</td><td id='{$k2}Box'>";
        $data = $db->table($tbexd)->where("did='$did' AND dno='$k2'")->find();
        $rows = json_decode($data['detail'],1); 
        if(!empty($rows )){
        foreach ($rows as $key => $row) {
            if(empty($row['name'])&&empty($row['url'])) continue;
            echo "
                <div style='padding:5px; margin:5px; border:1px solid #CCC;'>
                名称: <input type='text' class='wp80' name='crels[$k2][$key][name]' value='{$row['name']}'><br>
                链接: <input type='text' class='wp80 mv5' name='crels[$k2][$key][url]'  value='{$row['url']}'><br>
                描述: <input type='text' class='wp80' name='crels[$k2][$key][des]'  value='{$row['des']}'>
                </div>
            ";
        } }
        echo "</td></tr>\n";
        $jsb .= "<script id='{$k2}Tpl' type='text/html'></script>\n";
        $jsc .= "$('#{$k2}Tpl').append(tpl.replace(/r_keys/g,'{$k2}'));mitmInit('{$k2}');\n";
    }
    glbHtml::fmae_send('bsend',lang('flow.dops_send'));
    glbHtml::fmt_end(array("mod|topic","did|$did"));
    echo "
        <script id='itmTpl' type='text/html'>
        <div style='padding:5px; margin:5px; border:1px solid #CCC;'>
        名称: <input type='text' class='wp80' name='crels[r_keys][no_1][name]'><br>
        链接: <input type='text' class='wp80 mv5' name='crels[r_keys][no_1][url]'><br>
        描述: <input type='text' class='wp80' name='crels[r_keys][no_1][des]'>
        </div></script>
        $jsb
        <script>$(function(){ 
            var tpl = $('#itmTpl').html();
            $jsc
        });</script>
    ";

}elseif($view=='cform'){

    if(!empty($bsend)){
        $cform = $_POST['cform'];
        $tags = $_POST['tags'];
        foreach ($cform as $k2 => $rows) {
            $fmv = array();
            $fmv['did'] = $did;
            $fmv['dno'] = $fmv['part'] = $k2;
            $fmv['title'] = $fcfg[$k2];
            $data = $rows;
            $fmv['detail'] = empty($data) ? '' : comParse::jsonEncode($data);
            $fmv['tags'] = $tags[$k2];
            $db->table($tbexd)->data(in($fmv))->replace(0); 
        }
    }

    echo $navStr; 
    $tmps = $db->table($tbexd)->where("did='$did'")->select(); # AND dno='$k2'
    $datas = array();
    foreach ($tmps as $kd=>$vd) { $datas[$vd['dno']]=$vd; }
    echo "<script src='".PATH_SKIN."/adm/b_jscss/finps.js'></script>";
    glbHtml::fmt_head('fmlist',"$aurl[1]",'tbdata');
    echo "<tr><th>表单项目/标记</th><th>选项</th><th>票数</th><th>描述</th></tr>";
    $jsb = $jsc = "";
    foreach ($fcfg as $k2=>$title) {
        if(devTopic::skip($k2)) continue; 
        $data = isset($datas[$k2]) ? $datas[$k2] : array('detail'=>'', 'tags'=>'');
        $row = json_decode($data['detail'],1); 
        $flags = "<input type='text' class='wp80' name='cform[$k2][flags]' value='{$row['flags']}' placeholder='如: `s,must`, `m,` '>";
        echo "<tr><td class='tc'>$title<br>($k2)<br>$flags</td>";
        echo "<td><textarea class='wp100' name='cform[$k2][name]' rows=6 wrap='off'>{$row['name']}</textarea></td>";
        echo "<td><textarea class='wp100' name='tags[$k2]'  rows=6 wrap='off'>{$data['tags']}</textarea></td>";
        echo "<td><textarea class='wp100' name='cform[$k2][des]'  rows=6 wrap='off'>{$row['des']}</textarea></td>";
        echo "</tr>\n";
    }
    $msg = '标记说明: `s,m,i,a,` 分别表示 `单选,多选,填空,问答`, `must` 表示必选项,';
    glbHtml::fmae_send('bsend',lang('flow.dops_send'),0,"tc' colspan=2>$msg<td class=tc></td");
    glbHtml::fmt_end(array("mod|$mod","did|$did"));

}elseif(in_array($view,array('ctext','chtml','cpics','cmedia'))){

    echo $navStr; 
    echo "<table border='1' class='table tblist'>";
    echo "<tr><th>Key</th><th>标题</th><th class='hidden-xs'>图片</th><th>添加/修改</th></tr>";
    foreach ($fcfg as $k2=>$title) {
        if(devTopic::skip($k2)) continue;
        $data = $db->table($tbexd)->where("did='$did' AND dno='$k2'")->find();
        if(!empty($data)){
            $dno = $data['dno'];
            $dtitle = '修改';
        }else{
            $dno = '';
            $dtitle = '添加';
        }
        $title = devTopic::vTitle($data,$title);
        $mpic = devTopic::vMpic($data);
        $lnkurl = "?mkv=$mkv&mod=$mod&did=$did&dno=$dno&act=iform&view=$view&part=$k2&recbk=ref";
        $lnkset = $dop->cv->Url($dtitle,0,$lnkurl,"{$dtitle}资料");
        $mpic = "<td class='tc hidden-xs'>$mpic</td>\n";
        echo "<tr class='tc'><td>$k2</td><td>$title</td>$mpic<td>$lnkset</td></tr>\n";
    }
    echo "</table>";

}elseif(in_array($view,array('clist','cvote'))){

    echo $navStr; 
    $fs_do = req('fs_do');
    $fs = basReq::arr('fs'); 
    $msg = ''; $cnt = 0; 
    if(!empty($bsend)){
        if(empty($fs_do)) $msg = lang('flow.dops_setop');
        if(empty($fs)) $msg = lang('flow.dops_setitem');
        $cnt = 0; 
        if(empty($msg)){
            foreach($fs as $id=>$v){ 
                if($fs_do=='del'){ 
                    $db->table($tbexd)->where("did='$did' AND dno='$id'")->delete(); 
                }elseif($fs_do=='upd'){ 
                    $db->table($tbexd)->data(basReq::in($fm[$id]))->where("did='$did' AND dno='$id'")->update();
                }elseif($fs_do=='chk1' || $fs_do=='chk0'){ 
                    $data = array('show'=>$fs_do=='chk0'?0:1);
                    $db->table($tbexd)->data($data)->where("did='$did' AND dno='$id'")->update();
                }
                $cnt++;
            } 
        }
    }

    $cnt && $msg = "$cnt 操作成功!";

    $cfg = array(
        'kid'=>'topic',
        'sofields'=>array('title'),
        'soorders'=>array(
            'top' => '排序(降)',
            'top-a' => '排序(升)',
            'vote' => '票数(降)',
            'vote-a' => '票数(升)',
        ),
        'order'=>($order ? "$order,dno" : 'top,dno'),
        'title'=>$fcfg[$part],
    );
    $dop = new dopExtra($tbexd,$cfg); 

    $dop->so->whrstr .= " AND `did`='$did' AND part='$part'"; 
    $lnkurl = "?mkv=$mkv&mod=$mod&did=$did&act=iform&view=$view&part=$part&recbk=ref";
    $lnkadd = $dop->cv->Url(basLang::show('flow.dops_add2').'&gt;&gt;',0,$lnkurl,"添加资料");
    $dop->sobar($dop->msgBar($msg,$lnkadd),30,$cfg['soorders']);

    glbHtml::fmt_head('fmlist',"$aurl[1]",'tblist');
    echo "<th>选择</th><th>标题</th><th class='hidden-xs'>图片</th><th class='hidden-xs'>top</th><th class='hidden-xs'>vote</th><th>审</th><th>修改</th></tr>\n";
    $idfirst = ''; $idend = '';
    if($rs=$dop->getRecs()){ 
        foreach($rs as $r){ 
            $kid = $idend = $r['dno'];
            if(empty($idfirst)) $idfirst = $kid;
            $title = devTopic::vTitle($r);
            $mpic = devTopic::vMpic($r);
            $lnkurl = "?mkv=$mkv&mod=$mod&did=$did&dno=$kid&act=iform&view=$view&part=$part&recbk=ref";
            $lnkset = $dop->cv->Url('修改',0,$lnkurl,"修改资料");
            $top = "<input name='fm[$kid][top]' type='text' value='$r[top]' class='txt w40' />\n";
            $vote = "<input name='fm[$kid][vote]' type='text' value='$r[vote]' class='txt w40' />\n";
            $chk = $r['show'] ? 'Y' : 'N';
            echo "<tr>\n".$cv->Select($kid);
            echo "<td class='tc'>$title</td>\n";
            echo "<td class='tc hidden-xs'>$mpic</td>\n";
            echo "<td class='tc hidden-xs'>$top</td>\n"; // edit
            echo "<td class='tc hidden-xs'>$vote</td>\n"; // edit
            echo "<td class='tc'>$chk</td>\n"; // edit
            echo "<td class='tc'>$lnkset</td>\n"; // ".date('Y-m-d H:i',$r['etime'])."
            echo "</tr>";
        }
        $dop->pgbar($idfirst,$idend,"del|删除\nupd|更新\nchk1|显示\nchk0|隐藏"); // 
    }else{
        echo "\n<tr><td class='tc' colspan='15'>".lang('flow.dops_nodata')."</td></tr>\n";
    }
    glbHtml::fmt_end(array("mod|$mod"));
    
}

if($did && !in_array($view,array('form','list'))){
    echo "\n</div>\n\n";
}

/*

*/
