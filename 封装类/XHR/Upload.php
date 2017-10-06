<?php
namespace framework;
class Upload
{
	//定义传的路径
	protected $path = './upload/';
	//定义文件允许的mime类型
	protected $arr_mime = ['image/png', 'image/gif', 'image/jpeg', 'image/wbmp'];
	//定义文件允许的后缀
	protected $arr_suffix = ['png', 'gif', 'jpeg', 'jpg', 'pjpeg', 'wbmp'];
	//定义文件的最大尺寸
	protected $maxSize = 2000000;
	//是否启用随机文件名
	protected $isRandName = true;
	//是否启用日期目录
	protected $isDatePath = true;
	//定义文件前缀
	protected $prefix = 'xhr_';

	//上传文件的名字
	protected $name;
	//上传文件后缀
	protected $suffix;
	//上传文件大小
	protected $size;
	//上传文件的临时文件路径
	protected $tmpName;
	//上传文件mime类型率
	protected $mime;

	//定义错误号
	protected $errorNumber;
	//得到新文件的名字
	protected $newName;
	//得到新文件的路径
	protected $newPath;

	public function __construct($arr = [])
	{
		foreach ($arr as $key => $value) {
			$this->setValue($key, $value);
		}
	}

	protected function setValue($key, $value)
	{
		$keys = array_keys(get_class_vars(__CLASS__));
		if (in_array($key, $keys)) {
			return $this->$key = $value;
		}
	}


	public function upload($key)
	{
		//判断上传文件路径是否存在--文件不为空
		if (empty($this->path)) {
			$this->setValue('errorNumber', -1);
			return false;
		}

		//检查不是文件夹,或者不是目录
		if (!$this->checkDir()) {
			$this->setValue('errorNumber', -2);
			return false;
		}

		//取出上传信息,找到error对应的值为0
		$error = $_FILES[$key]['error'];
		if (!$error) {
			//上传文件成成,把心里边的值存到相应的属性当中
			$this->setFileInfo($key);
		} else {
			$this->setValue('errorNumber', $error);
			return false;
		}


		//判断文件大小,后缀和mime
		if (!$this->checkSize() || !$this->checkSuffix() || !$this->checkMime()) {
			//$this->setValue('errorNumber', -3);
			return false;
		}

		//创建新文件名
		$this->newName = $this->createNewName();
		//创建新的路径
		$this->newPath = $this->createNewPath();

		//判断是否是上传路径----是开始移动文件
		if (is_uploaded_file($this->tmpName)) {
			
			if (move_uploaded_file($this->tmpName, $this->newPath.'/'.$this->newName)) {
				$this->setValue('errorNumber', 0);
				return $this->newPath;
			} else {
				$this->setValue('errorNumber',-7);
				return false;
			}
		} else {
			$this->setValue('errorNumber', -8);
			return false;
		}

	}

	protected function checkDir()
	{
		if (!file_exists($this->path) || !is_dir($this->path)) {
			//不存在自己创建目录
			//第三个参数TRUE表示可以创建中间目录
			return mkdir($this->path, 0755, true);
		}

		//判断文件是否可写
		if (!is_writeable($this->path)) {
			return chmod($this->path, 0755);
		}
		return true;
	}

	protected function setFileInfo($key)
	{
		//获取文件名
		$this->name = $_FILES[$key]['name'];
		//获取mime类型
		$this->mime = $_FILES[$key]['type'];
		//获取临时文件路径
		$this->tmpName = $_FILES[$key]['tmp_name'];
		//获取文件的大小
		$this->size = $_FILES[$key]['size'];

		//获取文件后缀
		$this->suffix = pathinfo($this->name)['extension'];
	}

	protected function checkSize()
	{
		if ($this->size > $this->maxSize) {
			$this->setValue('errorNumber', -4);
			return false;
		}
		return true;
	}

	protected function checkMime()
	{
		if (!in_array($this->mime, $this->arr_mime)) {
			$this->setValue('errorNumber', -5);
			return false;
		}
		return true;
	}

	protected function checkSuffix()
	{
		if (!in_array($this->suffix, $this->arr_suffix)) {
			$this->setValue('errorNumber', -6);
			return false;
		}
		return true;
	}

	protected function createNewName()
	{
		if ($this->isRandName) {
			return $this->prefix.uniqid().'.'.$this->suffix;
		} else {
			return $this->prefix.$this->name;
		}
	}

	protected function createNewPath()
	{
		if ($this->isDatePath) {
			$path = $this->path.date('y/m/d');
			if (!file_exists($path)) {
				mkdir($path, 0755, true);
			} else {
				return $path;
			}
		} else {
			return rtrim($this->path, '/').'/';
		}
	}

	public function __get($property)
	{
		if ($property == 'errorNumber') {
			return $this->errorNumber;
		} elseif ($property == 'errorInfo') {
			return $this->setErrorMessage();
		}
	}

	protected function setErrorMessage()
	{
		$msg = '';
		switch ($this->errorNumber) {
			case 1:
				$msg = '超出php.ini配置文件项';
				break;
			case 2:
				$msg = '超出html配置文件项';
				break;
			case 3:
				$msg = '只有部分文件被上传';
				break;
			case 4:
				$msg = '没有文件上传';
				break;
			case 6:
				$msg = '找不到临时文件夹';
				break;
			case 7:
				$msg = '写入文件失败';
				break;
			case -1:
				//-1  上传路径不存在
				$msg = '上传路径不存在';
				break;
			case -2:
				//-2  上传文件错误
				$msg = '上传文件错误';
				break;
			case -4:
				//-4  上传文件大小错误
				$msg = '上传文件大小错误';
				break;
			case -5:
				//-5  上传文件mime类型错误
				$msg = '上传文件mime类型错误';
				break;
			case -6:
				//-6  上传文件后缀错误
				$msg = '上传文件后缀错误';
				break;
			case -7:
				//-6  移动文件失败
				$msg = '移动文件失败';
				break;
			case -8:
				//-6  不是上传文件
				$msg = '不是上传文件';
				break;
		}
		return $msg;
	}


}

