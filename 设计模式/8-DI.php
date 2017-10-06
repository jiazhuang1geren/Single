<?php
// class User extends  Controller
// {
// 	public function post(ArticleModel $article)
// 	{
// 		$article->add();
// 		// $article = new ArticleModel();
// 		// $article->add();
// 	}

// 	public function del(ArticleModel $article)
// 	{
// 		$article->delete();
// 	}
// }


//依赖注入
class Son
{
	public function cry(Father $father)
	{
		echo '哇哇哇...<br />';
		$father->hit();
	}
}

class Father
{
	public function hit()
	{
		echo '一脚踹飞你,赶过去接着踹,直到不哭为止<br />';
	}
}
$liwenkai = new Father();
$jiayi = new Son();
$jiayi->cry($liwenkai);