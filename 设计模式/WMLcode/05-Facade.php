<?php
interface Stock
{
	public function buy();
	public function sell();
}
class XxxStock implements Stock
{
	public $name;
	public function __construct($name) 
	{
		$this->name = $name;
	}
	public function buy()
	{
		return '买入'. $this->name .'股票 <br />';
	}
	public function sell()
	{
		return '抛出' . $this->name . '股票 <br />';
	}
}
//门面、、基金公司
class Fund
{
	private $_wanda;
	private $_wanke;
	private $_shunfeng;

	private $_name;//基金公司的名字
	public function __construct($name)
	{
		$this->name = $name;
		$this->_wanda = new XxxStock('万达');
		$this->_wanke = new XxxStock('万科');
		$this->_shunfeng = new XxxStock('顺丰');
	}

	public function buy()
	{
		echo $this->name . '根据行情'. $this->_wanda->buy();
	}
	public function sell()
	{
		echo $this->name . '根据行情' . $this->_wanke->sell();
	}

}
$cel = new Fund('天弘基金');
$cel->buy();
$cel->sell();