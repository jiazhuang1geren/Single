<?php
//适配器
//把不同的接口统一起来,最后对外提供一个简洁的接口
interface Person
{
	public function writePhp();
	public function cook();
}

class Wife
{
	public function cook()
	{
		echo '西红柿炒鸡蛋沫<br />';
	}
}

class Husbands implements Person
{
	public $wife;
	public function __construct($wife)
	{
		$this->wife = $wife;
	}
	public function writePhp()
	{
		echo '苦逼的我到凌晨4点了还在写小黄人布置的作业<br />';
	}


	public function cook()
	{
		$this->wife->cook();
	}
}

$xhr = new Wife();
$cwd = new Husbands($xhr);
$cwd->cook();
