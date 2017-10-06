<?php 
//适配的目标
interface DataBase
{
	public function connect($host, $user, $pwd, $dbname);
	public function query($sql);
}
class AdMysql implements DataBase
{
	private $link;
	public function connect($host, $user, $pwd, $dbname)
	{
		$this->link = mysql_connect($host, $user, $pwd);
		mysql_select_db($dbname, $this->link);
	}
	public function query($sql)
	{
		mysql_query($sql, $this->link);
	}

} 
class AdMysqli implements DataBase
{
	private $link;
	public function connect($host, $user, $pwd, $dbname)
	{
		$this->link = mysqli_connect($host, $user, $pwd);
		mysqli_select_db($dbname, $this->link);
	}
	public function query($sql)
	{
		mysqli_query($sql, $this->link);
	}

} 
$ve = PHP_VERSION;
if ($ve > 5.6)
{
	new AdMysqli;
} else {
	new  AdMysql;
}