<?php
/**
 * 使用Sqlite3作为对应的数据库存储
 */
// 以项目根目录起始计算数据库文件
$db_file_path = 'data/kotori.db';
$image_table = 'imagedata';
$user_table = 'userdata';
$statistics_table = 'statistics';

$db = new SQLite3('../' . $db_file_path);

if (!$db) {
	print($db ->lastErrorMsg() . '<br />');
	exit();
} else {
	print(sprintf('open %s successfullly!', $db_file_path) . '<br />');
}

// 增加imagedata表
$sql = "CREATE TABLE " . $image_table . "
		(id 		INTEGER PRIMARY KEY AUTOINCREMENT,
		size		INTEGER			NOT NULL,
		uploader	INTEGER			NOT NULL,
		filename	CHAR(32)		NOT NULL,
		filetype	CHAR(8)			NOT NULL,
		upload_time DATETIME		NOT NULL,
		expired		INT DEFAULT 0	NOT NULL,
		nsfw		INT	DEFAULT 0	NOT NULL)";
		
if ($db ->exec($sql)) {
	print(sprintf('create table %s successfullly!', $image_table). '<br />');
} else {
	print($db ->lastErrorMsg(). '<br />');
	exit();
}

// 增加userdata表
$sql = 'CREATE TABLE ' . $user_table . '
		(id 		INTEGER PRIMARY KEY AUTOINCREMENT,
		username	CHAR(20)		NOT NULL,
		password	CHAR(40)		NOT NULL,
		ip			CHAR(40)		NOT NULL,
		anonymous	INT	DEFAULT 1	NOT NULL)';
		
if ($db ->exec($sql)) {
	print(sprintf('create table %s successfullly!', $user_table). '<br />');
} else {
	print($db ->lastErrorMsg(). '<br />');
	exit();
}

// 增加static表
$sql = 'CREATE TABLE ' . $statistics_table . '
	(id			INTEGER PRIMARY KEY AUTOINCREMENT,
	name		VARCHAR(40)		NOT NULL,
	value		INTEGER			NOT NULL)';

if (!$db ->exec($sql)) {
	print($db ->lastErrorMsg(). '<br />');
	exit();
} else {
	print(sprintf('create table %s successfullly!', $statistics_table). '<br />');
}

// 写入static表
$sql = "INSERT INTO $statistics_table (name, value) VALUES ('image', 0)";
if (!$db ->exec($sql)) {
	print($db ->lastErrorMsg(). '<br />');
	exit();
} else {
	print(sprintf('insert table %s successfullly!', $statistics_table). '<br />');
}

$sql = "INSERT INTO $statistics_table (name, value) VALUES ('user', 0)";
if (!$db ->exec($sql)) {
	print($db ->lastErrorMsg(). '<br />');
	exit();
} else {
	print(sprintf('insert table %s successfullly!', $statistics_table). '<br />');
}

?>