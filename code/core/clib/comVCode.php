<?php
//生成验证码
class comVCode {

    private $im;            //图片对象
    private $dis;           //图片对象
    private $imw = 160;     //宽
    private $imh = 40;      //高
    private $mod = '';      //模块
    private $ttf = '';      //字体文件 //array
    private $ttOld = '';
    private $clOld = Null;
    private $code = '';     //字符
    private $corg = '';     //字符
    private $cTab = "";
    private $oTab = '';

    function __construct($mod='(istest)', $ttf='', $corg='0'){ 
        $this->mod = $mod; 
        $this->ttf = $ttf;
        $this->corg = $corg;
        // 显示图片一般图片
        if(function_exists('imagecreate')){ 
            $this->show();
        }else{ //bmp
            glbError::show("NO function:imagecreate!");
        }
    }
    function init(){
        $tab = array(0,40,80,120,160,200,240);
        foreach($tab as $v1){ foreach($tab as $v2){ foreach($tab as $v3){
            $this->cTab .= (empty($this->cTab) ? '' : ';')."$v1,$v2,$v3";
        } } }
        if($this->mod=='(emtel)'){
            $this->code = $this->corg;
            $this->imw = 20+strlen($this->corg)*20;
        }else{
            $this->code = self::strRand();
        }
        $this->im = imagecreate($this->imw, $this->imh); 
        // 填充
        $t = imagecolorallocate($this->im, 234, 234, 234);
        $this->bgColor = $this->rndColor(3); 
        imagefill($this->im,0,0,$this->bgColor);
    }
    // 输出
    function output(){
        $typa = array(1=>'gif', 2=>'png', 3=>'jpeg'); 
        $type = $typa[rand(1,3)]; 
        basEnv::obClean();
        header("Pragma:no-cache");
        header("Cache-control:no-cache");
        header("Content-type:image/$type");
        $func = "image$type"; //imagejpeg
        $func($this->im); 
    }
    // 显示
    function show(){
        $this->init();
        safComm::formCVimg($this->mod, $this->code, 'save'); 
        // 干扰: 点,线,特殊字符,雪花
        if($this->mod!='(emtel)'){
            $r = rand(1,4);
            if($r==1) $this->_rndPixels();
            if($r==2) $this->_rndLines();
            if($r==3) $this->_rndChars();
            if($r==4) $this->_rndSnow();
        }
        $this->drawStr(); // Draw字符,划边框
        //$this->distOrtion(); // 扭曲文字 
        $this->output();
        imagedestroy($this->im); 
        if(is_resource($this->dis)){
            imagedestroy($this->dis);
        }
    }
    // Draw字符
    function drawStr(){        
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
            $y     = $this->mod=='(emtel)' ? mt_rand(24,30) : mt_rand(27,30);
            $ffile = $this->rndFonts();
            if($this->mod=='(emtel)'){
                $color = $this->rndColor();
                imagettftext($this->im, $size, $angle, $x, $y, $color, $ffile, $chr); 
            }else{
                // 外围描边(空心效果)
                $color = $this->rndColor(1);
                imagettftext($this->im, $size, $angle, $x+1, $y, $color, $ffile, $chr); // x
                imagettftext($this->im, $size, $angle, $x, $y+1, $color, $ffile, $chr); // y
                // 多一像素描边(阴影效果)
                $r = mt_rand(0,9); //$color = $this->rndColor(2);
                if($r<5){
                    imagettftext($this->im, $size, $angle, $x-2, $y, $color, $ffile, $chr); // x-
                    imagettftext($this->im, $size, $angle, $x, $y-2, $color, $ffile, $chr); // y-
                }
                // 文字本身
                $color = $this->rndColor(2);
                imagettftext($this->im, $size, $angle, $x, $y, $color, $ffile, $chr);
            }
        }
    }
    // 随机颜色
    function rndColor($init=0){
        $ctab = ($init==2 && $this->oTab!='') ? $this->oTab : $this->cTab;
        $xTab = explode(";",$ctab);
        $xArr = explode(",",$xTab[mt_rand(0,count($xTab)-1)]);
        $xCol01 = $xArr[0]+mt_rand(-12,12);  
        $xCol02 = $xArr[1]+mt_rand(-12,12);
        $xCol03 = $xArr[2]+mt_rand(-12,12);
        $xColor = imagecolorallocate($this->im, $xCol01, $xCol02, $xCol03);
        if($init==1 || $init==3) $this->setCtab($xTab,$xArr,$init);
        return $xColor;
    }
    // 随机Font
    public function rndFonts(){
        if(is_string($this->ttf)){
            $tti = $this->ttf;
        }else{
            $tti = $this->ttf[mt_rand(0,count($this->ttf)-1)];
            while($this->ttOld==$tti){
                $tti = $this->ttf[mt_rand(0,count($this->ttf)-1)];
            }
            $this->ttOld = $tti; 
        } 
        $ffile = DIR_STATIC.'/media/fonts/'.$tti.'.ttf'; 
        return $ffile;
    }
    // 干扰象素
    function _rndPixels(){ //加入干扰象素
        for($i=0;$i<200;$i++){  
           imagesetpixel($this->im, rand()%160 , rand()%100 , $this->rndColor());    
        }
    }
    // 干扰线
    function _rndLines(){ 
        $w = $this->imw-2;
        $h = $this->imh-2;
        $cnt = mt_rand(5,8);
        for($j=2;$j<$cnt;$j++){
            imageline($this->im, rand(2,$w),rand(2,$h),rand(2,$w),rand(2,$h), $this->rndColor()
            ); //18
        }
        imagerectangle($this->im, 0, 0, $this->imw-1, $this->imh-1, $this->rndColor()); // 边框 
    }
    // 干扰字符
    function _rndChars(){ 
        if(empty($this->ttf)){ return; }
        $cnt = mt_rand(5,10); //《!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~》// All:32 '+','-',
        $chars = array('!',"'",'(',')',',','.','/',":",";","?","^","_","`","~");
        for($i=1; $i<=$cnt; $i++){
            imagestring($this->im,mt_rand(1,5),mt_rand(1,$this->imw),mt_rand(1,$this->imh),$chars[mt_rand(0,4)],$this->rndColor());
        }
    }
    // 雪花
    function _rndSnow(){  
        for ($i=0;$i<30;$i++) {
            $color = imagecolorallocate($this->im,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
            imagestring($this->im,mt_rand(2,5),mt_rand(0,$this->imw),mt_rand(0,$this->imh),'*',$color);
        }
    }
    // 扭曲文字
    public function distOrtion(){
        $rate = floor(0.08 * $this->imh);   // 扭曲程度
        $this->dis = imagecreatetruecolor($this->imw, $this->imh);
        imagefill($this->dis, 0, 0, $this->bgColor);
        for ($x = 0; $x < $this->imw; $x++) {
            for ($y = 0; $y < $this->imh; $y++) {
                $rgb = imagecolorat($this->im, $x, $y);
                if( (int)($x + sin($y / $this->imh * 2 * M_PI) * $rate) <= $this->imw && (int)($x + sin($y / $this->imh * 2 * M_PI) * $rate) >= 0 ) 
                {
                    imagesetpixel($this->dis, (int)($x + sin($y / $this->imh * 2 * M_PI - M_PI * 0.1) * $rate) , $y, $rgb);
                }
            }
        }
        $this->im = $this->dis;
    }
    // 颜色表
    function setCtab($xTab,$xArr,$init){
        $oTab = '';
        $gap = 100; //60,100,80
        foreach ($xTab as $val) {
            $tmp = explode(",",$val);
            $g0 = abs($xArr[0]-$tmp[0])>$gap;
            $g1 = abs($xArr[1]-$tmp[1])>$gap;
            $g2 = abs($xArr[2]-$tmp[2])>$gap;
            if($g0&&$g1 || $g1&&$g2 || $g0&&$g2){
                $oTab .= ($oTab ? ';' : '')."$val";
            }
        }
        if($init==3){
            $this->cTab = $oTab;
        }else{
            $this->oTab = $oTab;
        }
    }
    // *** 随机码
    static function strRand(){
        global $_cbase;
        $vimg = $_cbase['ucfg']['vimg'];
        $type = in_array($vimg,array('0','H','K')) ? $vimg : 'K';
        $str = basKeyid::kidRand($type,rand(4,5));
        return strtoupper($str);
    }
    
}

