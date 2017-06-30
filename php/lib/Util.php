<?php
/**
* Util类
*/
class Util {
	// 获取客户端信息
	public static function getClientInfo() {
		$info = Array();
		$info['userAgent'] = $_SERVER['HTTP_USER_AGENT'];		// agent字符串
		$info['remoteAddr'] = $_SERVER['REMOTE_ADDR'];			// 浏览当前页面的用户的IP
		$info['requestMethod'] = $_SERVER['REQUEST_METHOD'];	// 请求方法
		$info['query'] = $_SERVER['QUERY_STRING'];				// 请求参数
		if (isset($_SERVER['HTTP_REFERER'])) {					// 前一页面地址
			$info['referer'] = $_SERVER['HTTP_REFERER'];
		}
		return $info;
	}
	
	// 输出对应的结果
	public static function template($templateFile, $templateInfo = Array()) {
		global $config;
		$templateFolder = 'templates/' . $config['site']['template'];
		//print $file;
		$fileContent = file_get_contents($templateFolder .'/' . $templateFile);
		$fileContent = str_replace('%template%', $templateFolder, $fileContent);
		// $templateInfo非空则替换
		if ($templateInfo != Array()) {
			foreach ($templateInfo as $key => $value) {
				$fileContent = str_replace($key, $value, $fileContent);
			}
		}
		echo $fileContent;
		exit();
	}
	
	// 输出错误信息
	public static function err($errType, $errinfo = Array()) {
		// TODO：须调用对应的错误信息模板模板
		// 未找到配置文件
		if ($errType == 'missConfigurationFile') {
			
		// 不允许的请求方法
		} else if ($errType == 'notAllowedReqMethod' ) {
		
		// 未找到处理该路由的方法
		} else if ($errType = 'notAllowedReqQuery') {
			
		// 不允许的文件类型
		} else if ($errType = 'notAllowFileType') {
			print('不支持的文件类型');
			return;
		// 上传图片失败	
		} else if ($errType = 'uploadFileFailed') {
			die('IO::Error!');
		// 未找到的错误
		} else {
			
		}
		exit();
	}
	
	
}
?>