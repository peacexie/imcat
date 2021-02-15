<?php
namespace imcat;

class newsApi extends bextApi{
    
    function homeAct(){ 
        $parts = read("news.i");
        $res['parts'] = $parts;
        foreach($parts as $kp => $vp) {
            $list = data('news', "catid='$kp'", 3, '');
            $res[$kp] = glbData::fmtList($list, 'news');
        }
        $res['row'] = $this->row;
        return $res;
    }

    function listAct(){
        $res = $this->_list();
        $ops = [
            'mpic_resize' => '120x90',
        ];
        $res['list'] = glbData::fmtList($res['list'], 'news', $ops);
        $page = req('page', 1, 'N');
        if($page>1){
            unset($res['parts']);
        }
        return $res;
    }

    function _detailAct(){
        $ops = [
            'hinfo' => ['type'=>'cOpt', 'mod'=>'hinfo',],
        ];
        $res['row'] = glbData::fmtRow($this->row, 'news', $ops);
        $news = data('news', '', 5, '');
        $res['rels'] = glbData::fmtList($news, 'news');
        return $res;
    }

}
