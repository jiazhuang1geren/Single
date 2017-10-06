<?php
class Dog
{
	private static $instance;
	private function __construct()
	{

	}
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	private function __clone()
	{

	}

}
// $dog1 = new Dog();
// $dog2 = new Dog();
$dog1 = Dog::getInstance();
$dog2 = Dog::getInstance();
// $dog3 = clone $dog2;
//var_dump($dog1, $dog2);
if ($dog1 === $dog2) {
	echo '是同一条狗<br />';
} else {
	echo '不是同一条狗<br />';
}