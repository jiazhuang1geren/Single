<?php
namespace framework;
class Tpl
{
	//定义模板文件路径
	protected $view = './view/';
	//定义缓存文件路径
	protected $cache = './cache/';
	//定义缓存文件的过期时间
	protected $cacheLifeTime = 3600;
	protected $vars = [];

	public function __construct($view = null, $cache = null, $cacheLifeTime = null)
	{
		if (!empty($view)) {
			if ($this->checkDir($view)) {
				$this->view = $view;
			}
		}

		if (!empty($cache)) {
			if ($this->checkDir($cache)) {
				$this->cache = $cache; 
			}
		}
 
 		if (!empty($cacheLifeTime)) {
 			$this->cacheLifeTime = $cacheLifeTime;
 		}
		
	}

	/**
	 * $viewPath:  传递发过来的模板文件路径
	 * $isInclude: 你传递过来的模板文件,是否需要编译并且include过来:  TRUE,或者是需要编译一下而已: false
	 * $uri: [index.php?p=3]用来区分你是第几页,将uri和传递过来的文件进行MD5加密,生成新的唯一的一个文件

	 */
	public function display($viewName, $isInclude = true, $uri = null)
	{
		

		//拼接全模板文件路径   ./view/rain.html
		$viewPath = rtrim($this->view, '/').'/'.$viewName;

		if (!file_exists($viewPath)) {
			die('你在逗我啊,传过来的模板文件不在呀');
		}

		//将缓存文件新的名字
		$cacheName = md5($viewName.$uri).'.php';
		//拼接缓存文件全路径
		$cachePath = rtrim($this->cache, '/').'/'.$cacheName;
		if (!file_exists($cachePath)) {
			//判断缓存文件不存在,需要编译,生成缓存文件
			//得到的结构是编译后的一堆字符串
			$php = $this->compile($viewPath);
			file_put_contents($cachePath, $php);

		}
		//缓存文件存在.
		//判断缓存文件的过期时间,如果过期重新生成
		//判断如果缓存文件在有效期内,文件内容被修改,也要重新生成缓存文件
		
			$isOutTime = (filectime($cachePath) + $this->cacheLifeTime) < time() ? true : false;
			
			$isChange = filemtime($viewPath) > filemtime($cachePath) ? true : false;
			
			if ($isOutTime || $isChange || !file_exists($cachePath)) {
				$php = $this->compile($viewPath);
				file_put_contents($cachePath, $php);
			} else {
				//检测include的文件是否发生变化
				$this->updateInclude($viewPath);
			}	
		
			
		

		//是否包含include文件
		if ($isInclude) {
			//如果有数据需要处理数据
			extract($this->vars);
			include $cachePath;
		}

	}

	protected function updateInclude($viewPath)
	{
		$content = file_get_contents($viewPath);
		$pattern = '/\{include (.+)\}/U';
		preg_match_all($pattern, $content, $matches);
		foreach ($matches[1] as $key => $value) {
			$value = trim($value,'\'"');
			$this->display($value,false);
		}
	}

	protected function checkDir($filePath)
	{
		//判断文件路径存在不存在或者是否是目录
		if (!file_exists($filePath) || !is_dir($filePath)) {
			mkdir($filePath, 0777, true);
		}

		//判断文件是否可读或者可写
		if (!is_writable($filePath) || !is_readable($filePath)) {
			chmod($filePath, 0777);
		}
		return true; 
	}
 
	protected function compile($viewPath)
	{
		//获取模板文件的内容信息html
		$html = file_get_contents($viewPath);
		
		//写一个模板替换的正则
		$arr = [
			'{$%%}' => '<?=$\1; ?>',
			'{if %%}' => '<?php if (\1): ?>',
			'{else}' => '<?php else: ?>',
			'{elseif %%}' => '<?php elseif (\1): ?>',
			'{else if %%}' => '<?php elseif(\1) : ?>',
			'{/if}' => '<?php endif; ?>',
			'{foreach %%}' => '<?php foreach (\1): ?>',
			'{/foreach}' => '<?php endforeach; ?>',
			'{include %%}' => '这是一个假的内容',
			'{for %%}' => '<?php for(\1):?>',
            '{/for}' => '<?php endfor;?>',
            '{while %%}' => '<?php while(\1):?>',
            '{/while}' => '<?php endwhile;?>',
            '{switch %%}' => '<?php switch \1: ?>',
			'{case %%}' => '<?php case \1: ?>',
            '{continue}' => '<?php continue;?>',
            '{break}' => '<?php break;?>',
            '{default}' => '<?php default; ?>',
			'{/switch}' => '<?php endswitch; ?>',
            '{$%% = $%%}' => '<?php $\1 = $\2;?>',
            '{$%%++}' => '<?php $\1++;?>',
            '{$%%--}' => '<?php $\1--;?>',
            '{comment}' => '<?php /* ',
            '{/comment}' => ' */ ?>',
            '{/*}' => '<?php /* ',
            '{*/}' => '* ?>',
            '{section}' => '<?php ',
            '{/section}' => '?>',
			'{{%%(%%)}}' => '<?=\1(\2);?>',
			'{wn}'		=> '<?php ',
			'{/wn}'		=> '?>',
			'__%%__' 	 => '<?=\1;?>',
		];

		//遍历数组,一次替换正则
		foreach ($arr as $key => $value) {
			//生成正则表达式
			$pattern = '/'.str_replace('%%', '(.+)', preg_quote($key, '/')).'/';


			//判断正则中有么有include
			if (strstr($pattern, 'include')) {
				$html = preg_replace_callback($pattern, [$this, 'parseInclude'], $html);
			} else {
				//替换
				$html = preg_replace($pattern, $value, $html);
			}
		}
		return $html;

	}

	public function assign($key, $value)
	{
		$this->vars[$key] = $value;

	}

	protected function parseInclude($data)
	{
		//处理单双引号
		$file = trim($data[1], '\'"');
		$this->display($file, false);
		//生成缓存文件名字
		$cacheName = md5($file).'.php';
		//拼接缓存文件全路径
		$cachePath = rtrim($this->cache, '/').'/'.$cacheName;
		return '<?php include "'.$cachePath.'"; ?>';

	}

	//清除缓存文件里的内容,说白了就是删除文件
	public function clearCache($dir = null)
	{
		if ($dir) {
			$res = opendir($dir);
			$path = $dir;
		} else {
			$res = opendir($this->cache);
			$path = $this->cache;
		}

		while (false !==($fileName = readdir($res))) {

			if ($fileName == '.' || $fileName == '..') {
				continue;
			}
			chmod($path, 0777);
			$filePath = rtrim($path, '/').'/'.$fileName;
			if (is_dir($filePath)) {
				$this->clearCache($filePath);
				if (count(scandir($filePath)) == 2) {
					rmdir($filePath);
				}
			} else {
				unlink($filePath);
			}
		}
		closedir($res);
	}

}