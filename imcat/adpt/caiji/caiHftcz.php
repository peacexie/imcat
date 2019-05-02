<?php
namespace imcat;

// ...类 caiHftcz
class caiHftcz{

    static function dataRow($row, $det, $no=12){ 
        $fang = caiBase::attrs('fang.i'); 
        $local = caiBase::attrs('local.i'); 
        $res1 = $det[0]; 
        $res2 = $det[1]; 
        $res3 = $det[2];
        $pics = $det[3];
        $info = $ext = [];
        $reinfo = "";
        // row > info 
        $info['exid'] = 'hft-'.$row['exid'];
        $info['title'] = $row['title'];
        $info['catid'] = 'sysfy';
        $info['mpic'] = $row['thumb'];
        $info['price'] = $row['price'];
        $info['lpid'] = 'hft-'.$res1['lpid'];
        $info['map'] = $res1['map'];
        $zutps = ['整租'=>'1','合租'=>'2','短租'=>'3','未知'=>'4','xxx'=>'4',];
        $info['zutype'] = isset($zutps[$res1['方式']]) ? $zutps[$res1['方式']] : 4;
        $info['hxs'] = $res1['hxs'];
        $info['hxroom'] = intval($res1['hxs']);
        $info['mjout'] = intval($res1['area']);
        #$info['cxtype'] = caiBase::atkeys($fang['cx'],$res2['房屋朝向'],2);
        $info['zxtype'] = caiBase::atkeys($fang['zx'],$res1['装修'],2);
        $info['wytype'] = caiBase::atkeys($fang['wy'],$res2['物业类型'],2);
        $info['jztype'] = caiBase::atkeys($fang['jz'],str_replace(' ',',',$res2['建筑类型']),2);
        $info['sotags'] = $row['atts']; // 'atts' => '八步,八步,远东国贸,1室1厅,50㎡',
        #$info['addr'] = $res2['小　　区'].'/'.$res2['地　　址']; 
        // data > ext
        $ext['tihu'] = $res2['配备电梯'];
        $ext['yaqi'] = $res1['押金'].'/'.$res1['付款'];
        $ext['louceng'] = $res1['楼层'];
        #$ext['cqnian'] = $res2['产权年限']; // ? '竣工时间' => '2009', '绿 化 率' => '46.88%',
        $ext['jznian'] = $res1['年代']; 
        $ext['wyfee'] = floatval($res2['物 业 费']);
        $ext['remark'] = empty($res3['remark']) ? '' : $res3['remark'];
        if(!empty($res3['核心卖点'])){
            $ext['remark'] .= (empty($res3['remark']) ? '' : "；\n").$res3['核心卖点'];
        }
        $ext['remzb'] = '';
        $ext['remjt'] = ''; //empty($res3['交通出行']) ? '' : $res3['交通出行'];
        $ext['peitao'] = caiBase::atkeys($fang['pt'],implode(',',$res3['peitaos']),2);
        if(strpos(implode(',',$res3['peitaos']),'床')){
            $ext['peitao'] .= ',p44';
        }
        // update
        $old = db()->table('docs_rent')->where("exid='{$info['exid']}'")->find();
        if(!$old){
            $kar = [caiBase::dbKid('docs_rent',$no),$no];
            $lpid = $info['did'] = $ext['did'] = $kar[0];
            $info['dno'] = $kar[1];
            db()->table('docs_rent')->data($info)->insert();
            $reinfo .= "### {$info['exid']} > {$info['did']} ins; <br>\n";
            $actf = 'ins'; 
        }else{
            $lpid = $ext['did'] = $old['did'];
            db()->table('docs_rent')->data($info)->where("did='{$old['did']}'")->update();
            $reinfo .= "### {$info['exid']} > {$old['did']} upd; <br>\n";
            $actf = 'upd';
        }
        db()->table('dext_rent')->data($ext)->replace();
        // pics
        $cabi = $cabu = 0;
        foreach($pics as $tr){ 
            $abi = ['title'=>$tr['alt'],'mpic'=>$tr['src'],'aflag'=>'8','atype'=>'',];
            $abi['lpid'] = $lpid;
            $abi['part'] = 'rent';
            $abi['exid'] = "hft-".(md5($tr['src'])); 
            $old = db()->table('docs_album')->where("exid='{$abi['exid']}'")->find();
            if(!$old){
                $kar = [caiBase::dbKid('docs_album',$cabi,0,$no),$cabi];
                $abi['did'] = $kar[0];
                $abi['dno'] = $kar[1];
                db()->table('docs_album')->data($abi)->insert();
                $reinfo .= "- album {$abi['exid']} > {$abi['did']} ins; <br>\n";
                $cabi++;
            }else{
                db()->table('docs_album')->data($abi)->where("did='{$old['did']}'")->update();
                $reinfo .= "- album {$abi['exid']} > {$old['did']} upd; <br>\n";
                $cabu++;
            }
        }
        // debug
        #dump($info);
        #dump($ext);
        #dump($row);
        #dump($pics);
        // return
        $res = ['actf'=>$actf,'reinfo'=>$reinfo,'cabi'=>$cabi,'cabu'=>$cabu,];
        return $res;
    }

    // getList
    static function getList($url){
        $lists = extQuery::pqa([$url,30],'.list-content li.block');
        $list = [];
        foreach($lists as $li) {
            $tmp = pq($li)->find('.title');
            $row['url'] = pq($tmp)->find('a')->attr('href');
            $row['title'] = pq($tmp)->find('a')->text();
            $tmp = pq($li)->find('img:first');
            $row['thumb'] = pq($tmp)->attr('src');
            $row['atts'] = pq($tmp)->attr('alt');
            $row['price'] = pq($li)->find('.total-price')->find('span')->text();
            $row['exid'] = intval(basename($row['url']));
            $list[] = $row;
        }
        return $list;
    }
    
    // getRow
    static function getRow($url){
        $did = extQuery::pqa([$url,30],0);
        // 
        $tmp = pq('.m-box .wAll',$did);
        $res1 = []; 
        foreach($tmp as $k=>$li) {
            $ki = pq($li)->find('span:first')->text();
            $kv1 = pq($li)->find('label')->text();
            $kv2 = pq($li)->find('label')->find('span')->text();
            $res1[str_replace('：','',$ki)] = trim($kv2 ? $kv2 : $kv1);
        } #dump($res1); 
        #$res1['lpname'] = pq('.buildName',$did)->text();
        $head = pq('head',$did)->text(); 
        $head = basElm::getPos($head,['var pageConfig = ','var photo_url']); 
        preg_match_all("/\'(buildId|flatitude|flongitude)\'\: \"(\S+)\"\,/i",$head,$hpt);
        $res1['lpid'] = empty($hpt[2][0]) ? '-' : $hpt[2][0];
        $res1['map'] = (empty($hpt[2][2]) ? '0' : $hpt[2][2]).','.(empty($hpt[2][1]) ? '0' : $hpt[2][1]);
        $tmp = pq('.t-detail',$did);
        $res1['hxs'] = pq($tmp)->find('p:first')->text();
        $res1['area'] = pq($tmp)->find('p')->eq(1)->text();
        //
        $tmp = pq('.peitao-info .wAll',$did);
        $res2 = []; $key = '';
        foreach($tmp as $k=>$li) {
            $kv1 = pq($li)->find('span:first')->text();
            $kv2 = pq($li)->text();
            $kv2 = str_replace($kv1,'',$kv2); 
            $res2[str_replace('：','',$kv1)] = trim($kv2);
        } //dump($res2); 
        // 
        $tmp = pq('.md1_height .peitao-tubiao',$did);
        $res3 = []; $key = '';
        $res3['remark'] = trim(pq('.ct_fy-2',$did)->text());
        foreach($tmp as $k=>$li) {
            $kv1 = pq($li)->find('img')->attr('src');
            $kv2 = pq($li)->find('p')->text();
            if(!strpos($kv1,'-2.png')){
                $res3['peitaos'][] = trim($kv2);
            }
        } #dump($res3); 
        // 相册图
        $tmp = pq('.photoHtml .swiper-slide',$did);
        $imgs = []; $key = '';
        foreach($tmp as $k=>$li) {
            $img = pq($li)->find('img');
            $row['src'] = pq($img)->attr('data-src');
            $row['alt'] = pq($img)->attr('alt');
            $imgs[] = $row;
        } #dump($imgs); 
        // return
        return [$res1,$res2,$res3,$imgs];
    }

}

/*
 
*/
