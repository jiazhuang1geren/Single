<?php
class Start
{
	public static $auto;
	public static function init()
	{
		self::$auto = new Psr4AutoLoad();
	}

	public static function router()
	{
		//通过url地址中传递过来的参数
		$c = isset($_GET['c']) ? $_GET['c'] : 'index';
		$a = isset($_GET['a']) ? $_GET['a'] : 'index';
		$_GET['c'] = $c;
		$_GET['a'] = $a;

		//拼接带有命名空间的全类名

		$controller = '\\controller\\'.ucfirst(strtolower($c)).'Controller';

		//实例化对象
		$obj = new $controller();
		call_user_func([$obj, $a]);
	}

}

Start::init();
