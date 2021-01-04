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
		if ($page == 'upload_page') {
			$templateArray = Array(
				'title' => '上传文件',
				'userinfo' => User::generateRegisterandLoginList($this ->client_info['query']),
				'script' => 'upload.js',
			);
			Util::template('uploadFile.html', $templateArray);
			exit();
		} else if ($page == 'random_image_page') {
			$imageArray = Image::randomImage($config['file']['imageDataFile']);
			$templateArray = Array(
				'title' => '随便看看',
				'userinfo' => User::generateRegisterandLoginList($this ->client_info['query']),
				'filename' => $imageArray['filename'],
				'imgPath' => $config['file']['uploadFolder'] . '/' . $imageArray['filename'],
				'uploader' => $imageArray['uploader'],
				'size'	=> Util::suitableSize($imageArray['size']),
				'uploadtime' => Util::formatTime($imageArray['uploadtime']),
			);
			// Image::generateHeader($imageArray['filename']);
			Util::template('random.html', $templateArray);
		} else if ($page == 'noimage_random_error_page') {
			$templateArray = Array(
				'title' => '出错了',
				'userinfo' => User::generateRegisterandLoginList($this ->client_info['query']),
				'error' => '没有可显示的图片',
			);
		
			Util::template('error.html', $templateArray);
			exit();
		}
	}
}
?>