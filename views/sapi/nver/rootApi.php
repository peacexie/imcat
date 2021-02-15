<?php
namespace imcat;

class rootApi extends bextApi{
    
    function homeAct(){
        $top = req('top', '4');
        $whr = ''; //"hinfo>'0'";
        $news = data('news', $whr, 5, '');
        $res['news'] = glbData::fmtList($news, 'news');
        $cargo = data('cargo', $whr, 4, '');
        $opts['brand'] = ['type'=>'cOpt', 'mod'=>'brand'];
        $res['cargo'] = glbData::fmtList($cargo, 'cargo', $opts);
        $faqs = data('faqs', $whr, 3, '');
        $res['faqs'] = glbData::fmtList($faqs, 'faqs');
        return $res;
    }

    // mds
    function mdsAct(){
        $mds = req('mds');
        $mda = explode(',', $mds);
        $res = [];
        foreach($mda as $mdk){
            $res[$mdk] = comFiles::get(DIR_API."/mds/$mdk.md");
        }
        return $res;
    }

    // city
    function cityAct(){
        //;
    }
    
    // about+update
    function abupdAct(){
        $res = $this->uver;
        $res['about'] = comFiles::get(DIR_API."/mds/sys-about.md");
        return $res;
    }

}
