<?php
/**
 * FTP 操作类
 * 不支持 SFTP 和 SSL FTP 协议, 仅支持标准 FTP 协议.
 * 需要传递一个数组配置
 * 示例:
 * $config['hostname'] = 'ftp.example.com';
 * $config['username'] = 'your-username';
 * $config['password'] = 'your-password';
 * $config['debug'] = TRUE;
 */
// rsFtp-Ftp存储
@set_time_limit(1000);
class rsFtp{

    public static $objs = array();

    public $ftp_ssl     = FALSE;
    public $hostname    = '';
    public $username    = '';
    public $password    = '';
    public $port        = 21;
    public $passive     = TRUE;
    public $timeout     = 10;
    public $debug       = FALSE;
    public $conn_id     = FALSE;
    public $dir_ures    = '';

    // 移动:从临时文件夹移动(上传)到ftp远程
    function moveUres($org,$obj){
        //$re = rename($orgfile,DIR_URES.'/'.$obj)
        //$obj_dir = DIR_URES.'/'.$obj;
        $obj_dir = $this->dir_ures.'/'.$obj;
        $this->rmkdir(dirname($obj_dir)); 
        $re = $this->upload($org,$obj_dir,'auto');
        if($re) unlink($org);
        return $re;
    }
    // 删除:
    function delFiles($dir){
        //comFiles::delDir(DIR_URES.'/'.$dir,1);
        $obj_dir = $this->dir_ures.'/'.$dir;
        return $this->delete_dir($obj_dir);
    }

    /**
     * 析构函数 - 设置参数
     */
    function __construct($config=array()){
        if(empty($config)){
            $config = read('store.rsFtp','ex');
        } 
        foreach($config as $key => $val){
            if(isset($this->$key)){
                $this->$key = $val;
            }
        }
        // 准备主机名
        $this->hostname = preg_replace('|.+?://|', '', $this->hostname);
        $this->connect();
    }

    /**
     * FTP 链接
     */
    function connect(){
        $func = $this->ftp_ssl && function_exists('ftp_ssl_connect') ? 'ftp_ssl_connect' : 'ftp_connect';
        if(FALSE=== ($this->conn_id = @$func($this->hostname, $this->port, $this->timeout))){
            $this->_error('Can NOT Connect FTP!');
            return FALSE;
        }
        if(!$this->_login()){
            $this->_error('Can NOT Login FTP!');
            return FALSE;
        }
        // 如果需要则设置传输模式
        if($this->passive==TRUE){
            ftp_pasv($this->conn_id, TRUE);
        }
        return TRUE;
    }

    /**
     * FTP 登录
     */
    function _login(){
        return @ftp_login($this->conn_id, $this->username, $this->password);
    }

    /**
     * 验证连接ID
     */
    function _is_conn(){
        if(!is_resource($this->conn_id)){
            $this->_error('Can NOT Connect FTP!');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 更改目录
     * 第二个参数可以让我们暂时关闭，以便调试
     * 此功能可用于检测是否存在一个文件夹
     * 抛出一个错误。没有什么的FTP相当于is_dir()
     * 因此，我们试图改变某一特定目录。
     */
    function chdir($path = '', $isdebug = FALSE){
        if($path=='' OR ! $this->_is_conn()){
            return FALSE;
        }
        $result = @ftp_chdir($this->conn_id, $path);
        if($result=== FALSE){
            if($isdebug==FALSE){
                $this->_error('Can NOT Change DIR!');
            }
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 创建一个目录
     */
    function mkdir($path = '', $perm = NULL){
        if($path=='' OR ! $this->_is_conn()){
            return FALSE;
        }
        $result = @ftp_mkdir($this->conn_id, $path);
        if($result=== FALSE){
            $this->_error('Can NOT Create DIR!');
            return FALSE;
        }
        // 如果需要设置权限
        if(!is_null($perm)){
            $this->chmod($path, (int)$perm);
        }
        return TRUE;
    }

    /**
     * 创建深级目录
     */
    function rmkdir($path = '', $pathsymbol = '/'){
        $pathArray = explode($pathsymbol,$path);
        $pathstr = $pathsymbol;
        foreach($pathArray as $val){
            if(!empty($val)){
                //构建文件夹路径
                $pathstr = $pathstr.$val.$pathsymbol;
                if(!$this->_is_conn()){
                    return FALSE;
                }
                $result = @ftp_chdir($this->conn_id, $pathstr);
                if($result=== FALSE){
                    //如果不存在这个目录则创建
                    if(!$this->mkdir($pathstr)){
                        return FALSE;
                    }
                }
            }
        }
        return TRUE;
    }

    /**
     * 上传一个文件到服务器
     */
    function upload($locpath, $rempath, $mode = 'auto', $perm = NULL){
        if(!$this->_is_conn()){
            return FALSE;
        }
        if(!file_exists($locpath)){
            $this->_error('Local File NOT Found!');
            return FALSE;
        }
        // 未指定则设置模式
        if($mode=='auto'){
            // 获取文件扩展名，以便本类上传类型
            $ext = $this->_getext($locpath);
            $mode = $this->_settype($ext);
        }
        $mode = ($mode=='ascii') ? FTP_ASCII : FTP_BINARY;
        $result = @ftp_put($this->conn_id, $rempath, $locpath, $mode, 0);
        if($result=== FALSE){
            $this->_error('Can NOT Upload!');
            return FALSE;
        }
        // 如果需要设置文件权限
        if(!is_null($perm)){
            $this->chmod($rempath, (int)$perm);
        }
        return TRUE;
    }

    /**
     * 重命名(或者移动)一个文件
     */
    function rename($old_file, $new_file, $move = FALSE){
        if(!$this->_is_conn()){
            return FALSE;
        }
        $result = @ftp_rename($this->conn_id, $old_file, $new_file);
        if($result=== FALSE){
            $msg = ($move==FALSE) ? 'Can NOT Rename!' : 'Can NOT Move!';
            $this->_error($msg);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 移动一个文件
     */
    function move($old_file, $new_file){
        return $this->rename($old_file, $new_file, TRUE);
    }

    /**
     * 重命名或者移动一个文件
     */
    function delete_file($filepath){
        if(!$this->_is_conn()){
            return FALSE;
        }
        $result = @ftp_delete($this->conn_id, $filepath);
        if($result=== FALSE){
            $this->_error('Can NOT Delete!');
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     * 删除一个文件夹，递归删除一切（包括子文件夹）中内容
     */
    function delete_dir($filepath){
        if(!$this->_is_conn()){
            return FALSE;
        }
        // 如果需要在尾部加上尾随"/"
        $filepath = preg_replace("/(.+?)\/*$/", "\\1/",  $filepath);
        $list = $this->list_files($filepath);
        if($list !== FALSE AND count($list)>0){
            foreach($list as $item){
                // 如果我们不能删除该项目,它则可能是一个文件夹
                // 将调用 delete_dir()
                if(!@ftp_delete($this->conn_id, $item)){
                    $this->delete_dir($item);
                }
            }
        }
        $result = @ftp_rmdir($this->conn_id, $filepath);
        if($result=== FALSE){
            $this->_error('Can NOT Delete!');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 设置文件权限
     */
    function chmod($path, $perm){
        if(!$this->_is_conn()){
            return FALSE;
        }
        // 仅PHP5才能运行
        if(!function_exists('ftp_chmod')){
            $this->_error('Can NOT set Perm!');
            return FALSE;
        }
        $result = @ftp_chmod($this->conn_id, $perm, $path);
        if($result=== FALSE){
            $this->_error('Can NOT set Perm!');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 在指定的目录的FTP文件列表
     */
    function list_files($path = '.'){
        if(!$this->_is_conn()){
            return FALSE;
        }
        return ftp_nlist($this->conn_id, $path);
    }
    
    /**
     * 返回指定目录下文件的详细列表
     */
    function list_rawfiles($path = '.', $type='dir'){
        if(!$this->_is_conn()){
            return FALSE;
        }
        $ftp_rawlist = ftp_rawlist($this->conn_id, $path, TRUE);
        foreach($ftp_rawlist as $v){
            $info = array();
            $vinfo = preg_split("/[\s]+/", $v, 9);
            if($vinfo[0] !== "total"){
                $info['chmod'] = $vinfo[0];
                $info['num'] = $vinfo[1];
                $info['owner'] = $vinfo[2];
                $info['group'] = $vinfo[3];
                $info['size'] = $vinfo[4];
                $info['month'] = $vinfo[5];
                $info['day'] = $vinfo[6];
                $info['time'] = $vinfo[7];
                $info['name'] = $vinfo[8];
                $rawlist[$info['name']] = $info;
            }
        }
        $dir = array();
        $file = array();
        foreach($rawlist as $k => $v){
            if($v['chmod']{0}=="d"){
                $dir[$k] = $v;
            } elseif($v['chmod']{0}=="-"){
                $file[$k] = $v;
            }
        }
        return ($type=='dir')? $dir : $file;
    }

    /**
     * 检索一个本地目录下的所有内容(包括子目录和所有文件)，并通过FTP为这个目录创建一份镜像。
     * 源路径下的任何结构都会被创建到服务器上。你必须给出源路径和目标路径
     *
     * @access    public
     * @param    string    含有尾随"/"的源路径
     * @param    string    目标路径 - 含有尾随"/"的文件夹
     * @return    bool
     */
    function mirror($locpath, $rempath){
        if(!$this->_is_conn()){
            return FALSE;
        }
        // 打开本地文件路径
        if($fp = @opendir($locpath)){
            // 尝试打开远程文件的路径.
            if(!$this->chdir($rempath, TRUE)){
                // 如果不能打开则创建
                if(!$this->rmkdir($rempath) OR ! $this->chdir($rempath)){
                    return FALSE;
                }
            }
            // 递归读取本地目录
            while (FALSE !== ($file = readdir($fp))){
                if(@is_dir($locpath.$file) && substr($file, 0, 1) != '.'){
                    $this->mirror($locpath.$file."/", $rempath.$file."/");
                }
                elseif(substr($file, 0, 1) != "."){
                    // 获取文件扩展名，以便本类上传类型
                    $ext = $this->_getext($file);
                    $mode = $this->_settype($ext);

                    $this->upload($locpath.$file, $rempath.$file, $mode);
                }
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 取出文件扩展名
     */
    function _getext($filename){
        if(FALSE=== strpos($filename, '.')){
            return 'txt';
        }
        $x = explode('.', $filename);
        return end($x);
    }

    /**
     * 设置上传类型
     */
    function _settype($ext){
        $text_types = array(
            'txt','text','js','css',
            'htm','html','shtm','shtml',
            'php','log','xml'
        );
        return (in_array($ext, $text_types)) ? 'ascii' : 'binary';
    }

    /**
     * 关闭连接
     */
    function close(){
        if(!$this->_is_conn()){
            return FALSE;
        }
        @ftp_close($this->conn_id);
    }
    
    /**
     * 显示错误信息
     */
    function _error($msg){
        if($this->debug==TRUE){
            basDebug::bugLogs('rsFtp',$msg,'detmp','db');
        }
    }

}//End Class
