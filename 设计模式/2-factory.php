<?php
interface Car
{
	public function run();
	//载人的功能
	public function manned();
}

class BMW implements Car
{
	public function run()
	{
		echo '我开着宝马A4去吃胡辣汤<br />';
	}

	public function manned()
	{
		echo '宝马车上坐着10个人,一起去撸串<br />';
	}
}

class BinLi implements Car
{
	public function run()
	{
		echo '小磊的女儿开着宾利去约会,跟胖凯的儿子<br />';
	}

	public function manned()
	{
		echo '宾利车上宰了2个人,然后跑到河边掉进去了<br />';
	}
}

class AuDi implements Car
{
	public function run()
	{
		echo '小磊开着奥迪去追他女儿,然后打断了胖凯儿子的腿<br />';
	}

	public function manned()
	{
		echo '奥迪宰了一个人,去找胖凯<br />';
	}
}

//声明一个工厂类,专门来生产汽车对象
class Factory
{
	public static function getInstance($type)
	{
		switch ($type) {
			case 'BMW':
				return new BMW();
				break;
			case 'BinLi':
				return new BinLi();
				break;
			case 'AuDi':
				return new AuDi();
				break;
		}
	}
}


$binli = Factory::getInstance('BinLi');
$binli->run();
$binli->manned();