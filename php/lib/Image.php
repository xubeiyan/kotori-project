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
		
		if (!self::imageFormatVerify($img)) {
			return 'not image';
		}
		
		$fp = fopen($file, 'a') or die('can not open file: ' . $file);
		$extArray = explode('.', $img['name']);
		$ext = array_pop($extArray);
		
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
			print 'uploaded to folder ' . $filePath . "\r\n";
			fwrite($fp, $string);
			fwrite($fp, "\r\n");
			fclose($fp);
			return 'success';
		} else {
			fclose($fp);
			return 'fail, error number: ' . $img['error'];
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
	* 目前只有扩展名检查诶嘿（喂节操呢
	* TODO: 增加个内容检查？
	*/
	public static function imageFormatVerify($image) {
		global $config;
		$extArray = explode('.', $image['name']);
		$ext = array_pop($extArray);
		$allowFileType = $config['file']['allowFileType'];
		// print_r($allowFileType);
		// print($ext);
		if (!in_array($ext, $allowFileType)) {
			Util::err('notAllowFileType');
		}
		
		return true;
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
		// print($line);
		$imageArray = self::imagedataString2Array($line);

		return $imageArray;
	}
	
	/**
	* 根据文件格式生成对应的http头
	*/
	public static function generateHeader($filename) {
		$filenameArray = explode('.', $filename);
		$ext = array_pop($filenameArray);
		
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
	* 根据随机访问的图片访问
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
}
?>