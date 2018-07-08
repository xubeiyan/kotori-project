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
			die('can not find configuration file at ' . $errinfo['configfile']);
		// 未找到图像
		} else if ($errType == 'noImageforRandom' ) {
			die('there is not any image for random');
		// 未为列表模式找到图像
		} else if ($errType == 'noImageforList' ) {
			die('there is not any image for list');
		// 不允许的请求方法
		} else if ($errType == 'notAllowedReqMethod' ) {
			die('this method ' . $errinfo['requestmethod'] . ' is not allowed');
		// 未找到处理该路由的方法
		} else if ($errType == 'notAllowedReqQuery') {
			die('the query is ' .$errinfo['query'] . ' which is not allowed');
		// 不允许的文件类型
		} else if ($errType == 'notAllowFileType') {
			// 不是图片文件
			if ($errinfo['filetype'] == '') {
				$info = 'not picture format';
			} else {
				$info = $errinfo['filetype'];
			}
			
			$returnArray = Array ( 
				'api' => 'upload',
				'result' => 'upload fail',
				'error' => 'file type is ' . $info . ', which is not allowed',
			);
			header('Content-type: application/json');
			echo json_encode($returnArray, JSON_UNESCAPED_UNICODE);
			exit();
		// 上传图片失败	
		} else if ($errType == 'uploadFileFailed') {
			die('upload file failed');
		// 非管理员进入manage页面
		} else if ($errType == 'notAdminUser') {
			die('current user "' . $errinfo['username'] . '" is not administrator');
		// 开启rewrite模式而未找到对应文件
		} else if ($errType == 'noRewriteFile') {
			die('can not find ' . $errinfo['rewritefile'] . ' file at ' . $errinfo['rewritefilefolder']);
		// 未找到的错误
		} else {
			die('Uncatched error: ' . $errType);
		}
		exit();
	}
	
	/** 
	* 检查外部输入
	* 原样返回传入数组或者返回一个有error的错误信息
	*/
	public static function dataInspection($inspectRule, $dataArray) {
		$returnArray = Array();
		
		// userinfo检查
		if($inspectRule == 'userinfo') {
			if (isset($dataArray['userid']) && isset($dataArray['oldpass']) && isset($dataArray['newpass']) && isset($dataArray['username'])) {
				$currentUser = $_SESSION['currentUser'];
				
				if ($currentUser['id'] != $dataArray['userid']) {
					$returnArray['error'] = 'the id seems not match...';
					return $returnArray;
				} 
				
				if ($currentUser['username'] != $dataArray['username']) {
					$returnArray['error'] = 'the username seems not match...';
					return $returnArray;
				}
				
				// print($currentUser['password'] . ' ' . sha1($dataArray['oldpass']));
				if ($currentUser['password'] != sha1($dataArray['oldpass'])) {
					// print('!');
					$returnArray['error'] = 'the password seems not match...';
					return $returnArray;
				}
				
				if (!$currentUser['anonymous'] == 0) {
					$returnArray['error'] = 'you can not modify the user information of anonymous...';
					return $returnArray;
				}
				
				$returnArray['id'] = $dataArray['userid'];
				$returnArray['username'] = $dataArray['username'];
				$returnArray['password'] = sha1($dataArray['newpass']);
				$returnArray['ip'] = $currentUser['ip'];
				$returnArray['anonymous'] = $currentUser['anonymous'];
				
				return $returnArray;
			} else {
				die('user information provided seems to wrong...');
			}
			
		} else if ($inspectRule == 'register') {
			
		} else if ($inspectRule == 'login') {
			
		} else {
			
		}
		
		return $returnArray;
	}
	
}
?>