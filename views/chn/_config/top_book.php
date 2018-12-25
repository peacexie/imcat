<?php

function bookData($did,$dno){
    $data = db()->table('topic_items')->where("did='$did' AND dno='$dno'")->find();
    if(!$data){
        \imcat\glbHtml::httpStatus('404');
        die('<h3>404 Not Found</h3>');
    }
    return $data;
}

function bookNav2($data,$clist=''){
    $cfgs = array(
        'prev' => array('<=','<','top DESC,dno DESC'),
        'next' => array('>=','>','top,dno'),
    );
    $whrs = "did='{$data['did']}' "; // AND dno!='{$data['dno']}'
    foreach ($cfgs as $key => $cfg) { // top,dno
        $whr = "$whrs AND part='{$data['part']}' AND top{$cfg[0]}'{$data['top']}' AND dno{$cfg[1]}'{$data['dno']}'";
        $row = db()->table('topic_items')->where($whr)->order($cfg[2])->find(); 
        if(empty($row)){
            $cfgb = \imcat\devTopic::cfg2arr($clist); 
            $func = "{$key}Key";
            $pk = \imcat\basArray::$func($cfgb,$data['part']);
            if($pk){ 
                $whr = "$whrs AND part='$pk'";
                $row = db()->table('topic_items')->where($whr)->order($cfg[2])->find();
            }
        }
        if(empty($row)){
            $res[$key] = '-';
        }else{
            $title = ($key=='prev'?$cfg[1]:'')." {$row['title']} ".($key=='next'?$cfg[1]:'');
            $res[$key] = "<a href='".surl("topic.{$row['did']}.{$row['dno']}")."'>$title</a>";
        }
        
    } //dump($res);
    return $res;
}
