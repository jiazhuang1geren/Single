<?php
namespace framework;
class Page
{
	//总记录数
	protected $total;
	//每页显示的条数
	protected $perPage;
	//总页数
	protected $pages;
	//当前页码数
	protected $curPage;
	protected $url;

	public function __construct($total = 60, $perPage = 5)
	{
		$this->total = $total;
		$this->perPage = $perPage;
		$this->pages = ceil($total / $perPage);
		$this->curPage = isset($_GET['p']) ? min(max((int)$_GET['p'], 1), $this->pages) : 1;
		$this->url = $this->getUrl();
		

	}

	protected function getUrl()
	{
		//协议
		$scheme = $_SERVER['REQUEST_SCHEME'];
		//主机名
		$host = $_SERVER['SERVER_NAME'];
		//端口
		$port = $_SERVER['SERVER_PORT'];
		//获取文件路径和一堆参数
		$pathData = $_SERVER['REQUEST_URI'];
		$data = parse_url($pathData);
		//得到路径
		$path = $data['path'];
		
		//处理参数,干掉带有p参数的内容
		if (!empty($data['query'])) {
			//把参数变成数组
			parse_str($data['query'], $arr);
			
			unset($arr['p']);
			$query = http_build_query($arr);
			//拿到带有参数的路径
			$path = $path.'?'.$query;
		} else {
			//不带参数的路径
			$path = rtrim($data['path'], '?');
		}
		
		$url = $scheme.'://'.$host.':'.$port.$path;

		return $url;
	}

	public function page()
	{
		return [
			'firstUrl' => $this->firstUrl(),
			'prevUrl' => $this->prevUrl(),
			'nextUrl' => $this->nextUrl(),
			'endUrl' => $this->endUrl(),
		];
	}
 
 	protected function firstUrl()
 	{
 		return $this->setUrl('p=1');
 	}

 	protected function prevUrl()
 	{
 		if (($this->curPage -1) < 1) {
 			return $this->setUrl('p=1');
 		} else {
 			return $this->setUrl('p='.($this->curPage - 1));
 		}
 	}

 	protected function nextUrl()
 	{
 		if (($this->curPage + 1) > $this->pages) {
 			return $this->setUrl('p='.$this->pages);
 		} else {
 			return $this->setUrl('p='.($this->curPage + 1));
 		}
 	}

 	protected function endUrl()
 	{
 		
 		return $this->setUrl('p='.$this->pages);
 		
 	}

	protected function setUrl($params)
	{
		if (strstr($this->url, '?')) {
			return $this->url.'&'.$params;
		} else {
			return $this->url.'?'.$params;
		}
	}

	public function limit()
	{
		$offset = ($this->curPage - 1) * $this->perPage;
		return $offset.','.$this->perPage;
	}

}

