<?php
/**
* 配置文件
* 仅包含$config这一个变量
*/
$config = Array (
	'user' => Array(
		'adminUserName' => 'kotori',		#管理员用户名
		'adminPassword' => 'kotori',		#管理员密码
		'allowAnonymousUpload' => true, 	#是否允许匿名传图
		'anonymousMaxFileSize' => 4096, 	#匿名最大传图大小(单位K)
		'userMaxFileSize' => 10240,			#用户最大传图大小(单位K)
		'allowRegister' => true,			#允许注册
		'userDataFile' => 'data/userdata'	#用户信息所在位置
	),
	'file' => Array(
		'allowFileType' => Array(			#允许文件类型
			IMAGETYPE_GIF => 'gif', 
			IMAGETYPE_JPEG => 'jpg', 
			IMAGETYPE_PNG => 'png', 
			IMAGETYPE_WBMP => 'webp'
		),
		'uploadFolder' => 'uploads',		#上传文件夹
		'thumbFolder' => 'thumbs',			#略缩图文件夹
		'r18Cover' => 'r18cover.jpg',		#r18封面名称
		'imageDataFile' => 'data/imagedata',#图片信息所在位置
		'imagePerPage' => 10,
		'thumbWidth' => 200,				#略缩图宽度 
	),
	'site' => Array(
		'manageImagePerPage' => 20,			#管理页面每页显示图片
		'rewriteURI' => false,				#是否启用Rewrite URI
		'template' => 'default',			#使用模板名称
	)
);
?>