<?php
//声明一个抽象的电话
interface Tell
{
	public function call();
	public function receive();
}

class XiaoMi implements Tell
{
	public function call()
	{
		echo '小于使用小米手机给雷军打电话<br />';
	}
	public function receive()
	{
		echo '小于使用小米手机接她女朋友的电话<br />';
	}
}

class HuaWei implements Tell
{
	public function call()
	{
		echo '今天伟东买了一个华为手机,给她家小三的<br />';
	}
	public function receive()
	{
		echo '伟东用华为接志文的电话,因为他家小三跑了<br />';
	}
} 

interface FuShiKang
{
	public static function createPhone();
}

class XiaoMiFactory implements FuShiKang
{
	public static function createPhone()
	{
		return new XiaoMi();
	}
}

class HuaWeiFactory implements FuShiKang
{
	public static function createPhone()
	{
		return new HuaWei();
	}
}

$xiaomi = XiaoMiFactory::createPhone();
$huawei = HuaWeiFactory::createPhone();
$xiaomi->call();
$huawei->receive();


