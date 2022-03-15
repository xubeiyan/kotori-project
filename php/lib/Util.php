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
	
	/*
	* 处理页面参数返回一个数组
	* 例如list&page=1&uid=2
	* 返回['req' =>'list', 'page' => '1', 'uid' => 2]
	* 没有值['req' => '']
	*/
	public static function parameterParser($param) {
		$paramPair = explode('&', $param);
		$returnArray = Array( // 将第一个作为req参数
			'req' => array_shift($paramPair),
		);
		
		foreach ($paramPair as $value) {
			$pair = explode('=', $value);
			$returnArray[$pair[0]] = isset($pair[1]) ? $pair[1] : '';
		}
		return $returnArray;
	}
	
	/* 
	* 获取对应的模板并渲染
	* 参数：要渲染的页面名称，已经模板字符串数组
	*/
	public static function template($pageName, $templateStringArray = Array()) {
		global $config;
		// 是否有对应字段来确定模板名称
		if (!isset($config['site']['template'])) {
			$templateFolder = 'templates/' . $config['site']['templateName'];
		} else {
			$templateFolder = 'templates/default';
		}
		
		$templateStringArray['template_name'] = $templateFolder;
		$templateStringArray['current_year'] = date('Y');
		
		// 脚手架文件
		$scaffold = file_get_contents($templateFolder . '/template.html');
		$templatePage = file_get_contents($templateFolder . '/' . $pageName);
		$headerPage = file_get_contents($templateFolder . '/header.html');
		$footerPage = file_get_contents($templateFolder . '/footer.html');
		$scriptPage = self::jstemplate($templateStringArray, $templateFolder);
		
		// 最终模板文件
		$finalTemplate = Array(
			'include' => Array(
				'header' => $headerPage,
				'main' => $templatePage,
				'footer' => $footerPage,
				'script' => $scriptPage,
			),
			'string' => $templateStringArray,
		);
		$content = $scaffold;
		
		// 替换页面部分
		foreach ($finalTemplate['include'] as $key => $value) {
			$from = sprintf('<%% %s %%>', $key);
			$to = $value;
			$content = str_replace($from, $to, $content);
		}
		
		// 替换字符串部分
		foreach ($finalTemplate['string'] as $key => $value) {
			$from = sprintf('{%% %s %%}', $key);
			$to = $value;
			$content = str_replace($from, $to, $content);
		}

		echo $content;
		exit();
	}
	
	/*
	* 输出需要的js文件
	*/
	private static function jstemplate($fileArrayString, $folder) {
		if (!isset($fileArrayString['script'])) {
			return '';
		}
		
		$returnString = '';
		$fileArray = explode(',', $fileArrayString['script']);
		foreach ($fileArray as $value) {
			$completeSrc = sprintf('%s/js/%s', $folder, $value); 
			$returnString .= sprintf('<script type="text/javascript" src="%s"></script>', $completeSrc);
		}
		
		return $returnString;
	}
	
	// 输出错误信息
	public static function err($errType, $errinfo = Array()) {
		// TODO：须调用对应的错误信息模板模板
		// 未找到配置文件
		if ($errType == 'missConfigurationFile') {
			die(sprintf('can not find configuration file at %s, may not install?', $errinfo['configfile']));
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
			//
			$returnArray = Array (
				'api' => 'manage',
				'result' => 'set status fail',
				'error' => 'current user "' . $errinfo['username'] . '" is not administrator, can\'t update image status',
			);
			header('Content-type: application/json');
			echo json_encode($returnArray, JSON_UNESCAPED_UNICODE);
			exit();
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
	
	/*
	* 计算合适显示的文件大小，保留1位小数
	*/
	public static function suitableSize($byteSize) {
		$byteSize = 1 * $byteSize;
		if ($byteSize >= 1024 * 1024) {
			return sprintf('%.1f', $byteSize / 1024 / 1024) . 'MB';
		} else if ($byteSize >= 1024) {
			return sprintf('%.1f', $byteSize / 1024) . 'KB';
		} else {
			return sprintf('%.1f', $byteSize) . 'B';
		}
	}
	
	/*
	* 格式化时间
	* 由于imagedata里面存放的并不是时间戳，所以还是自己写个时间格式转换函数
	*/
	public static function formatTime($timeStr) {
		$year = substr($timeStr, 0, 4);
		$month = substr($timeStr, 4, 2);
		$day = substr($timeStr, 6, 2);
		$rest = substr($timeStr, 9);
		return sprintf("%s.%s.%s %s", $year, $month, $day, $rest);
	}
	
	/**
	* 清空所有图片和用户数据（跑路用
	* 危险！请谨慎调用此方法
	*/
	public static function clearAllData() {
		global $config;
		
		// 清空所有图片文件
		$path = $config['file']['uploadFolder'];
		
		foreach ($config['file']['allowFileType'] as $val) {
			$upper = '../' . $path . '/*.' . strtoupper($val);
			$lower = '../' . $path . '/*.' . strtolower($val);
			array_map('unlink', glob($upper));
			array_map('unlink', glob($lower));
		}
		
		$path = $config['file']['thumbFolder'];
		foreach ($config['file']['allowFileType'] as $val) {
			$upper = '../' . $path . '/*.' . strtoupper($val);
			$lower = '../' . $path . '/*.' . strtolower($val);
			array_map('unlink', glob($upper));
			array_map('unlink', glob($lower));
		}
		
		// 删除数据库中内容
		$db_file_path = $config['database']['sqliteFile'];
		$image_table = $config['database']['imageTableName'];
		$user_table = $config['database']['userTableName'];
		$statistics_table = $config['database']['statisticsTableName'];
		$db = new SQLite3('../' . $db_file_path);
	
		$sql = sprintf('DELETE FROM "%s"', $image_table);
		$db->exec($sql);
		$sql = sprintf('DELETE FROM "%s"', $user_table);
		$db->exec($sql);
		$sql = sprintf('DELETE FROM "%s"', $statistics_table);
		$db->exec($sql);
	}
}
?>