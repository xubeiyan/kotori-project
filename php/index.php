<?php
/**
* 入口文件
*/

require 'lib/Util.php';
// 检查配置文件状态
if(!file_exists('config/conf.php')) {
	//die('miss configuation file -> config/conf.php');
	Util::err('missConfigurationFile');
}

// 导入配置文件，配置变量$config
require 'config/conf.php';
global $config;

// 调用Util类，获取一些基本信息
$clientInfo = Util::getClientInfo();

// 调用User类，建立SESSION
require 'lib/User.php';
// 使用SESSION
session_start();
User::sessionCheck($config['user']['userDataFile'], $clientInfo['remoteAddr']);

// 调用Image类
require 'lib/Image.php';

if ($config['site']['rewriteURI'] == true) {
	
}
// 路由GET部分
if ($clientInfo['requestMethod'] == 'GET') {
	// 直接访问根目录
	if ($clientInfo['query'] == '') {
		header('refresh:0;url=?upload');
		exit();
	// 上传文件	
	} else if ($clientInfo['query'] == 'upload') {
		//var_dump($_SESSION['currentUser']);
		Util::template('uploadFile.html');
	// 随机访问图片
	} else if ($clientInfo['query'] == 'random') {
		$imageArray = Image::randomImage($config['file']['imageDataFile']);
		
		$templateArray = Image::generateRandomTemplate($imageArray); 
		// Image::generateHeader($imageArray['filename']);
		Util::template('random.html', $templateArray);
	// 列出图片
	} else if ($clientInfo['query'] == 'list') {
		
	// 注册
	} else if ($clientInfo['query'] == 'register') {
		
	// 登录	
	} else if ($clientInfo['query'] == 'login') {
		
	// 用户信息	
	} else if ($clientInfo['query'] == 'userinfo') {
		
	} else {
		Util::err('notAllowedReqQuery');
	}
// 路由POST部分
} else if ($clientInfo['requestMethod'] == 'POST') {
	// 上传图片
	if ($clientInfo['query'] == 'uploadpost') {
		//print_r($_FILES['img']);
		//print '<img src=' . $_POST['img'] . '>';
		if (isset($_FILES['img'])) {
			$imageDataFile = $config['file']['imageDataFile'];
			$result = Image::uploadFile($_FILES['img'], $imageDataFile);
			header('Content-type:application/json');
			print $result;
			exit();
		} else {
			Util::err('uploadFileFailed');
		}
	} else if ($clientInfo['query'] == 'registerpost') {
		
	} else if ($clientInfo['query'] == 'loginpost') {
		
	} else if ($clientInfo['query'] == 'userinfopost') {
		
	} else {
		Util::err('notAllowedReqQuery');
	}
// 其他不是知道什么的部分
} else {
	Util::err('notAllowedReqMethod');
}
	


?>