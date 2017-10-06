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
interface CreatePerson
{
	public function crate();
}
class MaleFactory implements CreatePerson
{
	public  function create()
	{
		return new Male();
	}
}
class FeMaleFactory implements CreatePerson
{
	public function create()
	{
		return new Female();
	}
}

$male = new MaleFactory();
$man = $male->create();
$man->marry();
$female = new FeMaleFactory();
$woman = $female->create();
$woman->marry();
