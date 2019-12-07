<?php
/**
* 用于清空其数据库（慎用）
*
*/
// 读取config/conf.php文件中文件位置，没有则直接检测data/userdata和data/imagedata存在与否
require '../config/conf.php';
// 引入一个简单的渲染引擎
require 'template.php';

define('PROD', 'production');
define('DEV', 'development');

if (isset($config['user']['userDataFile'])) {
	$user_data_file = '../' . $config['user']['userDataFile'];
} else {
	$user_data_file = '../data/userdata';
}

if (isset($config['file']['imageDataFile'])) {
	$image_data_file = '../' . $config['file']['imageDataFile'];
} else {
	$image_data_file = '../data/imagedata';
}

// 处理no的情况
if (isset($_GET['no'])) {
	$template = Array(
		'title' => '即将跳转',
		'header' => '跳转',
		'info' => '未作修改，将在{jump_time}秒后跳转至首页',
	);
	
	$param = Array(
		'jump' => Array(
			'time' => 5,
			'url' => '../index.php',
		),
	);
	echo Template::render('jump', $template, $param);
	exit();
}

// 处理yes的情况
if (isset($_GET['yes'])) {
	require '../lib/Util.php';
	Util::clearAllData();
	
	$template = Array(
		'title' => '提示信息',
		'header' => '已清除数据',
		'info' => '已清空所有图片和用户数据...',
	);
	
	echo Template::render('info', $template);
	exit();
}

if ($config['environment'] == DEV) {
	$template = Array(
		'title' => '提示信息...',
		'header' => '注意',
		'info' => '当前配置文件为生产环境，确定要清空所有图像和用户数据？',
	);
	
	echo Template::render('question', $template);
	exit();
}
?>