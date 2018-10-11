<?php
namespace imcat;

// 阿里云服务相关actions
class aliActs
{
	public $oss = null;
    public $cburl = "";
	
    public function _initialize()
	{
		$this->oss = new aliOss();
        $this->cburl = url('api/aliacts/webupJscb','',true,true);
        # 'http://test.xxx.com/oss/php/callback.php';
    }
    // (管理员)权限认证
    public function permCheck(){
        $adminMode = new Admin;
        if(!$adminMode->isLogin()){
            $res = ['code'=>1, 'msg'=>'Please Login!'];
            die(json_encode($res));
        }
    }
    // (回调)安全认证???
    public function liveCbCheck(){
        $cip = request()->ip();
        if(!$cip || $cip!='100.11.22.256'){
            #die('Error-1!');
        }
        $stream = input('stream');
        $ent = input('ent'); $enc = input('enc');
        $cks = md5("$stream@$ent");
        if(time()-$ent>300 || $enc!=$cks){
            die('Error-2!');
        }
        //return;
    }

    public function index(){
        return 'hi, ali!';
    }

    // 直播:录制/推短流:回调(通知)
    public function liveNotifyCb()
    {
        $this->tester(__FUNCTION__);
        $this->liveCbCheck();
        $stream = input('stream'); // live_123, dsfc_123
        $playstatus = input('playstatus',''); // start, stop, finish
        if(!in_array($playstatus,['start','stop','finish']))
            return json('参数错误');
        if(substr($stream,0,5)=='live_'){ // 直播
            $id = substr($stream,5);
            $data = array('playstatus'=>$playstatus);
            $eres = model('live')->where('id',$id)->data($data)->update();
            $res = $eres;
            if($res && $playstatus=='finish'){
                // 增加一条直播视频
                $datav['live_id'] = $id;
                $datav['title'] = date('Y-m-d H:i:s',input('start/d')).'-'.date('Y-m-d H:i:s',input('stop/d'));
                $datav['sort'] = '500';
                $datav['video'] = '{player}'.input('uri/s');
                $datav['create_time'] = date('Y-m-d H:i:s',input('start/d'));
                $datav['update_time'] = date('Y-m-d H:i:s',input('stop/d'));
                model('live_video')->save($datav);
            }
        }else{
            $res = '其他模型,另外处理';
        }
        return json($res);
    }

    // Oss上传:获取policy签名(原始demo)
    public function webupJsget()
    {
        $this->permCheck();
        // confog
        $id = $this->oss->accessKeyId;
        $key = $this->oss->accessKeySecret;
        $host = 'http://'.$this->oss->endpoint;
        $callbackUrl = $this->cburl;
        $dir = config('site_cid')."/";
        $end = time() + 20; //设置该policy超时时间是10/30s. 即这个policy过了这个有效时间，将不能访问
        $base64_callback_body = $this->oss->getCbBody($callbackUrl);
        $policy = $this->oss->getPolicy($dir, $end);
        $signature = base64_encode(hash_hmac('sha1', $policy, $key, true));
        $response = array();
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        #$response['cburl'] = $callbackUrl; # 测试,正式不要暴露
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        echo json_encode($response);
    }
    // Oss上传:获取policy签名(自定义)
    public function webupJscfg()
    {
        $oss = $this->oss;
        $dir = config('site_cid')."/";
        $end = time() + 20; //设置该policy超时时间是10/30s. 即这个policy过了这个有效时间，将不能访问
        $data['OSSAccessKeyId'] = $id = $oss->accessKeyId;
        $key = $oss->accessKeySecret;
        $data['policy'] = $policy = $oss->getPolicy($dir, $end, 0);
        $data['Signature'] = base64_encode(hash_hmac('sha1', $policy, $key, true));
        $data['key'] = $dir.date('Ym').'/'.date('dHis').'c'.rand(100,999);
        $data['callback'] = $oss->getCbBody($this->cburl);
        #$data['cburl'] = $this->cburl; # 测试,正式不要暴露
        $data['success_action_status'] = 201;
        echo json_encode($data);
    }
    // Oss上传:成功回调
    public function webupJscb()
    {
        // 1.获取OSS的签名header和公钥url header
        $authorizationBase64 = "";
        $pubKeyUrlBase64 = "";
        // 注意：如果要使用HTTP_AUTHORIZATION头，你需要先在apache或者nginx中设置rewrite
        if (isset($_SERVER['HTTP_AUTHORIZATION'])){
            $authorizationBase64 = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['HTTP_X_OSS_PUB_KEY_URL'])){
            $pubKeyUrlBase64 = $_SERVER['HTTP_X_OSS_PUB_KEY_URL'];
        }

        if ($authorizationBase64 == '' || $pubKeyUrlBase64 == ''){
            header("http/1.1 403 Forbidden");
            exit('e403a');
        }
        // 2.获取OSS的签名
        $authorization = base64_decode($authorizationBase64);
        // 3.获取公钥
        $pubKeyUrl = base64_decode($pubKeyUrlBase64);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $pubKeyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $pubKey = curl_exec($ch);
        if ($pubKey == "")
        {
            //header("http/1.1 403 Forbidden");
            exit('e403b');
        }
        // 4.获取回调body
        $body = file_get_contents('php://input');
        #file_put_contents('./test-s2'.date('His').'.txt', $body);
        // 5.拼接待签名字符串
        $authStr = '';
        $path = $_SERVER['REQUEST_URI'];
        $pos = strpos($path, '?');
        if($pos === false){
            $authStr = urldecode($path)."\n".$body;
        }else{
            $authStr = urldecode(substr($path, 0, $pos)).substr($path, $pos, strlen($path) - $pos)."\n".$body;
        }
        // 6.验证签名
        $ok = openssl_verify($authStr, $authorization, $pubKey, OPENSSL_ALGO_MD5);
        if ($ok == 1){
            header("Content-Type: application/json");
            $fname = input('filename');
            $size = input('size');
            $mimeType = input('mimeType');
            $data = ['url'=>"{player}$fname",'title'=>"$fname($mimeType)"];
            $res = ['code'=>1, 'msg'=>'上传成功', 'data'=>$data, 'url'=>'', 'wati'=>3];
            //$this->success('上传成功','',$data); // Response body is not valid json format.
            echo json_encode($res);
        }else{
            //header("http/1.1 403 Forbidden");
            exit('e403c');
        }
    }

    // Oss上传:回调(测试用)
    public function webupPostcb()
    {
        $data = array("Status"=>"OK");
        echo json_encode($data);
    }

    # tester
    private function tester($act){
        //if(!$this->test) return;
        $data = input('get.'); // request()->get(); //$_GET;
        $debug['cip'] = request()->ip();
        $debug['cref'] = empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
        $debug['ua'] = empty($_SERVER['HTTP_USER_AGENT']) ? '' : $_SERVER['HTTP_USER_AGENT'];
        $debug['hostname'] = empty($_SERVER['HOSTNAME']) ? '' : $_SERVER['HOSTNAME'];
        $debug['qlen'] = strlen($_SERVER['QUERY_STRING']);
        //$debug['server'] = $_SERVER;
        $debug['method'] = $act;
        $data['_debug'] = $debug;
        $data = var_export($data, 1);
        file_put_contents("../runtime/debug/$act-".date('md-His').'.txt',$data);
        echo "### isTester : \n$data\n\n";
    }
}
