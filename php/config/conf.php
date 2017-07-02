<?php
/**
* 配置文件
* 仅包含$config这一个变量
*/
$config = Array (
	'user' => Array(
		'adminUserName' => 'kotori',
		'adminPassword' => 'kotori',
		'allowAnonymousUpload' => true, 	#是否允许匿名传图
		'anonymousMaxFileSize' => 4096, 	#匿名最大传图大小(单位K)
		'userMaxFileSize' => 10240,			#用户最大传图大小(单位K)
		'allowRegister' => true,			#允许注册
		'userDataFile' => 'data/userdata'	#用户信息所在位置
	),
	'file' => Array(
		'allowFileType' => Array('jpg', 'png', 'webp', 'gif'),
		'uploadFolder' => 'uploads',
		'thumbFolder' => 'thumbs',
		'imageDataFile' => 'data/imagedata',#图片信息所在位置
		'imagePerPage' => 20,
		'thumbWidth' => 200,				#略缩图宽度 
	),
	'site' => Array(
		'rewriteURI' => false,				#是否启用Rewrite URI
		'template' => 'default',			#使用模板名称
	)
);
?>