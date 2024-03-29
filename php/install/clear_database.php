<?php
/**
* 用于清空其数据库（慎用）
*
*/
// 读取config/conf.php文件中文件位置，没有则直接检测data/userdata和data/imagedata存在与否
require '../config/conf.php';
// 引入一个简单的渲染引擎
require 'template.php';

$db_file_path = $config['database']['sqliteFile'];

$db = new SQLite3('../' . $db_file_path);

define('PROD', 'production');
define('DEV', 'development');

// 将提示信息置空
$hint_message = '';

if (!$db) {
	print($db ->lastErrorMsg() . '<br />');
	exit();
} else {
	$hint_message = sprintf('open %s successfullly!', $db_file_path);
}

// 处理no的情况
if (isset($_GET['no']) && $config['environment'] == DEV) {
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
if (isset($_GET['yes']) && $config['environment'] == DEV) {
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
		'info' => $hint_message . '<br>确定要清空所有图像和用户数据？',
	);
	
	echo Template::render('question', $template);
	exit();
}

if ($config['environment'] == PROD) {
	$template = Array(
		'title' => '提示信息...',
		'header' => '注意',
		'info' => '生产环境暂时无法直接清除所有数据',
	);
	
	echo Template::render('info', $template);
	exit();
}
?>