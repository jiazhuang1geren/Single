<?php
class Psr4AutoLoad
{
	protected $maps = [];
	public function __construct()
	{
		//实例化对象的时候注册一个自动加载的方法
		spl_autoload_register([$this, 'autoload']);
	}

	protected function autoload($className)
	{
		//1.将完整的类名拆分成命名空间名和类名   controller\IndexController
		$pos = strrpos($className, '\\');
		//提取命名空间名
		$namespace = substr($className, 0, $pos);//controller
		//提取类名
		$realClass = substr($className, $pos+1);
		//2.去查找对应的映射关系
		$this->mapLoad($namespace, $realClass);

	}

	protected function mapLoad($namespace, $realClass)
	{
		if (array_key_exists($namespace, $this->maps)) {
			//获取真实的路径
			// 'controller' => 'app/controller'
			$namespace = $this->maps[$namespace];
		}

		//处理斜杠
		$namespace = rtrim(str_replace('\\/', '/', $namespace), '/').'/';
		//拼接真实的全路径
		$realPath = $namespace.$realClass.'.php';
		if (!$this->quire($realPath)) {
			die('文件路径不存在');
		}	

	}

	protected function quire($realPath)
	{
		if (file_exists($realPath)) {
			include $realPath;
			return true;
		} else {
			return false;
		}
	}


	public function addMaps($namespace, $path)
	{
		if (array_key_exists($namespace, $this->maps)) {
			die('此命名空间的映射关系已存在');
		}
		$this->maps[$namespace] = $path;
	}
}