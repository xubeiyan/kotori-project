<?php
/**
* 入口文件
*/

require 'lib/Util.php';
// 检查配置文件状态
if(!file_exists('config/conf.php')) {
	//die('miss configuation file -> config/conf.php');
	Util::err('missConfigurationFile');
}

// 导入配置文件，配置变量$config
require 'config/conf.php';
global $config;

// 调用Util类，获取一些基本信息
$clientInfo = Util::getClientInfo();

// 调用User类，建立SESSION
require 'lib/User.php';
// 使用SESSION
session_start();
User::sessionCheck($config['user']['userDataFile'], $clientInfo['remoteAddr']);

// 调用Image类
require 'lib/Image.php';

if ($config['site']['rewriteURI'] == true) {
	
}
// 路由GET部分
if ($clientInfo['requestMethod'] == 'GET') {
	// 直接访问根目录
	if ($clientInfo['query'] == '') {
		header('refresh:0;url=?upload');
		exit();
	// 上传文件	
	} else if ($clientInfo['query'] == 'upload') {
		// var_dump($_SESSION['currentUser']);
		$templateArray = User::generateRegisterandLoginList($clientInfo['query']);
		Util::template('uploadFile.html', $templateArray);
	// 随机访问图片
	} else if ($clientInfo['query'] == 'random') {
		$imageArray = Image::randomImage($config['file']['imageDataFile']);
		
		$templateArray = Image::generateRandomTemplate($imageArray);
		$templateArray = array_merge($templateArray, User::generateRegisterandLoginList($clientInfo['query']));
		// Image::generateHeader($imageArray['filename']);
		Util::template('random.html', $templateArray);
	// 列出图片
	} else if (substr($clientInfo['query'], 0, 4) == 'list') {
		$listArray = explode('=', $clientInfo['query']);
		// 检测是否提供了页面值，否则赋值为1
		if (!isset($listArray[1]) || !is_numeric($listArray[1]) || $listArray[1] <= 0) {
			$listArray[1] = 1;
		} else {
			$listArray[1] = intval($listArray[1]);
		}
		$imageListArray = Image::generateImageList($listArray[1], $config['file']['imagePerPage']);
		// var_dump($listArray[1]);
		$templateArray = Image::generateListTemplate($imageListArray, $listArray[1]); 
		$templateArray = array_merge($templateArray, User::generateRegisterandLoginList($clientInfo['query']));
		Util::template('list.html', $templateArray);
	// 注册
	} else if ($clientInfo['query'] == 'register') {
		Util::template('register.html');
	// 登录	
	} else if ($clientInfo['query'] == 'login') {
		$templateArray = User::generateRegisterandLoginList($clientInfo['query']);
		Util::template('login.html', $templateArray);
	// 用户信息	
	} else if ($clientInfo['query'] == 'userinfo') {
		$templateArray = User::getUserInfo();
		$templateArray = array_merge($templateArray, User::generateRegisterandLoginList($clientInfo['query']));
		Util::template('userinfo.html', $templateArray);
	// 清除session，跳转至首页
	} else if ($clientInfo['query'] == 'logout') {
		session_unset();
		header('refresh:0;url=.');
	} else {
		$errInfo = Array(
			'query' => $clientInfo['query'],
		);
		Util::err('notAllowedReqQuery', $errInfo);
	}
// 路由POST部分
} else if ($clientInfo['requestMethod'] == 'POST') {
	// 上传图片
	if ($clientInfo['query'] == 'uploadpost') {
		//print '<img src=' . $_POST['img'] . '>';
		if (isset($_FILES['img'])) {
			$imageDataFile = $config['file']['imageDataFile'];
			$result = Image::uploadFile($_FILES['img'], $imageDataFile);
			
			header('Content-type:application/json');
			echo $result;
			exit();
		} else {
			Util::err('uploadFileFailed');
		}
	// 注册
	} else if ($clientInfo['query'] == 'registerpost') {
		$returnArray = Array();
		
		$registerInfo = Array();
		$registerInfo['username'] = $_POST['username'];
		$registerInfo['password'] = $_POST['password'];
		$registerInfo['ip'] = $clientInfo['remoteAddr'];
		$registerInfo['anonymous'] = '0';
		$returnArray['api'] = User::addUserData($config['user']['userDataFile'], $registerInfo);
		$returnArray['currentUser'] = $_SESSION['currentUser'];
		
		header('Content-type:application/json');
		echo json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		
		// print_r($_POST);
		// print_r($_SESSION['currentUser']);
	// 登录
	} else if ($clientInfo['query'] == 'loginpost') {
		$loginInfo = Array();
		$loginInfo['username'] = $_POST['username'];
		$loginInfo['password'] = $_POST['password'];
		
		$returnArray = Array();
		$returnArray['info'] = User::login($config['user']['userDataFile'], $loginInfo);
		
		header('Content-type:application/json');
		echo json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		
	// 更新用户信息
	} else if ($clientInfo['query'] == 'userinfopost') {
		$userInfo = User::dataInspection('userinfo', $_POST);
		
		header('Content-type:application/json');
		
		if (isset($userInfo['error'])) {
			$returnArray = Array(
				'api' => 'modify fail',
				'error' => $userInfo['error'],
			);
			
			echo json_encode($returnArray, JSON_UNESCAPED_UNICODE);
			exit();
		}
		
		global $config;
		// print_r($userInfo);
		User::updateData($config['user']['userDataFile'], $userInfo);
		$_SESSION['currentUser'] = $userInfo;
		
		$returnArray = Array(
			'api' => 'modify success',
		);
		
		echo json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		exit();
	// 未知的请求
	} else {
		Util::err('notAllowedReqQuery');
	}
// 其他不是知道什么的部分
} else {
	Util::err('notAllowedReqMethod');
}
?>