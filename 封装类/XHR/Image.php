<?php
namespace framework;
class Image
{
	//文件路径
	protected $path;
	//是否启用随机名字
	protected $isRandName;
	//文件后缀类型
	protected $type;

	public function __construct($path = './upload/', $isRandName = true, $type = 'png')
	{
		$this->path = $path;
		$this->isRandName = $isRandName;
		$this->type = $type;
	}

	public function water($imagePath, $waterPath, $position, $tmd = 100, $prefix = 'water_')
	{
		//判断文件路径是否存在
		if (!file_exists($imagePath) || !file_exists($waterPath)) {
			exit('文件不存在');
		}

		//获取图片的相关信息  宽度,高度 mime类型
		$imageInfo = self::getImageInfo($imagePath);
		$waterInfo = self::getImageInfo($waterPath);

		//判断水印的图片是否能够贴上去
		if (!$this->checkImage($imageInfo, $waterInfo)) {
			exit('水印太大');
		}

		//获取图片资源
		$imageRes = self::openImage($imagePath);
		$waterRes = self::openImage($waterPath);

		//计算位置
		$pos = $this->getPosition($position, $imageInfo, $waterInfo);
		//把水印贴上去
		imagecopymerge($imageRes, $waterRes, $pos['x'], $pos['y'], 0, 0, $waterInfo['width'], $waterInfo['height'], $tmd);

		//得到新的文件名
		$newName = $this->getNewName($imagePath, $prefix);
		//得到新路径
		$newPath = rtrim($this->path,'/').'/'.$newName;

		//保存文件
		$this->savePath($imageRes, $newPath);

		//销毁图像资源
		imagedestroy($imageRes);
		imagedestroy($waterRes);
		return $newPath;

	}

	public function suofang($image, $width, $height, $prefix = 'sf_')
	{
		//得到原图的宽高
		$info = self::getImageInfo($image);
		//根据原来图片的宽高和传递进来的宽高计算宽高
		$size = $this->getNewSize($width, $height, $info);
		//打开旧的图片资源
		$imageRes = self::openImage($image);
		//得到新图的资源
		$newRes = $this->kidOfImage($imageRes, $size, $info);
		//保存图片
		//得到新图片的名字
		$newName = $this->getNewName($image, $prefix);
		//得到你新路径
		$newPath = rtrim($this->path, '/').'/'.$newName;
		$this->savePath($newRes, $newPath);

		//销毁资源
		imagedestroy($imageRes);
		imagedestroy($newRes);

	}


	protected static function getImageInfo($jinzhiwePath)
	{
		$info = getimagesize($jinzhiwePath);
		//0表示宽, 1表示高
		//获取宽度
		$data['width'] = $info[0];
		$data['height'] = $info[1];
		$data['mime'] = $info['mime'];
		return $data;
	}

	protected function checkImage($imageInfo, $waterInfo)
	{
		if ($imageInfo['width'] < $waterInfo['width'] || $imageInfo['height'] < $waterInfo['height']) {
			return false;
		} else {
			return true;
		}
	}

	protected static function openImage($path)
	{
		$mime = self::getImageInfo($path)['mime'];
		switch ($mime) {
			case 'image/png':
				$image = imagecreatefrompng($path);
				break;
			case 'image/gif':
				$image = imagecreatefromgif($path);
				break;
			case 'image/jpeg':
				$image = imagecreatefromjpeg($path);
				break;
			case 'image/wbmp':
				$image = imagecreatefromwbmp($path);
				break;
		}
		return $image;
	}

	protected function getPosition($position, $imageInfo, $waterInfo)
	{
		$arr1 = [0, ($imageInfo['width'] - $waterInfo['width']) / 2, $imageInfo['width'] - $waterInfo['width']];
		$arr2 = [0, ($imageInfo['height'] - $waterInfo['height']) / 2, $imageInfo['height'] - $waterInfo['height']];

		if ($position) {
			//得到行号和列号
			$row = floor(($position - 1) / 3);
			$col = floor(($position - 1) % 3);
			$x = $arr1[$col];
			$y = $arr2[$row];
		} else {
			//这里传的位置是0
			$x = mt_rand(0, $imageInfo['width'] - $waterInfo['width']);
			$y = mt_rand(0, $imageInfo['height'] - $waterInfo['height']);
		}
		return ['x' => $x, 'y' => $y];
	}

	protected function getNewName($imagePath, $prefix)
	{
		if ($this->isRandName) {

			$name = $prefix.uniqid().'.'.$this->type;
		} else {
			$name = $prefix.pathinfo($imagePath)['basename'];

		}
		return $name;
	}

	protected function savePath($imageRes, $newPath)
	{
		$func = 'image'.$this->type;
		$func($imageRes, $newPath);
	}

	protected function getNewSize($width, $height, $imgInfo)
	{
		$size['old_w'] = $width;
		$size['old_h'] = $height;
		
		$scaleWidth = $width / $imgInfo['width'];
		$scaleHeight = $height / $imgInfo['height'];
		$scaleFinal = min($scaleWidth, $scaleHeight);

		$size['new_w'] = round($imgInfo['width'] * $scaleFinal);
		$size['new_h'] = round($imgInfo['height'] * $scaleFinal);
		if ($scaleWidth < $scaleHeight) {
			$size['x'] = 0;
			$size['y'] = round(abs($size['new_h'] - $height) / 2);
		} else {
			$size['y'] = 0;
			$size['x'] = round(abs($size['new_w'] - $width) / 2);
		}
		return $size;
	}

	protected function kidOfImage($srcImg, $size, $imgInfo)
	{
		//传入新的尺寸，创建一个指定尺寸的图片
		$newImg = imagecreatetruecolor($size['old_w'], $size['old_h']);		
		//定义透明色
		$otsc = imagecolortransparent($srcImg);
		if ($otsc >= 0) {
			//取得透明色
			$transparentcolor = imagecolorsforindex($srcImg, $otsc);
			//创建透明色
			$newtransparentcolor = imagecolorallocate(
				$newImg,
				$transparentcolor['red'],
				$transparentcolor['green'],
				$transparentcolor['blue']
			);
		} else {
			//将黑色作为透明色，因为创建图像后在第一次分配颜色时背景默认为黑色
			$newtransparentcolor = imagecolorallocate($newImg, 0, 0, 0);
		}
		//背景填充透明
		imagefill( $newImg, 0, 0, $newtransparentcolor);		 
		imagecolortransparent($newImg, $newtransparentcolor);

		imagecopyresampled( $newImg, $srcImg, $size['x'], $size['y'], 0, 0, $size["new_w"], $size["new_h"], $imgInfo["width"], $imgInfo["height"] );
		return $newImg;
	}


}

