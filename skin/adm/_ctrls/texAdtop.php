<?php
namespace imcat\adm;

use imcat\basJscss;
use imcat\glbConfig;
use imcat\usrBase;

class texAdtop{

    static function adm_jscfgs(){
        $jscfg = "\n// js Admin"; 
        $jscfg .= "\nvar _miadm={}, _mpadm={}; ";
        $user = usrBase::userObj('Admin');
        $imenu = '';
        if(!empty($user)){
            if(!empty($user->uperm['impid'])){ // 继承菜单
                $grades = glbConfig::read('grade','dset');
                if(isset($grades[$user->uperm['impid']])){
                    $imenu = $grades[$user->uperm['impid']]['pmadm'];
                }
            }
            $jscfg .= "\n_miadm.userType = '".$user->userType."';";
            $jscfg .= "\n_miadm.userGrade = '".@$user->uperm['grade']."';"; 
            $jscfg .= "\n_miadm.userFlag = '".$user->userFlag."';";
            $jscfg .= "\n_miadm.uname = '".@$user->usess['uname']."';";
            $jscfg .= "\n_mpadm.title = '".@$user->uperm['title']."';";
            $jscfg .= "\n_mpadm.menus = ':,".@$user->uperm['pmadm'].",$imenu,';";
            $jscfg .= "\n_mpadm.defmu = '".@$user->uperm['defmu']."';";
        }
        $jscfg .= "\n";
        echo basJscss::jscode($jscfg);
    }

}
