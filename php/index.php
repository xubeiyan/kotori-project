<?php
/**
* 入口文件
*/

// 调用Util类，获取一些基本信息
require 'lib/Util.php';
// 检查配置文件状态
if(!file_exists('config/conf.php')) {
	//die('miss configuation file -> config/conf.php');
	Util::err('missConfigurationFile');
}

// 导入配置文件，配置变量$config
require 'config/conf.php';
global $config;

$clientInfo = Util::getClientInfo();

// 路由GET部分
if ($clientInfo['requestMethod'] == 'GET') {
	
} else if ($clientInfo['requestMethod'] == 'POST') {
	
} else {
	Util::err('notAllowedReqMethod');
}
	
require 'lib/User.php';

?>