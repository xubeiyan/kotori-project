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
		$this ->conf = $config;
		$this ->client_info = $clientInfo;
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
				'upload_class' => 'select',
				'random_class' => '',
				'list_class' => '',
				'anonymous_file_size_limit' => Util::suitableSize(
					$this ->conf['user']['anonymousMaxFileSize']),
				'script' => 'upload.js',
			);
			Util::template('uploadFile.html', $templateArray);
			exit();
		} else if ($page == 'random_image_page') {
			$imageArray = Image::randomImage();

			// 没有图片
			if (empty($imageArray)) {
				$templateArray = Array(
					'title' => '出错了',
					'upload_class' => '',
					'random_class' => 'select',
					'list_class' => '',
					'userinfo' => $user_panel,
					'error' => '没有可显示的图片',
				);
			
				Util::template('error.html', $templateArray);
				exit();
			}

			global $config;
			$templateArray = Array(
				'title' => '随便看看',
				'userinfo' => $user_panel,
				'upload_class' => '',
				'random_class' => 'select',
				'list_class' => '',
				'filename' => $imageArray['filename'],
				'imgPath' => $config['file']['uploadFolder'] . '/' . $imageArray['filename'],
				'uploader' => $imageArray['uploader'],
				'size'	=> Util::suitableSize($imageArray['size']),
				'uploadtime' => Util::formatTime($imageArray['upload_time']),
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
					'upload_class' => '',
					'random_class' => '',
					'list_class' => 'select',
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
				'upload_class' => '',
				'random_class' => '',
				'list_class' => 'select',
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
			$fileInfo = Image::getImageInfoFromName($pageInfoArray['name']);
			$templateArray = Array(
				'title' => '浏览图片',
				'userinfo' => $user_panel,
				'imgPath' => sprintf("%s/%s.%s", 
					$config['file']['uploadFolder'], $fileInfo['filename'], $fileInfo['filetype']),
				'filename' => $fileInfo['filename'],
				'filetype' => $fileInfo['filetype'],
				'uploader' => $fileInfo['uploader'],
				'size' => Util::suitableSize($fileInfo['size']),
				'uploadtime' => Util::formatTime($fileInfo['upload_time']),
			);
			Util::template('view.html', $templateArray);
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