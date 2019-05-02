<?php
namespace imcat;

// ...类 caiHftxf
class caiHftxf{

    static function dataRow($row, $det, $no=12){
        $fang = caiBase::attrs('fang.i'); 
        $local = caiBase::attrs('local.i'); 
        $data = $det[0];
        $hxs = $det[1];
        $pics = $det[2];
        $info = $ext = [];
        $reinfo = "";
        // row > info
        $info['exid'] = 'hft-'.$row['exid'];
        $info['catid'] = 'lp';
        $info['title'] = $row['title'];
        $info['mpic'] = $row['thumb'];
        $addr = explode(' ',$row['addr']);
        $info['areas'] = caiBase::atkeys($local['0'],$addr[0],2);
        $info['addr'] = $addr[1];
        $info['price'] = $row['price'];
        $tmp = str_replace($row['price'],'',$row['pfull']);
        $info['punit'] = strpos($tmp,'套')>0 ? 'tao' : 'mi2';
        $info['sotags'] = $row['tag1'];
        $info['jztype'] = caiBase::atkeys($fang['jz'],$row['tag2'],2);
        $info['wytype'] = caiBase::atkeys($fang['wy'],$data['物业类型'],2);
        $info['zxtype'] = caiBase::atkeys($fang['zx'],$data['装修情况'],2);
        $info['addr'] = $data['楼盘地址']; 
        $info['map'] = $data['map'].',12';
        // data > ext
        $ext['cqnian'] = $data['产权年限']; 
        $ext['wycorp'] = $data['物业公司']; 
        $ext['dev'] = $data['开发商'];
        $ext['wyfee'] = floatval($data['物业费']);
        $ext['remark'] = ''; $tmp=['车位情况','容积率','绿化率','占地面积','建筑面积','售楼部地址'];
        foreach ($tmp as $key) {
            if(empty($data[$key])) continue;
            if(in_array($data[$key],['暂无','-'])) continue;
            $ext['remark'] .= "{$key}:{$data[$key]};\n";
        }
        // update
        $old = db()->table('docs_house')->where("exid='{$info['exid']}'")->find();
        if(!$old){
            $kar = [caiBase::dbKid('docs_house',$no),$no];
            $lpid = $info['did'] = $ext['did'] = $kar[0];
            $info['dno'] = $kar[1];
            db()->table('docs_house')->data($info)->insert();
            $reinfo .= "### {$info['exid']} > {$info['did']} ins; <br>\n";
            $actf = 'ins';
        }else{
            $lpid = $ext['did'] = $old['did'];
            db()->table('docs_house')->data($info)->where("did='{$old['did']}'")->update();
            $reinfo .= "### {$info['exid']} > {$old['did']} upd; <br>\n";
            $actf = 'upd';
        }
        db()->table('dext_house')->data($ext)->replace();
        // hxs
        $chxi = $chxu = 0;
        foreach($hxs as $tr){ 
            $sales = str_replace(['售罄','预售'],['售完','待售'],trim($tr['sale']));
            $hxi = ['title'=>$tr['detail'],'mpic'=>$tr['thumb'],'aflag'=>'8','hxs'=>$tr['title'],
                'mjout'=>$tr['area'],'sales'=>caiBase::atkeys($fang['sa'],$sales,2),
                'hxroom'=>intval($tr['title']),];
            $hxi['lpid'] = $lpid;
            $hxi['exid'] = "hft-{$tr['id']}";
            $old = db()->table('docs_huxing')->where("exid='{$hxi['exid']}'")->find();
            if(!$old){
                $kar = [caiBase::dbKid('docs_huxing',$chxi,0,$no),$chxi];
                $hxi['did'] = $kar[0];
                $hxi['dno'] = $kar[1];
                db()->table('docs_huxing')->data($hxi)->insert();
                $reinfo .= "- huxing {$hxi['exid']} > {$hxi['did']} ins; <br>\n";
                $chxi++;
            }else{
                db()->table('docs_huxing')->data($hxi)->where("did='{$old['did']}'")->update();
                $reinfo .= "- huxing {$hxi['exid']} > {$old['did']} upd; <br>\n";
                $chxu++;
            }
        }
        // pics
        $cabi = $cabu = 0;
        foreach($pics as $tr){ 
            $types = str_replace(['交通图','外景图','实景图','看图说房'],['周边图','小区图','小区图','室内图'],trim($tr['type']));
            $abi = ['title'=>$tr['type'],'mpic'=>$tr['thumb'],'aflag'=>'8','atype'=>caiBase::atkeys($fang['tu'],$types,2),];
            $abi['lpid'] = $lpid;
            $abi['part'] = 'house';
            $abi['exid'] = "hft-{$tr['id']}"; 
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
        #dump($data);
        #dump($hxs);
        #dump($pics);
        // return
        $res = ['actf'=>$actf,'reinfo'=>$reinfo,
            'chxi'=>$chxi,'chxu'=>$chxu,'cabi'=>$cabi,'cabu'=>$cabu,];
        return $res;
    }

    // getList
    static function getList($url){
        $lists = extQuery::pqa([$url,30],'.left-content li.block');
        $list = [];
        foreach($lists as $li) {
            $tmp = pq($li)->find('.newHouse-tit');
            $row['url'] = pq($tmp)->find('a')->attr('href');
            $row['title'] = pq($tmp)->find('a')->text();
            $row['thumb'] = pq($li)->find('img:first')->attr('src');
            $row['addr'] = pq($li)->find('.house-info:first')->text();
            $row['price'] = pq($li)->find('.total-price')->find('b')->text();
            $row['pfull'] = pq($li)->find('.total-price')->text();
            $row['tag1'] = pq($li)->find('.tag')->find('span')->text();
            $row['tag2'] = pq($li)->find('.type-btn')->text();
            $row['exid'] = intval(basename($row['url']));
            $list[] = $row;
        }
        return $list;
    }
    
    // getRow
    static function getRow($url){
        $did = extQuery::pqa([$url,30],0);
        $tmp = pq('.box-div-margin span',$did);
        $res1 = []; $key = '';
        foreach($tmp as $k=>$li) {
            $kv = pq($li)->text();
            if($k%2==0){
                $key = $kv;
            }else{
                $res1[str_replace('：','',$key)] = $kv;
            } 
        } //dump($res1); 
        $tmp = pq('.circum script:last',$did)->text();
        preg_match_all("/(lat|lng) = \"([\d\.]+)\";/i",$tmp,$pt);
        if(!empty($pt[2])){
            $res1['map'] = "{$pt[2][1]},{$pt[2][0]}";
        }else{
            $res1['map'] = '';
        } //dump($pt); 
        // 户型图
        $url1 = str_replace('xinfang/','xinfang/huxing/',$url);
        $lists = extQuery::pqa([$url1,30],'.n-list li.n-item'); 
        $hxs = []; $row = []; $pids = [];
        foreach($lists as $li) {
            $row['id'] = pq($li)->attr('data-huxingid');
            $row['title'] = pq($li)->find('.n-type')->text();
            $tmp = pq($li)->find('.n-tit')->text();
            preg_match_all("/([\d\.]+)㎡/i",$tmp,$pt);
            if(!empty($pt[1][0])){
                $row['area'] = $pt[1][0];
            } //dump($pt);
            $row['sale'] = pq($li)->find('.n-sale')->text();
            $row['detail'] = trim(pq($li)->find('.n-detail')->text());
            $row['thumb'] = pq($li)->find('img')->attr('src');
            $hxs[] = $row;
        }
        // 相册图
        $url2 = str_replace('xinfang/','xinfang/photo/',$url);
        $lists = extQuery::pqa([$url2,30],'#loupan li.l-item');
        $imgs = []; $row = []; $cnts = 0;
        foreach($lists as $li) {
            if($cnts>11) break;
            $row['id'] = pq($li)->attr('data-photoid');
            if(in_array($row['id'],$pids)) break;
            $row['type'] = pq($li)->attr('data-phototype');
            $row['thumb'] = pq($li)->find('img')->attr('data-original');
            $imgs[] = $row;
            $cnts++;
        }
        return [$res1,$hxs,$imgs];
    }

}

/*
 
*/
