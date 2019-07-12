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
        $res['list'] = glbData::fmtList($res['list'], 'news');
        return $res;
    }

    function _detailAct(){
        $res['row'] = $this->row;
        return $res;
    }

}
