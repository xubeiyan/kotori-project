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
		if (isset($_SERVER['HTTP_REFERER'])) {					// 前一页面地址
			$info['referer'] = $_SERVER['HTTP_REFERER'];
		}			
		return $info;
	}
	
	// 输出错误信息
	public static function err($errType, $errinfo = Array()) {
		// TODO：须调用对应的错误信息模板模板
		// 未找到配置文件
		if ($errType == 'missConfigurationFile') {
			
		// 不允许的方法
		} else if ($errType == 'notAllowedReqMethod' ) {
			
		}
		exit();
	}
}
?>