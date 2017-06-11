<?php
/**
 * UEditor编辑器通用上传类
 */
class comUpload
{
    private $fileField; //文件域名
    private $file; //文件上传对象
    private $base64; //文件上传对象
    private $config; //配置信息
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息,
    public $stateMap = array();

    /**
     * @param string $fileField 表单名称
     * @param array $config 配置项
     * @param type upload/remote/base64
     */
    function __construct($fileField, $config, $type = "upload")
    {
        $this->stateMap = basLang::ucfg('cfglibs.upload');
        $this->stateMap['ERROR_TYPE_NOT_ALLOWED'] = $this->stateMap['ERROR_TYPE_NOT_ALLOWED'];
        $this->fileField = $fileField;
        $this->config = $config;
        $this->type = $type;
        if ($type == "remote") {
            $this->saveRemote();
        } else if($type == "base64") {
            $this->upBase64();
        } else {
            $this->upFile();
        }
    }

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    private function upFile()
    {
        $file = $this->file = $_FILES[$this->fileField];
        if (!$file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return;
        }
        if ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);
            return;
        } else if (!file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE_NOT_FOUND");
            return;
        } else if (!is_uploaded_file($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMPFILE");
            return;
        }
        $this->oriName = $file['name'];
        $this->fileSize = $file['size'];
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->fileName = $this->getFileName();
        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }
        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return;
        }
        //移动文件
        if ( !move_uploaded_file( $file[ "tmp_name" ] , $this->fullName ) ) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_MOVE");
        }else{
            $this->stateInfo = $this->stateMap[0];
        }
    }

    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    private function upBase64()
    {
        $base64Data = $_POST[$this->fileField];
        $img = base64_decode($base64Data);
        $this->fileSize = strlen($img);
        $this->oriName = 'base64_'.$this->fileSize.'_'.basKeyid::kidRand('f',4).'.png'; //$this->config['oriName']; //
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->fileName = $this->getFileName();
        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }
        //移动文件
        if (!(comFiles::put($this->fullName, $img))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        }

    }

    /**
     * 拉取远程图片
     * @return mixed
     */
    private function saveRemote($imgUrl='')
    {
        if(empty($imgUrl)){
            $imgUrl = htmlspecialchars($this->fileField);
            $imgUrl = str_replace("&amp;", "&", $imgUrl); 
        }
        //http开头验证
        if (strpos($imgUrl, "http") !== 0) {
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_LINK");
            return;
        }
        //获取请求头并检测死链
        $heads = get_headers($imgUrl);
        if (!(stristr($heads[0], "200") && stristr($heads[0], "OK"))) {
            $this->stateInfo = $this->getStateInfo("ERROR_DEAD_LINK");
            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        //不认证：!in_array($fileType, $this->config['allowFiles']) 为了处理类似图片：http://mmbiz.qpic.cn/mmbiz/kCd...gtw/0
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (@stristr($heads['Content-Type'], "image")) { 
            $this->stateInfo = $this->getStateInfo("ERROR_HTTP_CONTENTTYPE");
            return;
        }//*/
        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/", $imgUrl, $m);
        $this->oriName = $m ? $m[1]:"";
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->fileName = $this->getFileName();
        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }
        //移动文件
        if (!(comFiles::put($this->fullName, $img))) { //移动失败
            $this->stateInfo = $this->getStateInfo("ERROR_WRITE_CONTENT");
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        } 

    }

    // 上传错误检查
    private function getStateInfo($errCode)
    {
        $msg = isset($this->stateMap[$errCode]) ? $this->stateMap[$errCode] : $this->stateMap["ERROR_UNKNOWN"];
        $msg = "$errCode:$msg";
        return $msg;
    }

    // 获取文件扩展名
    private function getFileExt()
    {
        $ext = strtolower(strrchr($this->oriName, '.'));
        if(empty($ext) && $this->type=='remote'){
            $ext = '.jpg';    
        } //处理类似图片:http://mmbiz.qpic.cn/mmbiz/kCd...gtw/0
        return $ext;
    }

    // 重命名文件
    private function getFullName()
    {
        $folder = comStore::getTmpDir();
        return $folder . '/' . $this->getFileName();

    }

    // 获取文件名
    private function getFileName () {
        
        $ext = $this->getFileExt();
        $upren = basReq::val('upren','auto');
        if($upren=='auto'){ // || in_array($this->type,array('remote','base64'))
            $name = basKeyid::kidTemp().$ext;
        }else{
            if(in_array($this->type,array('remote','base64'))){
                $org = $this->oriName;
            }else {
                $org = str_replace(array($ext),array(''),$this->file["name"]);    
            }
            $org = basStr::filKey($org,'()-.@_~[]'); //-._@()[]
            $org = str_replace(array('‘','’','“','”'),array("[",']','[',']'),$org);
            $name ="{$org}~".basKeyid::kidRand('f',4).$ext;
        }
        return $this->fileName = strtolower($name);
        
    }

    // 文件类型检测
    private function checkType()
    {
        $flag = $this->config["allowFiles"]==='(supper)' ? true : in_array($this->getFileExt(), $this->config["allowFiles"]);
        return $flag;
    }
    // 文件大小检测
    private function checkSize()
    {
        $flag = $this->config["maxSize"]==='(supper)' ? true : $this->fileSize <= ($this->config["maxSize"]*1024 );
        return $flag;
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    function getFileInfo()
    {
        return array(
            "state" => $this->stateInfo,
            "url" => comStore::fixTmpDir($this->fullName),
            "title" => $this->fileName,
            "original" => $this->oriName,
            "type" => $this->fileType,
            "size" => $this->fileSize
        );
    }

}
