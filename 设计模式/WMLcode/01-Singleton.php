<?php
/*
*$_instance  必须是私有静态属性
*构造方法一定是私有的，防止在外部实例化
*getInstanse一定是公有的
*魔术方法__clone变成私有
*因为new都会消耗内存
*场景:数据库链接  
*/
class Singleton
{
	private static $_instance = null;
	//阻止在外部实例化
	private function __construct()
	{
		echo '实例化对象 <br />';
	}
	static public function getInstanse()
	{
		if (is_null(self::$_instance)) {
			self::$_instance =  new self();
		}
		return self::$_instance;
	}
	//阻止克隆对象
	private function __clone()
	{

	}
}
//$obj1 = new Singleton;
//$obj2 = new Singleton;
$obj1 = Singleton::getInstanse();
$obj2 = Singleton::getInstanse();
$obj3 = clone $obj2;
if ($obj1 === $obj2) {
	echo '相同对象';
} else {
	echo '不同对象';
}