<?php
interface Person
{
    public function marry();
}
class Male implements Person
{
	public function marry()
	{
		echo '男方家:一动一不动,万紫千红一片绿 <br />';
	}
}
class Female implements Person
{
	public function marry()
	{
		echo '女方:只做一件事数钱 <br />';
	}
}
class SimpleFactory
{
	public static function createMale()
	{
		return new Male();
	}
	public static function createFemale()
	{
		return new Female();
	}
}
//之前能做的
// $male = new Male();
// $Female = new Female();
// $male->marry();
// $Female->marry();

//$si = new SimpleFactory();
$male = SimpleFactory::createMale();
$Female = SimpleFactory::createFemale();
$male->marry();
$Female->marry();