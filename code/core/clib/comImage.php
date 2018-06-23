<?php
//图像:缩略图/水印处理
class comImage{

    // 压缩一个图片目录
    public static function compdir($dir='', $size=500, $max=2400){
        $res = array();
        $lists = comFiles::listScan($dir);
        foreach ($lists as $file=>$row) {
            $full = "$dir/$file";
            if(strpos($file,'.jpg') && $row[1]>$size*1024){
                $res[] = self::compcut($full, $max);
                self::compress($full, 75);
            }
        }
        return $res;
    }

    // 限制图片大小, 针对jpg
    public static function compcut($full, $max=2400){
        $info = self::info($full);
        if($info['width']>$max || $info['height']>$max){
            if($info['width']>$info['height']){
                $h = $info['height']*$max/$info['width'];
                $w = $max;
            }else{
                $w = $info['width']*$max/$info['height'];
                $h = $max;
            }
            self::thumb($full, $full, $w, $h); 
        }
        return $info;
    }

    # 压缩图片, JPEG支持好，PNG良好，GIF不支持
    public static function compress($target, $quality=75){
        if($srcInfo = @getimagesize($target)){
            switch ($srcInfo[2]){
                case 1:
                    break;
                case 2:
                    $srcim =imagecreatefromjpeg($target);
                    imagejpeg($srcim,$target,$quality);
                    break;
                case 3:
                    $srcim =imagecreatefrompng($target);
                    $pngQuality = ($quality - 100) / 11.111111;
                    $pngQuality = round(abs($pngQuality));
                    imagepng($srcim,$target,$pngQuality);
                    break;
                default:
                    return;
            }
        }
    }
    
    /** 取得图像信息
     * @param string $image 图像文件名
     * @return mixed
     */
    static function info($img) {
        $imageInfo = getimagesize($img); // riff webpvp8 格式，不能获取到信息
        if( $imageInfo!== false)
         {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
            $imageSize = filesize($img);
            $info = array(
                "width"=>$imageInfo[0],
                "height"=>$imageInfo[1],
                "type"=>$imageType,
                "size"=>$imageSize,
                "mime"=>$imageInfo['mime']
            );
            return $info;
        }else {
            return false;
        }
    }

    /** 生成缩略图 (补灰边,超比例截取)
     * @param string $image  原图
     * @param string $thumbname 缩略图文件名
     * @param string $maxWidth  宽度
     * @param string $maxHeight  高度
     * @param boolean $interlace 启用隔行扫描
     * @param string $type 图像格式 
     */
    static function thumb($image,$thumbname,$maxWidth=120,$maxHeight=90,$interlace=true,$type=''){
        $ire = self::_thpos($image, $maxWidth, $maxHeight);
        if(!$ire) return false;
        extract($ire); 
        // 载入原图
        $func = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
        $srcImg = $func($image);
        // 创建缩略图
        $func = ($type!='gif' && function_exists('imagecreatetruecolor')) ? 'imagecreatetruecolor' : 'imagecreate';
        $thumbImg = $func($maxWidth, $maxHeight);
        // 填充白底
        $bg_color = imagecolorallocate($thumbImg, 204,204,204);
        imagefill($thumbImg,0,0,$bg_color);
        // 复制图片
        $func = function_exists("ImageCopyResampled") ? "ImageCopyResampled" : "ImageCopyResized";
        $func($thumbImg,$srcImg, $ox,$oy, $ix,$iy, $ow,$oh, $iw,$ih);
        if('gif'==$type || 'png'==$type) { // 设置为透明色
            imagecolortransparent($thumbImg,$bg_color);
        }else{ // 对jpeg图形设置隔行扫描
            if('jpg'==$type || 'jpeg'==$type) imageinterlace($thumbImg, $interlace?1:0);
        }
        // 生成图片
        $func = 'image'.($type=='jpg'?'jpeg':$type);
        $func($thumbImg,$thumbname);
        imagedestroy($thumbImg);
        imagedestroy($srcImg);
        return $thumbname;
    }
    // {staticroot}/icons/basic/demo_pic1.jpg
    // http://img.domain.com/imcat/demo/abc.jpg
    // {ures}/demo/abc.jpg
    static function thpic($val,$resize){
        // 还原完整路径
        $orgd = str_replace(array('{uresroot}','{staticroot}'),array(DIR_URES,DIR_STATIC),$val);
        $orgp = str_replace(array('{uresroot}','{staticroot}'),array(PATH_URES,PATH_STATIC),$val);
        $repath = read('repath','ex');
        $redir = empty($repath['dir']) ? array() : $repath['dir'];
        $reatt = empty($repath['att']) ? array() : $repath['att'];
        if(!empty($redir)){
            $orgd = str_replace(array_keys($redir),array_values($redir),$orgd); 
        }
        if(!empty($reatt)){
            $orgp = str_replace(array_keys($reatt),array_values($reatt),$orgp); 
        }
        // 目标路径
        $ext = strrchr($val,'.');
        $objd = str_replace($ext,"-$resize$ext",$orgd);
        $objp = str_replace($ext,"-$resize$ext",$orgp);
        if(strpos($objp,'://')>0){ // out-cdn/ftp:根据具体情况修改了...
            if(strpos($orgp,PATH_URES)===false) return $orgp; // http://img.xcdn.com/dir/file.ext
            $scfg = glbConfig::read('store','ex');
            if(!isset($scfg[$scfg['type']]['cut_ures'])) return $orgp;
            $cutp = $scfg[$scfg['type']]['cut_ures']; // http://domain.com/cut.php?
            return str_replace(array("(size)","(img)"),array($resize,$orgp),$cutp); 
        }elseif(file_exists($objd)){ // 有缩略图
            return $objp;
        }elseif(file_exists($orgd)){ // (本地)有原图片,
            $size = explode('x',$resize);
            $res = self::thumb($orgd,$objd,$size[0],$size[1]);
            return $res ? $objp : $orgp;
        }
        return $orgp;
    }
    
    // 描边文字水印
    static function wmtext($image, $text, $waterPos=array(9,10)){
        //读取原图像文件
        $imageInfo = self::info($image);
        $image_w = $imageInfo['width']; //取得水印图片的宽
        $image_h = $imageInfo['height']; //取得水印图片的高
        //读取水印文字配置
        $waterInfo = glbConfig::read('wmark','sy');
        $w = $waterInfo['width']; //取得水印图片的宽
        $h = $waterInfo['height']; //取得水印图片的高ctext
        if($image_w<=$w || $image_h<=$h){
            return 'file too SMALL!';
        }
        $func = "imagecreatefrom" . $imageInfo['type'];
        $image_im = $func($image);
        $pos = self::_wpos($image_w, $image_h, $w, $h, $waterPos);
        $posX = $pos[0]; $posY = $pos[1]; 
        //设定图像的混色模式
        imagealphablending($image_im, true);
        foreach(array('ctext','cborder') as $k){
            $$k = imagecolorallocate($image_im, $waterInfo[$k], $waterInfo[$k], $waterInfo[$k]);
        }
        $tfont = DIR_STATIC.$waterInfo['font'];
        $ia = array(1,-1,-1,1,0);
        $ja = array(1,1,-1,-1,0);
        for($i=0;$i<5;$i++){ //先4个象限移动1px,再到0,实现描边(Peace)
            $tcolor = empty($ia[$i]) ? $ctext : $cborder;
            imagettftext($image_im, $waterInfo['size'], 0, $posX+$ia[$i], $posY+$ja[$i], $tcolor, $tfont, $text); 
        }
        //生成水印后的图片
        $func = "image" . $imageInfo['type'];
        $func($image_im, $image);
        //释放内存
        $imageInfo = NULL;
        imagedestroy($image_im);
    }
    // 图片水印
    static function wmpic($image, $water, $waterPos=array(9,10)){
        //读取原图像文件
        $imageInfo = self::info($image);
        $image_w = $imageInfo['width']; //取得水印图片的宽
        $image_h = $imageInfo['height']; //取得水印图片的高
        //读取水印文件
        $waterInfo = self::info($water);
        $w = $waterInfo['width']; //取得水印图片的宽
        $h = $waterInfo['height']; //取得水印图片的高
        if($image_w<=$w || $image_h<=$h){
            return 'file too SMALL!';
        }
        $func = "imagecreatefrom" . $imageInfo['type'];
        $image_im = $func($image);
        $func = "imagecreatefrom" . $waterInfo['type'];
        $water_im = $func($water);
        $pos = self::_wpos($image_w, $image_h, $w, $h, $waterPos);
        $posX = $pos[0]; $posY = $pos[1]; 
        //设定图像的混色模式
        imagealphablending($image_im, true);
        imagecopy($image_im, $water_im, $posX, $posY, 0, 0, $w, $h); //拷贝水印到目标文件
        //生成水印后的图片
        $func = "image" . $imageInfo['type'];
        $func($image_im, $image);
        //释放内存
        $waterInfo = $imageInfo = NULL;
        imagedestroy($image_im);
        imagedestroy($water_im);
    }
     /** 图片/文字:水印(watermark)
     * @$image  原图
     * @$type 水印类型：0-按配置, pic-配置中的图片, text-配置中的文字, 图片path-图片水印, 其它-文字内容
     * @$$waterPos 水印位置
       - (0-9,margin=10) 0-9:0为随机,其他代表上中下9个部分位置; margin:水印边距
       - (-10,20)        距离下方10px, 右方20px
     */
    static function wmark($image, $type=0, $waterPos=array()){
        if(!file_exists($image)){ //检查图片是否存在
            return 'file NOT exists!';
        }
        $wcfgs = glbConfig::read('wmark','sy');
        if(empty($waterPos)) $waterPos = $wcfgs['pos'];
        if(empty($type) || in_array($type,array('pic','text'))){
            if(empty($type)) $type = $wcfgs['type'];
            if($type=='pic' && file_exists(DIR_SKIN.$wcfgs['plogo'])){
                return self::wmpic($image, DIR_SKIN.$wcfgs['plogo'], $waterPos);
            }else{
                return self::wmtext($image, $wcfgs['stext'], $waterPos);
            }
        }elseif(file_exists($type)){
            return self::wmpic($image, $type, $waterPos);
        }else{
            return self::wmtext($image, $type, $waterPos);
        }
    }
    // 水印位置(0-9,margin=10), (+-x,+-y)
    static function _wpos($image_w, $image_h, $w, $h, $waterPos=array(0,10)){
        $pos = $x = $waterPos[0]; //九宫格位置,坐标位置x
        $gap = $y = $waterPos[1]; //九宫格边距,坐标位置y
        $posX = $posY = 0;
        if(abs($pos)<10){
            switch ($pos) {
            case 1: //1为顶端居左
                $posX = $gap;
                $posY = $gap;
            break;
            case 2: //2为顶端居中
                $posX = ($image_w - $w) / 2;
                $posY = $gap;
            break;
            case 3: //3为顶端居右
                $posX = $image_w - $w - $gap;
                $posY = $gap;
            break;
            case 4: //4为中部居左
                $posX = $gap;
                $posY = ($image_h - $h) / 2;
            break;
            case 5: //5为中部居中
                $posX = ($image_w - $w) / 2;
                $posY = ($image_h - $h) / 2;
            break;
            case 6: //6为中部居右
                $posX = $image_w - $w - $gap;
                $posY = ($image_h - $h) / 2;
            break;
            case 7: //7为底端居左
                $posX = $gap;
                $posY = $image_h - $h - $gap;
            break;
            case 8: //8为底端居中
                $posX = ($image_w - $w) / 2;
                $posY = $image_h - $h - $gap;
            break;
            case 9: //9为底端居右
                $posX = $image_w - $w - $gap;
                $posY = $image_h - $h - $gap;
            break;
            default: //0,随机
                $posX = rand(0, ($image_w - $w));
                $posY = rand(0, ($image_h - $h));
            break;
            }
            if($posX<0) $posX = rand(0, ($image_w - $w));    
            if($posY<0) $posX = rand(0, ($image_h - $h));    
        }else{
            $posX = $x<0 ? $image_w + $x: $x;
            $posY = $y<0 ? $image_h + $y: $y;
        }
        return array($posX, $posY);
    }

    static function _thpos($image, $ow, $oh){
        $img = self::info($image);
        if(!$img) return false;
        $iw = $img['width'];
        $ih = $img['height'];
        $res = array(
            'type'=>$img['type'],
            'ix'=>0,'iy'=>0,'iw'=>$iw,'ih'=>$ih,
            'ox'=>0,'oy'=>0,'ow'=>$ow,'oh'=>$oh,
        );
        if($iw>$ow && $ih>$oh){
            $is = $iw/$ih; $os = $ow/$oh;
            if($is>$os){ // 太宽(截取)
                $ew = $ih * $os;
                $dw = intval(($iw-$ew)/2);
                $res['ix'] = $dw; $res['iw'] = $ew;
            }elseif($os>$is){ // 太高(截取)
                $eh = $iw / $os;
                $dh = intval(($ih-$eh)/2);
                $res['iy'] = $dh; $res['ih'] = $eh;
            }
        }elseif($iw>$ow){ // 太宽(截取+补边)
            $dw = intval(($iw-$ow)/2);
            $dh = intval(($oh-$ih)/2);
            $res['oy'] = $dh; $res['oh'] = $ih;
            $res['ix'] = $dw; $res['iw'] = $ow;
        }elseif($ih>$oh){ // 太高(截取+补边)
            $dw = intval(($ow-$iw)/2);
            $dh = intval(($ih-$oh)/2);
            $res['ox'] = $dw; $res['ow'] = $iw;
            $res['iy'] = $dh; $res['ih'] = $oh;
        }else{ // 
            return false; //$res
        }
        return $res; 
    }

}

/*

*/
