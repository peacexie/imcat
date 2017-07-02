<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

class Recorder{

    private static $data;
    private $inc;
    private $error;

    private $acfile = '/weixin/qqcon_(appid).cac_txt';
    private $aclife = '72d'; // Access_Token的有效期默认是3个月


    public function __construct(){
        $this->error = new ErrorCase();
        //-------读取配置文件
		/*
        $incFileContents = file(QQC_ROOT."comm/inc.php");
        $incFileContents = $incFileContents[1];
        $this->inc = json_decode($incFileContents);
		*/
		include DIR_ROOT.'/cfgs/excfg/ex_a3rd.php';
		$this->inc = $_cfgs['qqconn'];
        if(empty($this->inc)){
            $this->error->showError("20001");
        }

        if(empty($_SESSION['QC_userData'])){
            self::$data = array();
        }else{
            self::$data = $_SESSION['QC_userData'];
        }
        //-------读取配置文件 
        $appid = $this->readInc("appid");
        $this->acfile = str_replace('(appid)',$appid,$this->acfile);
    }

    public function write($name,$value){
        self::$data[$name] = $value;
    }

    public function read($name){
        if(empty(self::$data[$name])){
            return null;
        }else{
            return self::$data[$name];
        }
    }

    public function readInc($name){ 
        if(empty($this->inc[$name])){
            return null;
        }else{
            return $this->inc[$name];
        }
    }

    public function delete($name){
        unset(self::$data[$name]);
    }

    function __destruct(){
        $_SESSION['QC_userData'] = self::$data;
    }

    function setActic($actik='',$save=1){
        if($save){ 
		    $actik && comFiles::put(DIR_DTMP.$this->acfile,$actik);
		}else{ 
		    unlink(DIR_DTMP.$this->acfile);
		}
    }

    function getActic(){
        $upath = tagCache::chkUpd($this->acfile,$this->aclife);
        $actik = '';
        if($upath){ 
            $actik = comFiles::get(DIR_DTMP.$this->acfile);
        }
        return $actik;
    }
    

}
