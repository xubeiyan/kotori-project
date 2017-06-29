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
			return 'fail';
		}
	}

	/**
	* 生成图片信息字符串
	* 具体请参考data\imagedata文件头部说明
	*/
	public static function imagedataArray2String($imageArray) {
		// 判断数组长度是否为5
		if (count($imageArray) != 6) {
			die('the length of user detail string is invaild(in array2string)...');
		}
		$format = '%-32s|%-8s|%-37s|%-10s|%-17s|%s';
		$detailString = sprintf($format, $imageArray['id'], $imageArray['size'], 
			$imageArray['filename'], $imageArray['uploader'], $imageArray['uploadtime'], $imageArray['r18']);
		return $detailString;
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
}
?>