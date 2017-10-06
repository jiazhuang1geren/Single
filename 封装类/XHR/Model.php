<?php
namespace framework;
class Model
{
	//主机名
	protected $host;
	//用户名
	protected $user;
	//密码
	protected $pwd;
	//字符集
	protected $charset;
	//数据库名
	protected $dbName;

	//端口
	protected $port;
	//数据库资源对象
	protected $link;
	//表的前缀
	protected $prefix;
	//数据库表名
	protected $table;

	//定义缓存字段数组
	protected $fields = [];

	//定义查询条件的数组
	protected $options = [];

	//存放sql语句
	protected $sql;

	public function __construct($config = [])
	{
		if (empty($config)) {
			$config = $GLOBALS['config'];
		}
		$this->host = $config['DB_HOST'];
		$this->user = $config['DB_USER'];
		$this->pwd = $config['DB_PWD'];
		$this->port = $config['DB_PORT'];
		$this->charset = $config['DB_CHARSET'];
		$this->dbName = $config['DB_NAME'];
		$this->prefix = $config['DB_PREFIX'];
		//连接数据库
		$this->link = $this->connect();
		//获取表名
		$this->table = $this->getTableName();
		//获取缓存字段
		$this->fields = $this->getCacheField();
		//初始化查询条件
		$this->initOptions();


		
	}

	protected function connect()
	{
		//1.连接数据库
		$link = mysqli_connect($this->host, $this->user, $this->pwd);
		//2.判断数据库是否连接成功
		if (!$link) {
			exit('<font color="green">'.mysqli_connect_error().'</font>');
		}
		//3.选择数据库名
		$db = mysqli_select_db($link, $this->dbName);
		if (!$db) {
			exit('<h1 style="color:blue;">错误信息为:'.mysqli_error($link).'<br />错误号为:'.mysqli_errno($link).'</h1>');
		}	
		//4.设置字符集
		mysqli_set_charset($link, $this->charset);
		return $link;
	}

	//获取表名
	protected function getTableName()
	{
		//model\UserModel model\GoodsModel
		$className = get_class($this);
		$pos = strrpos($className, '\\');
		$className = substr($className, $pos+1);//UserModel
		
		//user goods
		$table = strtolower(substr($className, 0, -5));
		return $this->prefix.$table;
	}

	protected function getCacheField()
	{

		//拼接缓存文件全路径
		//cache/user.php
		$cacheFile = './cache/'.$this->table.'.php';
		if (file_exists($cacheFile)) {
			return include $cacheFile;
		}

		//得到缓存字段并且把它存到缓存文件中
		//拼接sql语句
		$sql = 'desc '.$this->table;

		
		$newData = $this->query($sql);
		if ($newData) {
			foreach ($newData as $key => $value) {
				
				//获取键为Field的值
				$fields[] = $value['Field'];
				//获取主键PRI 的值是id
				if ($value['Key'] == 'PRI') {
					$fields['PRI'] = $value['Field'];
				}

			}
			//先将数组转换 成  字符串数组
			$str = var_export($fields, true);
			$str = "<?php \n\n return $str;";
			file_put_contents($cacheFile, $str);
			return $fields;
		}
		return false;
		
	}

	//执行查询sql
	protected function query($sql)
	{
		$this->initOptions();
		
		$result = mysqli_query($this->link, $sql);
		
		if ($result && mysqli_affected_rows($this->link) > 0) {
			while ($row = mysqli_fetch_assoc($result)) {
				$newData[] = $row;
			}
			return $newData;
		}
		return false;
	}

	//初始化查询条件的数组
	protected function initOptions()
	{
		$arr = ['field', 'table', 'where', 'group', 'having', 'order', 'limit'];
		foreach ($arr as $key => $value) {

			$this->options[$value] = '';
			//除了field和table
			if ($value == 'field') {
				$this->options['field'] = join(',', array_unique($this->fields));
			} elseif ($value == 'table') {
				$this->options['table'] = $this->table;
			}

		}
		
	}



	//where
	public function where($where)
	{
		if (!empty($where)) {
			if (is_string($where)) {
				$this->options['where'] = ' WHERE '.$where;
			} elseif (is_array($where)) {
				$whereSql = '';
				foreach ($where as $key => $value) {
					$whereSql .= $key.'='.$value.' AND ';
				}
				$whereSql = ' WHERE '.rtrim($whereSql, ' AND ');
				$this->options['where'] = $whereSql;

			}
		}
		return $this;
	}

	public function table($table)
	{	
		if (!empty($table)) {
			$this->options['table'] = $this->prefix.$table;
		}
		return $this;
	}

	public function field($field)
	{
		if (!empty($field)) {
			if (is_string($field)) {
				$this->options['field'] = $field;
			} elseif (is_array($field)) {
				$this->options['field'] = join(',', $field);
			}
		}
		return $this;
	}

	public function limit($limit)
	{
		if (!empty($limit)) {
			if (is_string($limit)) {
				$this->options['limit'] = ' LIMIT '.$limit;
			} elseif (is_array($limit)) {
				$this->options['limit'] = ' LIMIT '.join(',', $limit);
			}
		}
		return $this;
	}

	public function order($order)
	{
		if (!empty($order)) {
			$this->options['order'] = ' ORDER BY '.$order;
		}
		return $this;
	}

	public function group($group)
	{
		if (!empty($group)) {
			$this->options['group'] = ' GROUP BY '.$group;
		}
		return $this;
	}

	public function having($having)
	{
		if (!empty($having)) {
			$this->options['having'] = ' HAVING '.$having;
		}
		return $this;
	}

	public function select()
	{
		//拼接sql语句
		$sql = "SELECT %FIELD% FROM %TABLE% %WHERE% %GROUP% %HAVING% %ORDER% %LIMIT%";
		//替换
		$sql = str_replace(['%FIELD%', '%TABLE%', '%WHERE%', '%GROUP%', '%HAVING%', '%ORDER%', '%LIMIT%'],

		 [$this->options['field'], $this->options['table'], $this->options['where'], $this->options['group'], $this->options['having'], $this->options['order'], $this->options['limit']], $sql);
		$this->sql = $sql;
		
		return $this->query($sql);
	}

	public function insert($data)
	{	
		$data = $this->parseValue($data);
		
		//得到字段数组
		$keys = join(',',array_keys($data));
		//得到值得数组
		$values = join(',', array_values($data));
		$sql = "INSERT INTO %TABLE%(%FIELD%) VALUES(%VALUES%)";
		$sql = str_replace(['%TABLE%', '%FIELD%', '%VALUES%'], [$this->options['table'], $keys, $values], $sql);
		$this->sql = $sql;
		return $this->exec($sql);

	}

	protected function parseValue($data)
	{
		foreach ($data as $key => $value) {
			if (is_string($value)) {

				$value = '\''.$value.'\'';

			}
			$newData[$key] = $value;

		}
		return $newData;
	}

	//执行增删改的sql语句
	protected function exec($sql, $insertId = true)
	{
		
		$result = mysqli_query($this->link, $sql);
		if ($result && mysqli_affected_rows($this->link) > -1) {
			if ($insertId) {
				return mysqli_insert_id($this->link);
			} else {
				return mysqli_affected_rows($this->link);
			}
		}
		return false;
	}

	public function delete()
	{
		$sql = "DELETE FROM %TABLE% %WHERE%";
		$sql = str_replace(['%TABLE%', '%WHERE%'], [$this->options['table'], $this->options['where']], $sql);
		$this->sql = $sql;
		return $this->exec($sql, false);
	}

	//[name='goudan',sex=2]
	public function update($data)
	{
		$data = $this->parseValue($data);
		//name='goudan',sex='2'
		$value = $this->parseUpdate($data);

		$sql = "UPDATE %TABLE% SET %VALUE% %WHERE%";
		$sql = str_replace(['%TABLE%', '%VALUE%', '%WHERE%'], [$this->options['table'], $value, $this->options['where']], $sql);
		$this->sql = $sql;
		return $this->exec($sql, false);
	}

	protected function parseUpdate($data)
	{
		foreach ($data as $key => $value) {
			$arr[] = $key.'='.$value;
		}
		return join(',', $arr);
	}

	public function __destruct()
	{
		mysqli_close($this->link);
	}

	public function __call($method, $params)
	{
		if (substr($method, 0, 5) == 'getBy') {
			$field = strtolower(substr($method, 5));// id name  email

			return $this->where("$field='$params[0]'")->select();
			
		}
		return false;
	}

	public function __get($property)
	{
		if ($property == 'sql') {
			return $this->sql;
		} else {
			return false;
		}
	}

	//count sum max min

	public function count($field = null)
	{
		if (empty($field)) {
			$field = $this->fields['PRI'];
		} 
		$result = $this->field("count($field) as count")->select();
		return $result[0]['count'];
	}


	public function sum($field = null)
	{
		if (empty($field)) {
			$field = $this->fields['PRI'];
		} 
		$result = $this->field("sum($field) as sum")->select();
		return $result[0]['sum'];
	}

	public function max($field = null)
	{
		if (empty($field)) {
			$field = $this->fields['PRI'];
		} 
		$result = $this->field("max($field) as max")->select();
		return $result[0]['max'];
	}

	public function min($field = null)
	{
		if (empty($field)) {
			$field = $this->fields['PRI'];
		} 
		$result = $this->field("min($field) as min")->select();
		return $result[0]['min'];
	}

}