<?php
/**
* Image类
* 负责图片文件上传
* 方法有uploadFile(上传文件)
*/
class Image {
	/**
	* 上传文件
	* 参数：$img(需要上传的文件数组，目前是$_FILES['img'])
	* 参数：$file(要写入的imagedata文件)
	*/
	public static function uploadFile($img, $file) {
		
		if (empty($img)) {
			return 'empty';
		}
		
		$ext = self::imageFormatVerify($img);
		
		$fp = fopen($file, 'a') or die('can not open file: ' . $file);
		// $extArray = explode('.', $img['name']);
		// $ext = array_pop($extArray);
		
		$md5Value = md5(time());
		
		$imageArray = Array(
			'id' => $md5Value,
			'size' => $img['size'],
			'filename' => $md5Value . '.' . $ext,
			'uploader' => $_SESSION['currentUser']['id'],
			'uploadtime' => date("Ymd H:i:s"),
			'r18' => '0'
		);
		// print '!';
		$string = self::imagedataArray2String($imageArray);
		
		$filePath = 'uploads/' . $imageArray['filename'];
		if (move_uploaded_file($img['tmp_name'], $filePath)) {
			// print 'uploaded to folder ' . $filePath . "\r\n";
			fwrite($fp, $string);
			fwrite($fp, "\r\n");
			fclose($fp);
			
			$returnArray = Array (
				'api' => 'upload',
				'result' => 'upload success',
				'error' => '',
				'savePath' => $filePath,
			);
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		} else {
			fclose($fp);
			
			$returnArray = Array (
				'api' => 'upload',
				'result' => 'upload fail',
				'error' => $img['error'],
				'savePath' => '',
			);
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}
	}

	/**
	* 生成图片信息字符串
	* 具体请参考data\imagedata文件头部说明
	*/
	public static function imagedataArray2String($imageArray) {
		// 判断数组长度是否为6
		if (count($imageArray) != 6) {
			die('the length of user detail string is invaild(in array2string)...');
		}
		$format = '%-32s|%-8s|%-37s|%-10s|%-17s|%s';
		$detailString = sprintf($format, $imageArray['id'], $imageArray['size'], 
			$imageArray['filename'], $imageArray['uploader'], $imageArray['uploadtime'], $imageArray['r18']);
		return $detailString;
	}
	
	/**
	* 由图片信息字符串生成数组
	*/
	public static function imagedataString2Array($imageString) {
		$instant = explode('|', $imageString);
		if (count($instant) != 6) {
			die('the length of user detail string is invaild(in string2array)...');
		}
		
		$detailArray = Array(
			'id' =>  trim($instant[0]),
			'size' =>  trim($instant[1]),
			'filename' =>  trim($instant[2]),
			'uploader' =>  trim($instant[3]),
			'uploadtime' =>  trim($instant[4]),
			'r18' =>  trim($instant[5])
		);
		return $detailArray;
	}
	
	/** 
	* 图片格式检查
	* 发现只检查文件名根本不行，已完全修改为使用getimagesize获取文件类型
	* 返回文件类型
	*/
	public static function imageFormatVerify($image) {
		global $config;
		$extArray = explode('.', $image['name']);
		$ext = array_pop($extArray);
		
		// 使用getimagesize获取文件类型
		$imageFormat = exif_imagetype($image['tmp_name']);
		
		$allowFileType = $config['file']['allowFileType'];
		
		if (!in_array($imageFormat, array_keys($allowFileType))) {
			Util::err('notAllowFileType');
		}
		
		$ext = $allowFileType[$imageFormat];
		
		return $ext;
	}
	
	/**
	* 随机访问图片
	* 返回可读的
	*/
	public static function randomImage($file) {
		$fp = fopen($file, 'r') or die('can not open file: ' . $file);
		for ($lineNum = 0, $line = fgets($fp); !feof($fp); $line = fgets($fp)) {
			if ($line[0] == '#') {
				continue;
			}
			$lineNum += 1;
		}
		rewind($fp); // 返回文件开头
		// 跳过#开头的注释行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		$selectLine = rand(0, $lineNum - 1);
		// print($selectLine);
		for (; $selectLine > 0; $selectLine -= 1, $line = fgets($fp));
		
		if ($line == '') {
			Util::err('noImageforRandom');
		}
		
		$imageArray = self::imagedataString2Array($line);

		return $imageArray;
	}
	
	/**
	* 根据文件格式生成对应的http头
	*/
	public static function generateHeader($filename) {
		$filenameArray = explode('.', $filename);
		$ext = array_pop($filenameArray);
		
		// 增加一个转换成小写的步骤
		$ext = strtolower($ext);
		
		if ($ext == 'gif') {
			header('Content-type: image/gif');
		} else if ($ext == 'png') {
			header('Content-type: image/png');
		} else if ($ext == 'jpg') {
			header('Content-type: image/jpeg');
		} else if ($ext == 'webp') {
			header('Content-type: image/webp');
		}
	}
	
	/**
	* 生成随机访问的模板数组
	*/
	public static function generateRandomTemplate($imageArray) {
		global $config;

		$imageInfo = Array(
			'%imgPath%' => $config['file']['uploadFolder'] . '/' . $imageArray['filename'],
			'%filename%' => $imageArray['filename'],
			'%size%' => round($imageArray['size'] / 1024) . 'KB',
			'%uploader%' => $imageArray['uploader'],
			'%uploadtime%' => substr($imageArray['uploadtime'], 0, 4) . '年' . substr($imageArray['uploadtime'], 4, 2) . '月' . substr($imageArray['uploadtime'], 6, 2) . '日' . substr($imageArray['uploadtime'], 8),
		);
		return $imageInfo;
	}
	
	/**
	* 生成列表访问的模板数组
	*/
	public static function generateListTemplate($array, $currentPage) {
		$imagelist = '';
		global $config;
		
		foreach ($array as $value) {
			if ($value['r18'] == 1) {
				$imagelist .= '<a href="uploads/' . $value['filename'] . '"><img style="width:200px" title="好孩子不要点开！" src="'. $config['file']['r18Cover'] . '" /></a>';
			} else {
				$imagelist .= '<a href="uploads/' . $value['filename'] . '"><img style="width:200px" src="'. Image::getThumb($value['filename']). '" /></a>';
			}
		}
		
		if ($currentPage == 1) {
			$prev = 1;
			$next = 2;
		} else {
			$prev = $currentPage - 1;
			$next = $currentPage + 1;
		}
		
		$imageListTemplate = Array(
			'%imagelist%' => $imagelist,
			'%prev%' => $prev,
			'%next%' => $next,
		);
		return $imageListTemplate;
	}
	
	/**
	* 根据页数和每页图片数提供图片链接
	*/
	public static function generateImageList($page, $imgPerPage) {
		global $config;
		$fp = fopen($config['file']['imageDataFile'], 'r') or die ('can not open file: ' . $config['file']['imageDataFile']);
		
		// 将传入的的页面值减1
		$skipImage = ($page - 1) * $imgPerPage; 
		$imageInfoArray = Array();
		
		// print('image per page: ' . $imgPerPage);

		// 跳过#开头的注释行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		// 跳过前面的$skipImage行
		for (; $skipImage > 0; $line = fgets($fp), $skipImage -= 1);
		
		if ($line == '') {
			Util::err('noImageforList');
		}
		
		for (; $imgPerPage > 0 && $line != ''; $imgPerPage -= 1, $line = fgets($fp)) {
			// print_r($line);
			$imageArray = self::imagedataString2Array($line);
			array_push($imageInfoArray, $imageArray);
		}
		
		fclose($fp);
		return $imageInfoArray;
	}
	
	/**
	* 生成略缩图thumbs
	* 返回略缩图路径
	*/
	public static function getThumb($imageFile) {
		global $config;
		
		$thumbFolder = $config['file']['thumbFolder'];
		if (file_exists($thumbFolder . '/' . $imageFile)) {
			return $thumbFolder . '/' . $imageFile;
		}
		
		$uploadFolder = $config['file']['uploadFolder'];
		if (!file_exists($uploadFolder . '/' . $imageFile)) {
			die('file ' . $imageFile . ' seems not to exsit...');
		}
		
		
		$filenameArray = explode('.', $imageFile);
		$ext = array_pop($filenameArray);
		// 增加一个转换成小写的步骤
		$ext = strtolower($ext);
		
		if ($ext == 'jpg') {
			$img = imagecreatefromjpeg($uploadFolder . '/' . $imageFile);
		} else if ($ext == 'png') {
			$img = imagecreatefrompng($uploadFolder . '/' . $imageFile);
		} else if ($ext == 'webp') {
			$img = imagecreatefromwebp($uploadFolder . '/' . $imageFile);
		} else if ($ext == 'gif') {
			if (!copy($uploadFolder . '/' . $imageFile, $thumbFolder . '/' . $imageFile)) {
				die('copy gif file failed...');
			}
			return $thumbFolder . '/' . $imageFile;
		}
		if (!$img) {
			die('the file ' . $imageFile . ' seems to fail to open...');
		}
		
		
		$width = imagesx($img);
		$height = imagesy($img);
		
		$newWidth = $config['file']['thumbWidth'];
		$newHeight = round($height / $width * $newWidth);
		$newImage = imagecreatetruecolor($newWidth, $newHeight);
		
		if (!imagecopyresampled($newImage, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height)) {
			die('resize failed!');
		}
		
		if ($ext == 'jpg') {
			$result = imagejpeg($newImage, $thumbFolder . '/' . $imageFile);
		} else if ($ext == 'png') {
			$result = imagepng($newImage, $thumbFolder . '/' . $imageFile);
		} else if ($ext == 'webp') {
			$result = imagewebp($newImage, $thumbFolder . '/' . $imageFile);
		} else if ($ext == 'gif') {
			
		}
		
		if (!$result) {
			die('can not create a new image...');
		}
		
		return $thumbFolder . '/' . $imageFile;
	}
}
?>