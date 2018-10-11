<?php
namespace imcat;

/*
 * 中文圆形印章类
 * @author lkk/lianq.net
 * @create on 10:03 2012-5-29
 * @example:
 *  $seal = new exaCSeal('某某单位通用章',75,18,16,40);
 *  $seal->doImg();
 */ 

class exaCSeal {
    
    static $fpath = '/media/fonts/simkai.ttf'; //指定的字体,simkai.ttf
    
    private $sealString;    //印章字符  
    private $sealRadius;    //印章半径
    private $startRadius;   //五角星半径
    private $fontSize;      //指定字体大小
    private $inRadius;      //内圆半径
    
    private $backGround;    //印章颜色
    private $centerDot;     //圆心坐标
    private $img;           //图形资源句柄
    
    private $width;         //图片宽度
    private $height;        //图片高度
    
    private $strMaxLeng = 12 ;    //最大字符长度
    private $rimWidth = 6;      //边框厚度
    private $startAngle = 0;    //五角星倾斜角度
    private $charAngle = 0;     //字符串倾斜角度
    private $spacing = 0;       //字符间隔角度
 
    //构造方法
    public function __construct($str ='', $rad = 75, $strad = 24, $fsize = 16, $inrad =0){
        $this->sealString    = empty($str) ? basLang::show('core.seal_defstr') : $str;
        $this->sealRadius    = $rad;
        $this->startRadius   = $strad;
        $this->fontSize      = $fsize;
        $this->inRadius      = $inrad;   //默认0,没有
        $this->centerDot     = array('x'=>$rad, 'y'=>$rad);
    }
 
    //创建图片资源
    private function createImg(){
        $this->width      = 2 * $this->sealRadius;
        $this->height     = 2 * $this->sealRadius;
        $this->img        = imagecreate($this->width, $this->height);
        imagecolorresolvealpha($this->img,255,255,255,127);
        $this->backGround = imagecolorallocate($this->img,255,0,0);
    }
 
    //画印章边框
    private function drawRim(){
        for($i=0;$i<$this->rimWidth;$i++){
            imagearc($this->img,$this->centerDot['x'],$this->centerDot['y'],$this->width - $i,$this->height - $i,0,360,$this->backGround);
        }
    }
 
    //画内圆
    private function drawInnerCircle(){
        imagearc($this->img,$this->centerDot['x'],$this->centerDot['y'],2*$this->inRadius,2*$this->inRadius,0,   45,$this->backGround);
        imagearc($this->img,$this->centerDot['x'],$this->centerDot['y'],2*$this->inRadius,2*$this->inRadius,135,360,$this->backGround);
    }
 
    //画字符串
    private function drawString(){
        //编码处理
        $charset = mb_detect_encoding($this->sealString);
        if($charset != 'UTF-8'){
            $this->sealString = mb_convert_encoding($this->sealString, 'UTF-8', 'GBK');
        }
        //相关计量
        $charRadius = $this->sealRadius - $this->rimWidth - $this->fontSize - 4;  //字符串半径
        $leng   = mb_strlen($this->sealString,'utf8');   //字符串长度
        if($leng > $this->strMaxLeng) $leng = $this->strMaxLeng;
        $avgAngle   = 360 / ($this->strMaxLeng); //平均字符倾斜度
        //拆分并写入字符串
        $words  = array();  //字符数组
        $font = DIR_STATIC.self::$fpath; 
        for($i=0;$i<$leng;$i++){
            $words[] = mb_substr($this->sealString,$i,1,'utf8');
            $r = 630 + $this->charAngle + $avgAngle*($i - $leng/2) + $this->spacing*($i-1);       //坐标角度
            $R = 720 - $this->charAngle + $avgAngle*($leng-2*$i-1)/2 + $this->spacing*(1-$i); //字符角度
            $x = $this->centerDot['x'] + $charRadius * cos(deg2rad($r));    //字符的x坐标
            $y = $this->centerDot['y'] + $charRadius * sin(deg2rad($r));    //字符的y坐标
            imagettftext($this->img, $this->fontSize, $R, $x, $y, $this->backGround, $font, $words[$i]);
        }
    }   
 
    //画五角星
    private function drawStart(){
        $ang_out = 18 + $this->startAngle;
        $ang_in  = 56 + $this->startAngle;
        $rad_out = $this->startRadius;
        $rad_in  = $rad_out * 0.382;
        for($i=0;$i<5;$i++){
            //五个顶点坐标
            $points[] = $rad_out * cos(2*M_PI/5*$i - deg2rad($ang_out)) + $this->centerDot['x'];
            $points[] = $rad_out * sin(2*M_PI/5*$i - deg2rad($ang_out)) + $this->centerDot['y'];
            //内凹的点坐标
            $points[] = $rad_in * cos(2*M_PI/5*($i+1) - deg2rad($ang_in)) + $this->centerDot['x'];
            $points[] = $rad_in * sin(2*M_PI/5*($i+1) - deg2rad($ang_in)) + $this->centerDot['y'];
        }
        imagefilledpolygon($this->img, $points, 10, $this->backGround);
    }
 
    //输出
    private function outPut(){
        basEnv::obClean();
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
        die();
    }
 
    //对外生成
    function doImg(){
        $this->createImg();
        $this->drawRim();
        $this->drawInnerCircle();
        $this->drawString();
        $this->drawStart();
        $this->outPut();
    }
    
    static function show($indep,$name='',$pos='',$tpl='',$eid='sceal_out'){ 
        $tpl = empty($tpl) ? "<p class='cseal_out' title='".basLang::show('core.seal_move')."'><i onclick='sealMove(this)' class='cseal_in {pos}' style='background-image:url({dept})'><b class='cseal_text'>{name}</b></i></p>" : $tpl;
        $name = empty($name) ? basLang::show('core.seal_mark') : $name;
        if(file_exists(DIR_STATIC."/icons/indep/seal-$indep.png")){
           $file = "seal-$indep.png";
        }else{
           $file = "seal-_comm.png";
        }
        $path = PATH_STATIC."/icons/indep/$file";
        $tpl = str_replace(array('{dept}','{name}','{pos}'),array($path,$name,$pos),$tpl);
        return $tpl;
    }
    
}

