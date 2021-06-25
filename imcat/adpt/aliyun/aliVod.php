<?php
namespace imcat;

include_once DIR_VENDOR.'/aliyuncs/Autoloader.php';

//use OSS\OssClient;
//use OSS\Core\OssException;

// 阿里OSS类
class aliVod
{
    public $endpoint = '';
    public $bucket = '';
    private $ossClient = null;

    public function __construct($typ='video') {
        // 读配置
        $appkeys = read('aliyun', 'ex');
        $this->accessKeyId = $appkeys['oss_ak'];
        $this->accessKeySecret = $appkeys['oss_as'];
        $this->endpoint = $appkeys['endpoint'];
        $this->bucket = $appkeys['bucket'];
        #$this->getOssClient();
    }

    // 生成GetObject的签名url，用户可以使用这个url直接在浏览器下载
    public function getSignUrl($file='', $exp=7200){
        $exp = time() + $exp;
        $domain = "http://{$this->endpoint}/$file";
        $StringToSign = "GET\n\n\n".$exp."\n/".$this->bucket."/".$file;
        $Sign = base64_encode(hash_hmac("sha1", $StringToSign, $this->accessKeySecret, true));
        $url = "$domain?OSSAccessKeyId=".$this->accessKeyId."&Expires=".$exp."&Signature=".urlencode($Sign);
        return $url;
        #return $this->ossClient->signUrl($this->bucket, $file, $exp);
    }

    public function getCbBody($callbackUrl){
        $callback_param = array('callbackUrl'=>$callbackUrl,
            'callbackBody'=>'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType'=>"application/x-www-form-urlencoded");
        $callback_string = json_encode($callback_param);
        $body = base64_encode($callback_string);
        return $body;
    }

    public function getPolicy($dir, $end, $keypre=1){
        $expiration = $this->fmtTime($end); // fmtIso8601
        //最大文件大小.用户可以自己设置
        $condition = array(0=>'content-length-range', 1=>0, 2=>1048576000);
        $conditions[] = $condition;
        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        if($keypre) {
            $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
            $conditions[] = $start;
        }
        $arr = array('expiration'=>$expiration,'conditions'=>$conditions);
        $policy = json_encode($arr);
        return base64_encode($policy);
    }

    // 上传本地文件
    public function fileUpload($file, $fobj)
    {
        $this->getOssClient(); 
        return $this->ossClient->uploadFile($this->bucket, $fobj, $file);
    }
    // 简单上传变量的内容到oss文件
    public function putObject($data, $fobj)
    {
        $this->getOssClient(); 
        return $this->ossClient->putObject($this->bucket, $fobj, $data);
    }
    // 删除object
    public function deleteObject($fobj)
    {
        $this->getOssClient(); 
        return $this->ossClient->deleteObject($this->bucket, $fobj);
    }

    // 'test/peace/127K-sky.jpg';
    public function fileMeta($file)
    {
        $this->getOssClient();
        return $this->ossClient->getObjectMeta($this->bucket, $file);
    }

    // 'test/peace/127K-sky.jpg';
    public function fileExists($file)
    {
        $this->getOssClient();
        return $this->ossClient->doesObjectExist($this->bucket, $file);
    }

    // 列出Bucket内所有目录和文件， 根据返回的nextMarker循环得到所有Objects
    public function fileLists($prefix='imcat/')
    {
        $this->getOssClient();
        #$prefix = '';
        $nextMarker = '';
        $res = array();
        while (true) {
            $options = array(
                'delimiter' => '/',
                'prefix' => $prefix,
                'max-keys' => 30,
                'marker' => $nextMarker,
            );
            try {
                $listObjectInfo = $this->ossClient->listObjects($this->bucket, $options);
            } catch (OssException $e) {
                glbError::show($e->getMessage());
                return;
            }
            // 得到nextMarker，从上一次listObjects读到的最后一个文件的下一个文件开始继续获取文件列表
            $nextMarker = $listObjectInfo->getNextMarker();
            $listObject = $listObjectInfo->getObjectList();
            foreach ($listObject as $itm) {
                $res[] = $itm->getKey();
            }
            $listPrefix = $listObjectInfo->getPrefixList(); dump($listPrefix);
            if ($nextMarker === '') {
                break;
            }
        }
        return $res;
    }

    // 
    public function delDir($dir='imcat/')
    {
        $list = $this->fileLists($dir);
        if(!empty($list)){
            $this->ossClient->deleteObjects($this->bucket, $list);
        }
    }

    // 根据Config配置，得到一个OssClient实例
    public function getOssClient()
    {
        if(!class_exists('\OSS\OssClient')){
            glbError::show('OSS\OssClient NOT Found!');
        }
        $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint, true);
        // 0.0060560703277588
        $this->ossClient = $ossClient;
        return $ossClient;
    }

    # ----------------------------------

    static function println($message)
    {
        if(!empty($message)){
            echo strval($message)."\n";
        }
    }



}

/*

$bucketName = Common::getBucketName();
$object = "example.jpg";
$ossClient = Common::getOssClient();
$download_file = "download.jpg";
if (is_null($ossClient)) exit(1);

//*******************************简单使用***************************************************************

// 先把本地的example.jpg上传到指定$bucket, 命名为$object
$ossClient->uploadFile($bucketName, $object, "example.jpg");

// 图片缩放
$options = array(
    OssClient::OSS_FILE_DOWNLOAD => $download_file,
    OssClient::OSS_PROCESS => "image/resize,m_fixed,h_100,w_100", );
$ossClient->getObject($bucketName, $object, $options);
printImage("imageResize",$download_file);

// 图片裁剪
$options = array(
    OssClient::OSS_FILE_DOWNLOAD => $download_file,
    OssClient::OSS_PROCESS => "image/crop,w_100,h_100,x_100,y_100,r_1", );
$ossClient->getObject($bucketName, $object, $options);
printImage("iamgeCrop", $download_file);

// 图片旋转
$options = array(
    OssClient::OSS_FILE_DOWNLOAD => $download_file,
    OssClient::OSS_PROCESS => "image/rotate,90", );
$ossClient->getObject($bucketName, $object, $options);
printImage("imageRotate", $download_file);

// 图片锐化
$options = array(
    OssClient::OSS_FILE_DOWNLOAD => $download_file,
    OssClient::OSS_PROCESS => "image/sharpen,100", );
$ossClient->getObject($bucketName, $object, $options);
printImage("imageSharpen", $download_file);

// 图片水印
$options = array(
    OssClient::OSS_FILE_DOWNLOAD => $download_file,
    OssClient::OSS_PROCESS => "image/watermark,text_SGVsbG8g5Zu-54mH5pyN5YqhIQ", );
$ossClient->getObject($bucketName, $object, $options);
printImage("imageWatermark", $download_file);

// 图片格式转换
$options = array(
    OssClient::OSS_FILE_DOWNLOAD => $download_file,
    OssClient::OSS_PROCESS => "image/format,png", );
$ossClient->getObject($bucketName, $object, $options);
printImage("imageFormat", $download_file);

// 获取图片信息
$options = array(
    OssClient::OSS_FILE_DOWNLOAD => $download_file,
    OssClient::OSS_PROCESS => "image/info", );
$ossClient->getObject($bucketName, $object, $options);
printImage("imageInfo", $download_file);


/**
 *  生成一个带签名的可用于浏览器直接打开的url, URL的有效期是3600秒
 *-/
 $timeout = 3600;
$options = array(
    OssClient::OSS_PROCESS => "image/resize,m_lfit,h_100,w_100",
    );
$signedUrl = $ossClient->signUrl($bucketName, $object, $timeout, "GET", $options);
Common::println("rtmp url: \n" . $signedUrl);

//最后删除上传的$object
$ossClient->deleteObject($bucketName, $object);     

function printImage($func, $imageFile)
{
    $array = getimagesize($imageFile);
    Common::println("$func, image width: " . $array[0]);
    Common::println("$func, image height: " . $array[1]);
    Common::println("$func, image type: " . ($array[2] === 2 ? 'jpg' : 'png'));
    Common::println("$func, image size: " . ceil(filesize($imageFile)));
}

*/
