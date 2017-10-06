<?php
namespace framework;
/**
1.通过对象调用方法code,拿到验证码
2.要把常用的变量封装到属性里边,属性要使用protected
3.把不对外公开的方法设置为你protected
*/
class Code
{
	protected $width;
	protected $height;
	protected $type;
	protected $num;
	//保存图像资源
	protected $image;
	//验证码
	protected $code;
	//得到图片的类型
	protected $imageType;

	public function __construct($width = 100, $height = 50, $type = 1, $num = 4, $imageType = 'png')
	{
		$this->width = $width;
		$this->height = $height;
		$this->type = $type;
		$this->num = $num;
		$this->imageType = $imageType;
		$this->code = $this->getCode();
		
	}

	/**
	 * 获取验证码的
	 */
	public function code()
	{
		//创建画布
		$this->image = $this->createImage();
		//给画布填充颜色
		$this->fillColor();
		//把验证画到画布上,首先要有验证码
		$this->drawCode();
		//对验证设置干扰项
		$this->drawDistrub();
		//显示到浏览器
		$this->show();
	}

	//专门创建画布图像资源方法
	protected function createImage()
	{
		return imagecreatetruecolor($this->width, $this->height);
	}

	//给画布填充颜色
	protected function fillColor()
	{
		return imagefill($this->image, 0, 0, $this->light_color());
	}

	//创建浅色系
	protected function light_color()
	{
		return imagecolorallocate($this->image, mt_rand(130, 255), mt_rand(130, 255), mt_rand(130, 255));
	}

	//创建深色系
	protected function dark_color()
	{
		return imagecolorallocate($this->image, mt_rand(0, 120), mt_rand(0, 120), mt_rand(0, 120));
	}

	//获取验证码

	protected function getCode()
	{
		switch ($this->type) {
			case 0:
				$code = $this->getNumberCode();
				break;
			case 1:
				$code = $this->getCharCode();
				break;
			case 2:
				$code = $this->getNumberCharCode();
				break;
			default:
				exit('该类型不支持');
		}
		return $code;
	}

	//获取数字验证码
	protected function getNumberCode()
	{
		$str = '0123456789';
		return substr(str_shuffle($str), 0, $this->num);
	}

	protected function getCharCode()
	{	
		$arr1 = range('a', 'z');
		$arr2 = range('A', 'Z');
		$arr = array_merge($arr1, $arr2);
		shuffle($arr);
		$result = array_slice($arr, 0, $this->num);
		return join('', $result);

	}

	protected function getNumberCharCode()
	{
		$str = '';
		for ($i = 0; $i < $this->num; $i++) { 
			$rand = mt_rand(0,2);
			switch ($rand) {
				case 0:
					$str .= chr(mt_rand(48, 57));
					break;
				case 1:
					$str .= chr(mt_rand(97, 122));
					break;
				case 2:
					$str .= chr(mt_rand(65, 90));
					break;
			}
		}
		return $str;
	}

	protected function drawCode()
	{
		$width = ceil($this->width / $this->num);
		for ($i = 0; $i < $this->num; $i++) { 
			$code = $this->code[$i];
			$x = mt_rand($i * $width, ($i + 1)* $width-10);
			$y = mt_rand(0, $this->height-15);
			imagechar($this->image, 5, $x, $y, $code, $this->dark_color());
		}
	}

	//设置干扰项
	protected function drawDistrub()
	{
		for ($i = 0; $i < 100; $i++) { 
			imagesetpixel($this->image, mt_rand(0, $this->width), mt_rand(0, $this->height), $this->dark_color());
		}
		for ($i = 0; $i < 5; $i++) { 
			imagearc($this->image, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(30, 100), mt_rand(180, 360), $this->dark_color());
		}
	}

	//显示到浏览器
	protected function show()
	{
		header('Content-type: image/'.$this->imageType);
		$func = 'image'.$this->imageType;//imagepng()
		$func($this->image);
	}

	public function __destruct()
	{
		if (!empty($this->image)) {
			imagedestroy($this->image);
		}
		
	}

	public function __get($property)
	{
		if ($property == 'code') {
			return $this->code;
		} else {
			return false;
		}
	}
}
