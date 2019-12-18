<?php
/**
* 入口文件
*/

require 'lib/Util.php';
// 检查配置文件状态
if(!file_exists('config/conf.php')) {
	//die('miss configuation file -> config/conf.php');
	Util::err('missConfigurationFile', Array('configfile' => 'config/conf.php'));
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

// 是否调用URI Rewrite
if ($config['site']['rewriteURI'] == true) {
	// 暂时只支持Apache的rewrite
	if (!file_exists('./.htaccess')) {
		Util::err('noRewriteFile', Array('rewritefile' => '.htaccess', 'rewritefilefolder' => '.'));
	}
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
		
		$templateArray = Array(
			'title' => '上传文件',
			'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
			'script' => 'upload.js',
		);
		Util::template('uploadFile.html', $templateArray);
	// 随机访问图片
	} else if ($clientInfo['query'] == 'random') {
		$imageArray = Image::randomImage($config['file']['imageDataFile']);
		
		// 如果返回没有图片可随机
		if ($imageArray == 'NoImageForRandom') {
			$templateArray = Array(
				'title' => '出错了',
				'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
				'error' => '没有可显示的图片',
			);
		
			Util::template('error.html', $templateArray);
			exit();
		}
		
		$templateArray = Array(
			'title' => '随便看看',
			'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
			'filename' => $imageArray['filename'],
			'imgPath' => $config['file']['uploadFolder'] . '/' . $imageArray['filename'],
			'uploader' => $imageArray['uploader'],
			'size'	=> Util::suitableSize($imageArray['size']),
			'uploadtime' => $imageArray['uploadtime'],
		);
		// Image::generateHeader($imageArray['filename']);
		Util::template('random.html', $templateArray);
	// 列出图片
	} else if (substr($clientInfo['query'], 0, 4) == 'list') {
		// 处理页面参数
		$pageInfo = Util::parameterParser($clientInfo['query']);
		
		// 检测是否提供了页面值，否则赋值为1
		// 2017.10.17 修改为last跳到最后一页
		if ($pageInfo['list'] == 'last') {
			$page = Image::getLastPage($config['file']['imagePerPage']);
		} else if ($pageInfo['list'] == '' || !is_numeric($pageInfo['list'])) {
			$page = 1;
		} else {
			$page = intval($pageInfo['list']);
		}
		
		$imageSrcArray = Image::generateImageList($page, $config['file']['imagePerPage']);
		
		if ($imageSrcArray == 'NoImageForList') {
			$templateArray = Array(
				'title' => '出错了',
				'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
				'error' => '没有可显示的图片',
			);
		
			Util::template('error.html', $templateArray);
			exit();
		}
		
		// 计算上一页和下一页的值
		$prev = $page == 1 ? 1 : $page - 1;
		$next = $page + 1;
		
		// var_dump($listArray[1]);
		$templateArray = Array(
			'title' => '文件列表',
			'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
			'imagelist' => Image::generateListTemplate($imageSrcArray, $page),
			'prev' => $prev,
			'next' => $next,
			'first-d' => $page == 1 ? 'disabled' : '',
			'prev-d' => $page == 1 ? 'disabled' : '',
			'next-d' => $pageInfo['list'] == 'last' ? 'disabled' : '',
			'last-d' => $pageInfo['list'] == 'last' ? 'disabled' : '',
		);
		
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
		if ($_SESSION['currentUser']['id'] == 0) {
			header('refresh:0;url=?manage');
			exit();
		}
		
		$templateArray = User::getUserInfo();
		$templateArray = array_merge($templateArray, User::generateRegisterandLoginList($clientInfo['query']));
		Util::template('userinfo.html', $templateArray);
	// 清除session，跳转至首页
	} else if ($clientInfo['query'] == 'logout') {
		session_unset();
		header('refresh:0;url=.');
	// 管理页面（于是现在如何认定管理员呢……暂时认为叫kotori的就是管理员吧）
	} else if (substr($clientInfo['query'], 0, 6) == 'manage') {
		$manageArray = explode('=', $clientInfo['query']);
		if (isset($manageArray[1]) && $manageArray[1] == 'last') {
			$manageArray[1] = Image::getLastPage($config['file']['imagePerPage']);
		} else if (!isset($manageArray[1]) || !is_numeric($manageArray[1]) || !$manageArray[1] > 0) {
			$manageArray[1] = 1;
		}
		
		$managePage = $manageArray[1];
		
		if ($_SESSION['currentUser']['username'] != $config['user']['adminUserName']) {
			Util::err('notAdminUser', Array('username' => $_SESSION['currentUser']['username']));
			exit();
		}
		
		$managePerPage = $config['site']['manageImagePerPage'];
		
		$imageListArray = Image::generateImageList($managePage, $managePerPage);
		$templateArray = Image::generateManageListTemplate($imageListArray, $managePage);
		$templateArray = array_merge($templateArray, User::generateRegisterandLoginList($clientInfo['query']));
		Util::template('manage.html', $templateArray);
		

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
		$result = User::addUserData($config['user']['userDataFile'], $registerInfo);
		// $returnArray['currentUser'] = $_SESSION['currentUser'];
		
		header('Content-type:application/json');
		echo $result;
		
		// print_r($_POST);
		// print_r($_SESSION['currentUser']);
	// 登录
	} else if ($clientInfo['query'] == 'loginpost') {
		$loginInfo = Array();
		$loginInfo['username'] = $_POST['username'];
		$loginInfo['password'] = $_POST['password'];
		
		// $returnArray = Array();
		$result = User::login($config['user']['userDataFile'], $loginInfo);
		
		header('Content-type:application/json');
		echo $result;
		
	// 更新用户信息
	} else if ($clientInfo['query'] == 'userinfopost') {
		$userInfo = Util::dataInspection('userinfo', $_POST);
		
		$result = User::updateUserInfo($config['user']['userDataFile'], $userInfo);
		
		echo $result;
		exit();
	// 更新管理信息
	} else if ($clientInfo['query'] == 'managepost') {
		// print_r($_SESSION['currentUser']);
		
		if ($_SESSION['currentUser']['username'] != $config['user']['adminUserName'] || $_SESSION['currentUser']['password'] != $config['user']['adminPassword']) {
			Util::err('notAdminUser', Array('username' => $_SESSION['currentUser']['username']));
			exit();
		}
		
		$imageDataFile = $config['file']['imageDataFile'];
		
		$modifyInfo = $_POST;
		
		$result = Image::modifyStatus($imageDataFile, $modifyInfo);
		
		header('Content-type:application/json');
		echo $result;
		
	// 未知的请求
	} else {
		$errInfo = Array(
			'query' => $clientInfo['query'],
		);
		Util::err('notAllowedReqQuery', $errInfo);
	}
// 其他不是知道什么的部分
} else {
	Util::err('notAllowedReqMethod', Array('requestmethod' => $clientInfo['requestMethod']));
}
?>