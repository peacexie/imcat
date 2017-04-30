<?php
//生成验证码
class comVCode {

    private $im;            //图片对象
    private $imw = 160;     //宽
    private $imh = 40;      //高
    private $mod = '';      //模块
    private $ttf = '';      //字体文件
    private $code = '';     //字符
    private $color = '';    //颜色

    function __construct($mod='(istest)', $ttf='', $type='0'){ 
        //if($mod=='vsms4') $mod = '(emtel)';
        $this->mod = $mod; 
        $this->ttf = $ttf;
        $this->color = rand(1,2);
        // 显示图片一般图片
        if(function_exists('imagecreate')){ 
            if($this->mod=='(emtel)'){
                $this->code = $type;
                $this->imw = 20+strlen($type)*20;
            }else{
                $this->code = self::strRand();
            }
            $this->im = imagecreate($this->imw, $this->imh); 
            safComm::formCVimg($this->mod, $this->code, 'save'); 
            $this->show();
        }else{ //bmp
            glbError::show("NO function:imagecreate!");
        }
    }
    
    // 显示
    function show(){
        // 填充
        if(empty($type) || $this->mod=='(emtel)'){
            $color = imagecolorallocate($this->im, 255,255,255);
        }else{
            $color = imagecolorallocate($this->im, 1,1,1);
        }
        imagefill($this->im,0,0,$color);
        // 干扰: 线,点,特殊字符
        $this->rnd_lines();
        if($this->mod!='(emtel)'){
            $this->rnd_pixels();
            $this->rnd_chars();
        }
        // Draw字符,划边框
        $this->draw_str();
        imagerectangle($this->im, 0, 0, $this->imw-1, $this->imh-1, $this->rnd_color());
        // 文件格式
        $typa = array(1=>'gif', 2=>'png', 3=>'jpeg'); 
        $type = $typa[rand(1,3)]; 
        // output
        basEnv::obClean();
        header("Pragma:no-cache");
        header("Cache-control:no-cache");
        header("Content-type:image/$type");
        $func = "image$type"; //imagejpeg
        $func($this->im); 
        imagedestroy($this->im); 
    }
    
    // Draw字符
    function draw_str(){        
        for($i=0;$i<strlen($this->code);$i++) {
            $chr = $this->code[$i];
            if($this->mod=='(emtel)'){
                $size = 20;
            }elseif(ord($chr)>96){
                $size = mt_rand(22,26);
            }else{
                $size = mt_rand(16,20); 
            }
            $angle = $this->mod=='(emtel)' ? 0              : mt_rand(-20,20);
            $x     = $this->mod=='(emtel)' ? $i*20+8        : mt_rand(15,20)+$i*26;
            $y     = $this->mod=='(emtel)' ? mt_rand(27,29) : mt_rand(28,30);
            if(empty($this->ttf)){
                imagestring($this->im, mt_rand(1,5), $x, $y-15, $chr, $this->rnd_color($this->im));
            }else{
                $ffile = DIR_STATIC.'/media/fonts/'.$this->ttf.'.ttf'; 
                imagettftext($this->im, $size, $angle, $x, $y, $this->rnd_color(), $ffile, $chr); 
            }
        }
    }
    
    // 随机颜色
    function rnd_color($type=0){
        if(empty($type) || $this->mod=='(emtel)'){
            $xColArr = explode(";","20,220,20;20,220,40;20,240,20;20,240,40;40,220,20;40,220,40;40,240,20;40,240,40;20,20,220;20,20,240;20,40,220;20,40,240;40,20,220;40,20,240;40,40,220;40,40,240;220,20,20;220,20,40;220,20,220;220,20,240;220,40,20;220,40,40;220,40,220;220,40,240;240,20,20;240,20,40;240,20,220;240,20,240;240,40,20;240,40,40;240,40,220;240,40,240");            
        }else{ 
            $xColArr = explode(";","20,220,20;20,220,40;20,240,20;20,240,40;40,220,20;40,220,40;40,240,20;40,240,40;240,240,220;240,240,240;240,250,220;240,240,250;240,220,230;250,250,240;240,240,250;250,250,250;220,20,20;220,20,40;220,20,220;220,20,240;220,40,20;220,40,40;220,40,220;220,40,240;240,20,20;240,20,40;240,20,220;240,20,240;240,40,20;240,40,40;240,40,220;240,40,240");
        }
        $xColStr = explode(",",$xColArr[mt_rand(0,31)]);
        $xCol01 = $xColStr[0]+mt_rand(-12,12);
        $xCol02 = $xColStr[1]+mt_rand(-12,12);
        $xCol03 = $xColStr[2]+mt_rand(-12,12);
        $xColor = imagecolorallocate($this->im, $xCol01, $xCol02, $xCol03);
        return $xColor;
    }
    // 干扰线
    function rnd_lines(){ 
        $w = $this->imw-2;
        $h = $this->imh-2;
        $cnt = mt_rand(2,3);
        for($j=2;$j<$cnt;$j++){
            imageline($this->im
                , rand(2,$w),rand(2,$h),rand(2,$w),rand(2,$h)
                , $this->rnd_color($this->im)
            ); //18
        }
    }
    // 干扰象素
    function rnd_pixels(){ //加入干扰象素
        for($i=0;$i<200;$i++){  
           imagesetpixel($this->im, rand()%160 , rand()%100 , $this->rnd_color($this->im));    
        }
    }
    // 干扰字符
    function rnd_chars(){ 
        if(empty($this->ttf)){ return; }
        $im = $this->im;
        $imw = $this->imw;
        $imh = $this->imh;
        $cnt = mt_rand(2,3); //《!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~》// All:32 '+','-',
        $chars = array('!',"'",'(',')',',','.','/',":",";","?","^","_","`","~");
        for($i=1; $i<=$cnt; $i++){
            imagestring($im,mt_rand(1,5),mt_rand(1,$imw),mt_rand(1,$imh),$chars[mt_rand(0,4)],$this->rnd_color($im));
        }
    }
    
    // *** 随机码
    static function strRand(){
        $vimg = cfg('ucfg.vimg');
        $type = in_array($vimg,array('0','H','K')) ? $vimg : 'K';
        $str = basKeyid::kidRand($type,rand(3,5));
        return $str;
    }
    
}

