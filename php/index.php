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

// 调用router类，生成一个router
require 'lib/Router.php';
$router = new Router($config, $clientInfo);

// 处理页面参数
$pageInfoArray = Util::parameterParser($clientInfo['query']);

// 路由GET部分
if ($clientInfo['requestMethod'] == 'GET') {
	// 直接访问根目录
	if ($pageInfoArray['req'] == '') {
		$router -> routeTo('upload_page');
		exit();
	// 上传文件	
	} else if ($pageInfoArray['req'] == 'upload') {
		// var_dump($_SESSION['currentUser']);
		$router -> renderPage('upload_page');
		
	// 随机访问图片
	} else if ($pageInfoArray['req'] == 'random') {
		// 空的直接返回没有文件可随机
		if (Image::isImageFileEmpty($config['file']['imageDataFile'])) {
			$router -> renderPage('noimage_random_error_page');
		} else {
			$router -> renderPage('random_image_page');
		}
	// 列出图片
	} else if ($pageInfoArray['req'] == 'list') {
		// 空的直接返回没有文件可列出
		if (Image::isImageFileEmpty($config['file']['imageDataFile'])) {
			$router -> renderPage('noimage_list_error_page');
		} else {
			$router -> renderPage('list_page');
		}
	// 查看某张图片
	} else if ($pageInfoArray['req'] == 'view') {
		$fullPath = sprintf("%s/%s", $config['file']['uploadFolder'], 	
			$pageInfoArray['name']);
		if (isset($pageInfoArray['name']) && 
		Image::isFileExist($fullPath)) {
			$router -> renderPage('view_image_page');
		} else {
			$router -> renderPage('no_such_image_page');
		}
	// 注册
	} else if ($pageInfoArray['req'] == 'reg') {
		$router -> renderPage('register_page');
	// 登录	
	} else if ($clientInfo['query'] == 'login') {
		$router -> renderPage('login_page');
		
	// 用户信息	
	} else if ($clientInfo['query'] == 'userinfo') {
		// 如果是管理员就跳转到管理页面
		if ($_SESSION['currentUser']['id'] == 0) {
			header('refresh:0;url=?manage');
			exit();
		}
		
		$userinfo = User::getUserInfo();
		
		$templateArray = Array(
			'title' => '用户信息',
			'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
			'script' => 'userinfo.js',
			'id' => 'user id: ' . $userinfo['id'],
			'username' => 'username: ' . $userinfo['username'],
		);
		
		Util::template('userinfo.html', $templateArray);
	// 退出登录
	} else if ($clientInfo['query'] == 'logout') {
		session_unset();
		header('refresh:0;url=.');
	// 管理页面（于是现在如何认定管理员呢……暂时认为叫kotori的就是管理员吧）
	} else if (substr($clientInfo['query'], 0, 6) == 'manage') {
		if ($_SESSION['currentUser']['username'] != $config['user']['adminUserName']) {
			$templateArray = Array(
				'title' => '出错了',
				'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
				'error' => '当前用户不是管理员用户',
			);
		
			Util::template('error.html', $templateArray);
			exit();
		}
		
		// 处理页面参数
		$pageInfo = Util::parameterParser($clientInfo['query']);
		
		// 检测是否提供了页面值，否则赋值为1
		// 2017.10.17 修改为last跳到最后一页
		if ($pageInfo['manage'] == 'last') {
			$page = Image::getLastPage($config['file']['imagePerPage']);
		} else if ($pageInfo['manage'] == '' || !is_numeric($pageInfo['manage'])) {
			$page = 1;
		} else {
			$page = intval($pageInfo['manage']);
		}
		
		$managePerPage = $config['site']['manageImagePerPage'];
		
		$imageListArray = Image::generateImageList($page, $managePerPage);
		
		if ($imageListArray == 'NoImageForList') {
			$templateArray = Array(
				'title' => '出错了',
				'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
				'error' => '没有图片可供管理',
			);
		
			Util::template('error.html', $templateArray);
			exit();
		}
		
		$imageList = Image::generateManageListTemplate($imageListArray);
		
		// 计算上一页和下一页的值
		$prev = $page == 1 ? 1 : $page - 1;
		$next = $page + 1;
		
		$templateArray = Array(
			'title' => '图片管理',
			'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
			'imagelist' => $imageList,
			'use' => 'manage',
			'prev' => $prev,
			'next' => $next,
			'first-d' => $page == 1 ? 'disabled' : '',
			'prev-d' => $page == 1 ? 'disabled' : '',
			'next-d' => count($imageListArray) == $config['file']['imagePerPage']? '' : 'disabled',
			'last-d' => $pageInfo['manage'] == 'last' ? 'disabled' : '',
			'script' => 'manage.js'
		);
		
		Util::template('manage.html', $templateArray);
	// 查看上传图片
	} else if (substr($clientInfo['query'], 0, 10) == 'userupload') {
		// 判断是不是匿名用户
		if ($_SESSION['currentUser']['anonymous'] == 1) {
			$templateArray = Array(
				'title' => '出错了',
				'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
				'error' => '匿名用户不可管理上传图片',
			);
			Util::template('error.html', $templateArray);
			exit();
		}
		
		// 处理页面参数
		$pageInfo = Util::parameterParser($clientInfo['query']);
		
		$userid = $_SESSION['currentUser']['id'];
		
		if ($pageInfo['userupload'] == 'last') {
			$page = Image::getLastPage($config['file']['imagePerPage']);
		} else if ($pageInfo['userupload'] == '' || !is_numeric($pageInfo['userupload'])) {
			$page = 1;
		} else {
			$page = intval($pageInfo['userupload']);
		}
		
		$managePerPage = $config['site']['manageImagePerPage'];
		
		$imageListArray = Image::getImageListByUploader($page, $managePerPage, $userid);
		$imageList = Image::generateManageListTemplate($imageListArray);
		
		// 计算上一页和下一页的值
		$prev = $page == 1 ? 1 : $page - 1;
		$next = $page + 1;
		
		$templateArray = Array(
			'title' => '图片管理',
			'userinfo' => User::generateRegisterandLoginList($clientInfo['query']),
			'imagelist' => $imageList,
			'use' => 'userupload',
			'prev' => $prev,
			'next' => $next,
			'first-d' => $page == 1 ? 'disabled' : '',
			'prev-d' => $page == 1 ? 'disabled' : '',
			'next-d' => count($imageListArray) == $config['site']['manageImagePerPage']? '' : 'disabled',
			'last-d' => $pageInfo['userupload'] == 'last' ? 'disabled' : '',
			'script' => 'manage.js'
		);
		
		Util::template('manage.html', $templateArray);
		exit();
		
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
			$imageTable = $config['database']['imageTableName'];
			
			header('Content-type:application/json');
			$result = Image::uploadFile($_FILES['img'], $imageTable);
			echo $result;
			exit();
		} else {
			Util::err('uploadFileFailed');
		}
	// 注册
	} else if ($clientInfo['query'] == 'registerpost') {
		// 如果不允许注册，即config['user']['allowRegister'] != true
		if (!$config['user']['allowRegister']) {
			$notReg = User::notAllowRegister();
			header('Content-type:application/json');
			echo $notReg;
			exit();
		}
		
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
		exit();
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
		exit();
	// 更新用户信息
	} else if ($clientInfo['query'] == 'userinfopost') {
		$userInfo = Util::dataInspection('userinfo', $_POST);
		
		$result = User::updateUserInfo($config['user']['userDataFile'], $userInfo);
		
		echo $result;
		exit();
	// 修改图片可见性
	} else if ($clientInfo['query'] == 'managepost') {
		$imageDataFile = $config['file']['imageDataFile'];
		$modifyInfo = $_POST;
		
		// 判断是管理员还是普通用户
		if ($_SESSION['currentUser']['username'] != $config['user']['adminUserName'] || $_SESSION['currentUser']['password'] != $config['user']['adminPassword']) {
			$result = Image::modifyStatus($imageDataFile, $modifyInfo, $_SESSION['currentUser']['id']);
		} else {
			$result = Image::modifyStatus($imageDataFile, $modifyInfo);
		}
		
		header('Content-type:application/json');
		echo $result;
		exit();
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