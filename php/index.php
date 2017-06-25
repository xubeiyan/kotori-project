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

//print_r($clientInfo);

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
		Util::template('uploadFile.html');
	// 随机访问图片
	} else if ($clientInfo['query'] == 'random') {
	
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
	if ($client['query'] == 'uploadpost') {
		
	} else if ($client['query'] == 'registerpost') {
		
	} else if ($client['query'] == 'loginpost') {
		
	} else if ($client['query'] == 'userinfopost') {
		
	} else {
		Util::err('notAllowedReqQuery');
	}
// 其他不是知道什么的部分
} else {
	Util::err('notAllowedReqMethod');
}
	
require 'lib/User.php';

?>