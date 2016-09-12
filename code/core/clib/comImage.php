<?php
//图像:缩略图/水印处理
class comImage{

	/**
	 +----------------------------------------------------------
	 * 取得图像信息
	 +----------------------------------------------------------
	 * @param string $image 图像文件名
	 +----------------------------------------------------------
	 * @return mixed
	 +----------------------------------------------------------
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

	/**
	 +----------------------------------------------------------
	 * 生成缩略图
	 +----------------------------------------------------------
	 * @param string $image  原图
	 * @param string $thumbname 缩略图文件名
	 * @param string $type 图像格式 
	 * @param string $maxWidth  宽度
	 * @param string $maxHeight  高度
	 * @param boolean $interlace 启用隔行扫描
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 */
	static function thumb($image,$thumbname,$type='',$maxWidth=120,$maxHeight=90,$interlace=true)
	{
		// 获取原图信息
		$info = self::info($image);
		if($info !== false){
			$srcWidth  = $info['width'];
			$srcHeight = $info['height'];
			$type = empty($type)?$info['type']:$type;
			$type = strtolower($type);
			$interlace  =  $interlace? 1:0;
			unset($info);
			$scale = min($maxWidth/$srcWidth, $maxHeight/$srcHeight); // 计算缩放比例
			if($scale>=1) { // 超过原图大小不再缩略
				$width   =  $srcWidth;
				$height  =  $srcHeight;
			}else{ // 缩略图尺寸
				$width  = (int)($srcWidth*$scale);
				$height = (int)($srcHeight*$scale);
			}
			// 载入原图
			$createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
			$srcImg	= $createFun($image);
			//创建缩略图
			if($type!='gif' && function_exists('imagecreatetruecolor'))
				$thumbImg = imagecreatetruecolor($width, $height);
			else
				$thumbImg = imagecreate($width, $height);
			// 复制图片
			if(function_exists("ImageCopyResampled"))
				imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $srcWidth,$srcHeight);
			else
				imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height,  $srcWidth,$srcHeight);
			if('gif'==$type || 'png'==$type) {
				$background_color  =  imagecolorallocate($thumbImg,  0,255,0);  //  指派一个绿色
				imagecolortransparent($thumbImg,$background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
			}
			// 对jpeg图形设置隔行扫描
			if('jpg'==$type || 'jpeg'==$type) imageinterlace($thumbImg,$interlace);
			// 生成图片
			$imageFun = 'image'.($type=='jpg'?'jpeg':$type);
			$imageFun($thumbImg,$thumbname);
			imagedestroy($thumbImg);
			imagedestroy($srcImg);
			return $thumbname;
		 }
		 return false;
	}
	
	// 描边文字水印
	static function wmtext($image, $text, $waterPos=array(9,10)){
		//读取原图像文件
		$imageInfo = self::info($image);
		$image_w = $imageInfo['width']; //取得水印图片的宽
		$image_h = $imageInfo['height']; //取得水印图片的高
		//读取水印文字配置
		$waterInfo = glbConfig::read('wmark','ex');
		$w = $waterInfo['width']; //取得水印图片的宽
		$h = $waterInfo['height']; //取得水印图片的高ctext
		if($image_w<=$w || $image_h<=$h){
			return 'file too SMALL!';
		}
		$func = "imagecreatefrom" . $imageInfo['type'];
		$image_im = $func($image);
		$pos = self::_pos($image_w, $image_h, $w, $h, $waterPos);
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
		$pos = self::_pos($image_w, $image_h, $w, $h, $waterPos);
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
	
	 /**
	 * +----------------------------------------------------------
	 * 图片/文字:水印(watermark)
	 * +----------------------------------------------------------
	 * @$image  原图
	 * @$type 水印类型：0-按配置, pic-配置中的图片, text-配置中的文字, 图片path-图片水印, 其它-文字内容
	 * @$$waterPos 水印位置
	   - (0-9,margin=10) 0-9:0为随机,其他代表上中下9个部分位置; margin:水印边距
	   - (-10,20)		距离下方10px, 右方20px
	 * +----------------------------------------------------------
	 */
	static function wmark($image, $type=0, $waterPos=array()){
		if(!file_exists($image)){ //检查图片是否存在
			return 'file NOT exists!';
		}
		$wcfgs = glbConfig::read('wmark','ex');
		if(empty($waterPos)) $waterPos = $wcfgs['pos'];
		if(empty($type) || in_array($type,array('pic','text'))){
			if(empty($type)) $type = $wcfgs['type'];
			if($type=='pic' && file_exists(DIR_ROOT.$wcfgs['plogo'])){
				return self::wmpic($image, DIR_ROOT.$wcfgs['plogo'], $waterPos);
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
	static function _pos($image_w, $image_h, $w, $h, $waterPos=array(0,10)){
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

}

