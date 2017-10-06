<?php
interface Person
{
    public function marry();
}
class HaveMoneyMale implements Person
{
	public function marry()
	{
		echo '男方家:一动一不动,万紫千红一片绿 <br />';
	}
}
class NoMoneyMale implements Person
{
	public function marry()
	{
		echo '还结婚,多喝热水 <br />';
	}
}
class HaveMoneyFemale implements Person
{
	public function marry()
	{
		echo '女方:只做一件事数钱 <br />';
	}
}
class NoMoneyFemale implements Person
{
	public function marry()
	{
		echo '抠脚大汉 <br />';
	}
}
interface CreatePerson
{
	public function HaveMoney($type);
	public function NoMoney($type);
}
class MoneyPerson implements CreatePerson
{
    public function HaveMoney($type)
    {
    	$className = 'HaveMoney' . $type;
    		return new $className();
    }
    public function NoMoney($type)
    {
    	$className = 'NoMoney' . $type;
    		return new $className;
    }
}
$Person = new MoneyPerson();
$male = $Person->HaveMoney('Male');
$Female = $Person->NoMoney('Female');
$male->marry();
$Female->marry();