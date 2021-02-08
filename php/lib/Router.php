<?php
/**
 * Router类
 * // 主要用于解决index.php逻辑过于复杂的情况
 */ 
class Router {
	// 
	private $conf;
	private $client_info;
	
	// 初始化
	public function __construct($config, $clientInfo) {
		$conf = $config;
		$client_info = $clientInfo;
	}
	
	// 导航路由
	public function routeTo($route) {
		if ($route == 'upload_page') {
			header('refresh:0;url=?upload');
		}
	}
	
	// 渲染路由
	public function renderPage($page) {
		$user_panel = User::generateRegisterandLoginList($this ->client_info['query']);
		if ($page == 'upload_page') {
			$templateArray = Array(
				'title' => '上传文件',
				'userinfo' => $user_panel,
				'script' => 'upload.js',
			);
			Util::template('uploadFile.html', $templateArray);
			exit();
		} else if ($page == 'random_image_page') {
			global $config;
			$imageArray = Image::randomImage($config['file']['imageDataFile']);
			$templateArray = Array(
				'title' => '随便看看',
				'userinfo' => $user_panel,
				'filename' => $imageArray['filename'],
				'imgPath' => $config['file']['uploadFolder'] . '/' . $imageArray['filename'],
				'uploader' => $imageArray['uploader'],
				'size'	=> Util::suitableSize($imageArray['size']),
				'uploadtime' => Util::formatTime($imageArray['uploadtime']),
			);
			// Image::generateHeader($imageArray['filename']);
			Util::template('random.html', $templateArray);
			exit();
		} else if ($page == 'list_page') {
			global $config;
			global $pageInfoArray;
			if (!isset($pageInfoArray['page']) || $pageInfoArray['page'] == '' || !is_numeric($pageInfoArray['page'])) {
				$page = 1;
			} else if ($pageInfoArray['page'] == 'last') {
				$page = Image::getLastPage($config['file']['imagePerPage']);
			} else {
				$page = intval($pageInfoArray['page']);
			}
			
			$imageSrcArray = Image::generateImageList($page, $config['file']['imagePerPage']);

			// 如果空了显示没有可显示的图片
			if (empty($imageSrcArray)) {
				$templateArray = Array(
					'title' => '出错了',
					'userinfo' => $user_panel,
					'error' => '没有可显示的图片',
				);
			
				Util::template('error.html', $templateArray);
				exit();
			}

			// 计算上一页和下一页的值
			$prev = $page == 1 ? 1 : $page - 1;
			$next = $page + 1;
			
			$templateArray = Array(
				'title' => '文件列表',
				'userinfo' => $user_panel,
				'imagelist' => Image::generateListTemplate($imageSrcArray),
				'prev' => $prev,
				'next' => $next,
				'first-d' => $page == 1 ? 'disabled' : '',
				'prev-d' => $page == 1 ? 'disabled' : '',
				'next-d' => count($imageSrcArray) == ($config['file']['imagePerPage'] + 1) ? '' : 'disabled',
				'last-d' => isset($pageInfoArray['page']) && $pageInfoArray['page'] == 'last' ? 'disabled' : '',
			);
			
			Util::template('list.html', $templateArray);
			exit();
		} else if ($page == 'view_image_page') {
			global $config;
			global $pageInfoArray;
			$fileInfo = Image::getImageInfoFromName($config['file']['imageDataFile'], $pageInfoArray['name']);
			$templateArray = Array(
				'title' => $pageInfoArray['name'],
				'userinfo' => $user_panel,
				'imgPath' => sprintf("%s/%s", $config['file']['uploadFolder'], $pageInfoArray['name']),
				'filename' => $fileInfo['filename'],
				'uploader' => $fileInfo['uploader'],
				'size' => Util::suitableSize($fileInfo['size']),
				'uploadtime' => Util::formatTime($fileInfo['uploadtime']),
			);
			Util::template('random.html', $templateArray);
			exit();
		} else if ($page == 'register_page') {
			$templateArray = Array(
				'title' => '注册',
				'userinfo' => User::generateRegisterandLoginList(),
				'script' => 'register.js'
			);
			Util::template('register.html', $templateArray);
			exit();
		} else if ($page == 'login_page') {
			$templateArray = Array(
				'title' => '登录',
				'userinfo' => User::generateRegisterandLoginList(),
				'script' => 'login.js'
			);
			Util::template('login.html', $templateArray);
		}
	}
}
?>