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
	* 参数：$imagetable(要写入的imagetable)
	*/
	public static function uploadFile($img, $image_table) {
		// 先判断是否上传成功，不然imageFormatVerify那儿会出现一些问题
		if ($img['error'] != 0) {
			$err = Array(
				'OK',										// UPLOAD_ERR_OK
				'greater than upload_max_size in php.ini', 	// UPLOAD_ERR_INI_SIZE
				'greater than MAX_FILE_SIZE in HTML form',	// UPLOAD_ERR_FORM_SIZE
				'partial file was uploaded',				// UPLOAD_ERR_PARTIAL
				'no file was uploaded',						// UPLOAD_ERR_NO_FILE
				'can not find temp dir',					// UPLOAD_ERR_NO_TMP_DIR
				'file failed to write',						// UPLOAD_ERR_CANT_WRITE
			);
			$returnArray = Array (
				'api' => 'upload',
				'result' => 'upload fail',
				'error' => $err[$img['error']],
			);
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}
		
		$ext = self::imageFormatVerify($img);
		
		$md5Value = md5(time());
		
		$ia = Array(
			'size' => $img['size'],
			'fn' => $md5Value,
			'ft' => $ext,
			'upld' => $_SESSION['currentUser']['id'],
			'upldtime' => date("Ymd H:i:s"),
			'nsfw' => 0,
		);
		// print '!';
		$sql = sprintf('INSERT INTO `%s` 
				(size, 	uploader, 	filename, 	filetype, 	upload_time, 	nsfw) VALUES 
				(%d, 	%d, 		"%s", 		"%s", 		"%s", 			%d)',
				$image_table,
				$ia['size'], $ia['upld'], $ia['fn'], $ia['ft'], $ia['upldtime'], $ia['nsfw']);
		// $string = self::imagedataArray2String($imageArray);
		global $config;
		$db = new SQLite3($config['database']['sqliteFile']);
		if (!$db ->exec($sql)) { // 写入失败
			$returnArray = Array (
				'api' => 'upload',
				'result' => 'upload fail',
				'error' => $db ->lastErrorMsg(),
			);
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}
		
		$filePath = sprintf('uploads/%s.%s', $ia['fn'], $ia['ft']);
		if (move_uploaded_file($img['tmp_name'], $filePath)) {	
			$returnArray = Array (
				'api' => 'upload',
				'result' => 'upload success',
				'savePath' => $filePath,
			);
		} else {
			$returnArray = Array (
				'api' => 'upload',
				'result' => 'upload fail',
				'error' => 'move uploaded file failed',
			);
		}
		
		return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
	}

	/**
	* 生成图片信息字符串
	* 具体请参考data\imagedata文件头部说明
	*/
	public static function imagedataArray2String($imageArray) {
		// 判断数组长度是否为6
		if (count($imageArray) != 6) {
			print_r($imageArray);
			die('the length of image detail string is invaild(in array2string)...');
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
			print_r($instant);
			die('the length of image detail string is invaild(in string2array)...');
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
			Util::err('notAllowFileType', Array('filetype' => $imageFormat));
		}
		
		$ext = $allowFileType[$imageFormat];
		
		return $ext;
	}
	
	/**
	 * 判断文件是否存在
	 */
	public static function isFileExist($file) {
		if (file_exists($file)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 判断图像列表是否空的
	 * 
	 */
	public static function isImageFileEmpty($file) {
		$fp = fopen($file, 'r') or die('can not open file: ' . $file);
		// 跳过#开头的注释行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		if ($line == '') {
			return true;
		} else {
			return false;
		}
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

		for (; $selectLine > 0; $selectLine -= 1, $line = fgets($fp));
		
		$imageArray = self::imagedataString2Array($line);

		return $imageArray;
	}
	/**
	 * 根据文件名返回文件信息
	 */
	public static function getImageInfoFromName($fromFile, $filename) {
		$fp = fopen($fromFile, 'r') or die('can not open file: ' . $fromFile);
		// 跳过#开头的注释行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		// 跳过文件名不符合的若干行
		for ($line_filename= self::imagedataString2Array($line)['filename']; 
			$line !='' && $line_filename != $filename;
			$line = fgets($fp), $line_filename = self::imagedataString2Array($line)['filename']);
			
		$lineArray = self::imagedataString2Array($line);
		return $lineArray;
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
	* 生成列表访问的模板数组
	*/
	public static function generateListTemplate($array, $currentPage) {
		$imagelist = '';
		global $config;
		
		foreach ($array as $value) {
			if ($value['r18'] == 1) {
				$imagelist .= '<a href="uploads/' . $value['filename'] . '"><img title="好孩子不要点开！" src="templates/' . $config['site']['templateName'] . '/' . $config['file']['r18Cover'] . '" /></a>';
			} else {
				// $imagelist .= '<a href="uploads/' . $value['filename'] . '"><img src="'. Image::getThumb($value['filename']). '" /></a>';
				$imagelist .= sprintf('<a href="?view&name=%s"><img src="%s"></a>', $value['filename'], Image::getThumb($value['filename']));
			}
		}
		
		if ($currentPage == 1) {
			$prev = 1;
			$next = 2;
		} else {
			$prev = $currentPage - 1;
			$next = $currentPage + 1;
		}
		
		// $imageListTemplate = Array(
			// '%imagelist%' => $imagelist,
			// '%prev%' => $prev,
			// '%next%' => $next,
		// );
		return $imagelist;
	}
	
	/**
	* 生成管理用的列表
	* $array = Array(
	* 	'id' 		=> 图片ID（和文件名相同）
	* 	'uploader' 	=> 上传者ID
	* 	'filename' 	=> 文件名
	*	'size' 		=> 文件大小
	* )
	*/
	public static function generateManageListTemplate($array) {
		if (count($array) == 0) {
			return '<p>没有文件</p>';
		}
		
		$imagelist = '';
		
		global $config;
		
		foreach ($array as $value) {
			$id = $value['id'];
			$uploader = $value['uploader'];
			$filename = $value['filename'];
			$filesize = sprintf('%.3f', $value['size'] / 1024) . 'KB';
			
			$filetype = self::getImageType($filename);
			
			if (explode('.', $filename)[1] == 'gif')
			
			$yes = $no = '';
		
			if ($value['r18'] == 1) {
				$yes = 'selected="selected"';
				$no = '';
			} else {
				$yes = '';
				$no = 'selected="selected"';
			}
			
			$selectMenu = sprintf('<select class="r18select" id="%s">
				<option value="yes" %s>是</option>
				<option value="no" %s>否</option>
			</select>', $value['id'], $yes, $no);
				
			$imagelist .= sprintf('<div class="file-detail">
				<div class="left"><a href="uploads/%s"><img src="%s"/></a></div>
				<div class="right">
					<span class="id" title="Image ID">图片ID: <span class="important">%s</span></span>
					<span class="uploader" title="Uploader">上传者ID: %s</span>
					<span class="filesize" title="FileSize">文件大小: %s</span>
					<span class="filename" title="FileName">文件名: %s</span>
					<span class="filetype" title="FileType">文件类型: %s</span>
					<span class="special" title="R18">限制：%s</span>
				</div></div>', $filename, Image::getThumb($value['filename']), $id, $uploader, $filesize, $filename, $filetype, $selectMenu);
		}
		
		$imagelist .= '<button id="confirm">确认修改</button>';
		
		return $imagelist;
	}
	
	/**
	* 根据图像文件名判断文件类型
	* 返回值：（正常返回）
	* 	image/gif
	* 	image/jpeg
	* 	image/png
	* 	image/wbep
	* （异常返回）
	*	notImage
	* 	notAllowType
	*/
	public static function getImageType($filename) {
		$filenameArray = explode('.', $filename);
		
		if (!isset($filenameArray[1])) {
			return 'notImage';
		}
		
		$ext = strtolower($filenameArray[1]);
		
		if ($ext == 'gif') {
			return 'image/gif';
		} else if ($ext == 'jpg') {
			return 'image/jpeg';
		} else if ($ext == 'png') {
			return 'image/png';
		} else if ($ext == 'webp') {
			return 'image/webp';
		} else {
			return 'notAllowType';
		}
	}
	
	/**
	* 修改图片的r18属性
	* $file imagedata文件
	* $id 图片ID
	* $r18 r18值
	*/
	public static function modifyStatus($file, $modifyStatus, $role_id = -1) {
		$returnArray = Array(
			'api' => 'manageinfo',
			'result' => 'fail',
			'details' => Array(),
		);
		
		$fp = fopen($file, 'r+') or die('can not open file: ' . $file);
		// 跳过#开头的注释行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		foreach ($modifyStatus as $id => $r18) {
			// 跳过id不符合的行
			for ($lineID = self::imagedataString2Array($line)['id']; 
				$line !='' && $lineID != $id;
				$line = fgets($fp), $lineID = self::imagedataString2Array($line)['id']);
				
			$lineArray = self::imagedataString2Array($line);
			if ($role_id != -1 && $lineArray['uploader'] != $role_id) {
				continue;
			}
			if ($r18 == 'yes') {
				$lineArray['r18'] = 1;
				$displayText = "'hide'";
			} else if ($r18 == 'no') {
				$lineArray['r18'] = 0;
				$displayText = "'show'";
			}
			
			$newLine = self::imagedataArray2String($lineArray);
			
			fseek($fp, 0 - strlen($line), SEEK_CUR);
			
			fwrite($fp, $newLine) or die('failed to write into imagedata');
			
			$returnArray['result'] = 'success';
			array_push($returnArray['details'], 'display status of ' . $id . ' has changed to ' . $displayText);
		}
		fclose($fp);
		
		return json_encode($returnArray);
	}
	
	/**
	* 根据页数和每页图片数提供图片链接
	* 通过多读取一张判断是否后面还有
	*/
	public static function generateImageList($page, $imgPerPage) {
		global $config;
		$fp = fopen($config['file']['imageDataFile'], 'r') or die ('can not open file: ' . $config['file']['imageDataFile']);
		
		// 将传入的的页面值减1乘以每页图片得到要跳过的图片量
		$skipImage = ($page - 1) * $imgPerPage; 
		$imageSrcArray = Array();
		
		// 跳过#开头的注释行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		// 跳过前面的$skipImage行
		for (; $skipImage > 0 && $line != ''; $line = fgets($fp), $skipImage -= 1);
		
		// 是空则返回空数组
		if ($line == '') {
			return Array();
		}
		// 增加imagePerPage
		$imgPerPage += 1;
		for (; $imgPerPage > 0 && $line != ''; $imgPerPage -= 1, $line = fgets($fp)) {
			// print_r($line);
			$imageArray = self::imagedataString2Array($line);
			array_push($imageSrcArray, $imageArray);
		}
		
		fclose($fp);
		return $imageSrcArray;
	}
	
	/**
	* 指定uploader获取其图片的list
	*/
	public static function getImageListByUploader($page, $imgPerPage, $uploaderId) {
		global $config;
		$fp = fopen($config['file']['imageDataFile'], 'r') or die ('can not open file: ' . $config['file']['imageDataFile']);
		
		// 将传入的的页面值减1乘以每页图片得到要跳过的图片量
		$skipImage = ($page - 1) * $imgPerPage; 
		$imageSrcArray = Array();
		
		// 跳过#开头的注释行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		// 只选出该uploader的图
		for (; $imgPerPage > 0 && $line != ''; $line = fgets($fp)) {
			$imageArray = self::imagedataString2Array($line);
			if ($imageArray['uploader'] == $uploaderId) {
				// 跳过前面的
				if ($skipImage > 0) {
					$skipImage -= 1;
					continue;
				} else {
					array_push($imageSrcArray, $imageArray);
					$imgPerPage -= 1;
				}
			}
		}
		
		fclose($fp);
		return $imageSrcArray; 
	}
	
	/** 
	* 获得最后一页
	* 参数：每页的条数
	* 返回: 最后一页的页数
	*/
	public static function getLastPage($imgPerPage) {
		global $config;
		$fp = fopen($config['file']['imageDataFile'], 'r') or die ('can not open file: ' . $config['file']['imageDataFile']);
		
		// 跳过#开头的注释行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		// 获取一共有多少行
		$lineNum = 0;
		for (; !feof($fp); $line = fgets($fp), $lineNum += 1);
		
		// 没有则返回零
		if ($lineNum == 0) {
			return 0;
		}
		
		// ceil为向上取整
		return ceil($lineNum / $imgPerPage);
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
	
	// 清除所有图片信息（跑路用
	public static function clearAllImageData($file) {
		$fp = fopen($file, 'r+');
		# 读取前面的#行
		$temp = '';
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp)) {
			$temp .= $line;
		}
		fclose($fp);
		unlink($file);
		file_put_contents($file, $temp);
		// echo 'all image data cleared!<br>';
	}
}
?>