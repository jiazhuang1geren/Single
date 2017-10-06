<?php
//被观察者
interface BeiGuanChaZhe
{
	public function ZengJia($gcz);
	public function GanDiao($gcz);
	public function TongZhi();
}
class HJDG implements BeiGuanChaZhe
{
	private $_guanchazhe = [];
	public function ZengJia($gcz)
	{
		$this->_guanchazhe[] = $gcz;

	}
	public function GanDiao($gcz)
	{
		foreach ($this->_guanchazhe as $key => $value) {
			if ($gcz == $value) {
				unset($this->_guanchazhe[$key]);
			}
		}
	}
	public function TongZhi()
	{
		echo '千军万马来相见，我要洗脚<br />';

		foreach ($this->_guanchazhe as $key => $value) {
			$value->NieJiao();
		}
	}
}
interface GuanChaZhe
{
	public function NieJiao();
}
class LH implements GuanChaZhe
{
	public function NieJiao()
	{
		echo '刘航说:大哥多久没有洗脚了 <br />';
	}
}
class ZP implements GuanChaZhe
{
	public function NieJiao()
	{
		echo '张鹏说:这个气味有点辣眼睛 <br />';
	}
}
class LZA implements GuanChaZhe
{
	public function NieJiao()
	{
		echo '刘泽安说:大哥你舒服么 <br />';
	}
}
//被观察者
$HJDG = new HJDG();
//观察者
$LH = new LH();
$ZP = new ZP;
$LZA = new LZA;
//增加观察者列表
$HJDG->ZengJia($LH);
$HJDG->ZengJia($ZP);
$HJDG->ZengJia($LZA);
$HJDG->TongZhi();
echo '------------<hr />';
$HJDG->GanDiao($LZA);
$HJDG->TongZhi();

