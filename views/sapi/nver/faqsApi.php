<?php
namespace imcat;

class faqsApi extends bextApi{
    
    function homeAct(){ 
        $parts = read("faqs.i");
        $res['parts'] = $parts;
        foreach($parts as $kp => $vp) {
            $list = data('faqs', "catid='$kp'", 3, '');
            $res[$kp] = glbData::fmtList($list, 'faqs');
        }
        $res['row'] = $this->row;
        return $res;
    }

    function listAct(){
        $res = $this->_list('faqs');
        $ops = [
            'mpic_resize' => '120x90',
        ];
        $res['list'] = glbData::fmtList($res['list'], 'faqs', $ops);
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
        $res['row'] = glbData::fmtRow($this->row, 'faqs', $ops);
        $faqs = data('faqs', '', 5, '');
        $res['rels'] = glbData::fmtList($faqs, 'faqs');
        return $res;
    }

}
