<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init'); 

$msg = ''; $tabext = '';
if($view=='list'){
    if(!empty($bsend)){
        require dopFunc::modAct('list_do',$mod,$dop->type);
    } //$dop->whrstr = " AND "; $_mpid,
    require dopFunc::modAct('list_show',$mod,$dop->type);
}elseif($view=='form'){
    if(!empty($bsend)){
        require dopFunc::modAct('form_do',$mod,$dop->type);
    }else{
        require dopFunc::modAct('form_show',$mod,$dop->type);
    }
}elseif($view=='set'){
    ;//utf-8编码
}
