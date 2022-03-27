<?php
/**
* 配置文件
* 仅包含$config这一个变量
*/
$config = Array (
	'environment' 				=> 'development',	#运行级别 development/production 
	'user' => Array(
		'adminUserName' 		=> 'kotori',		#管理员用户名
		'adminPassword' 		=> 'kotori',		#管理员密码
		'allowAnonymousUpload' 	=> true, 			#是否允许匿名传图
		'anonymousMaxFileSize' 	=> 2048 * 1024, 	#匿名最大传图大小(单位K)
		'userMaxFileSize' 		=> 10240 * 1024,	#用户最大传图大小(单位K)
		'allowRegister' 		=> true,			#是否允许注册
		'userDataFile' 			=> 'data/userdata'	#用户信息所在位置
	),
	'file' => Array(
		'allowFileType' => Array(					#允许文件类型
			IMAGETYPE_GIF 		=> 'gif', 
			IMAGETYPE_JPEG 		=> 'jpg', 
			IMAGETYPE_PNG 		=> 'png', 
			IMAGETYPE_WEBP 		=> 'webp'			#PHP需要7.1才支持此文件类型判断
		),
		'customHeaders'			=> 'KotoriRequest:FileUpload',
		'uploadFolder' 			=> 'uploads',		#上传文件夹
		'thumbFolder' 			=> 'thumbs',		#略缩图文件夹
		'r18Cover' 				=> 'r18cover.jpg',	#r18封面名称
		'imageDataFile' 		=> 'data/imagedata',#图片信息所在位置
		'imagePerPage' 			=> 10,
		'thumbHeight' 			=> 180,				#略缩图高度 
	),
	'database' => Array(
		'sqliteFile'			=> 'data/kotori.db',#sqlite数据库文件名
		'imageTableName' 		=> 'imagedata',		#image表名称
		'userTableName'			=> 'userdata',		#user表名称
		'statisticsTableName'	=> 'statistics'		#statistics表名称
	),
	'site' => Array(
		'manageImagePerPage' 	=> 20,				#管理页面每页显示图片
		'rewriteURI' 			=> false,			#是否启用Rewrite URI
		'templateName' 			=> 'default',		#使用的模板名称，默认为default
	)
);
?>