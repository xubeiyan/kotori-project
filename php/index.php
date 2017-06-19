<?php
/**
* 入口文件
*/

// 检查配置文件状态
if(!file_exists('config/conf.php')) {
	// TODO: 更改输出模式
	die('miss configuation file -> config/conf.php');
}

// 导入配置文件
require 'config/conf.php';
global $config;

// 浏览图片
//if (isset($_GET))
	
require 'lib/User.php';

$content = Array (
	'id' => '0',
	'username' => 'abc',
	'password' => sha1('sasadfasdfsa'),
	'ip' => '[::1]',
	'anonymous' => '0'
);

User::addData('data/userdata', $content);
?>