<?php
/**
* User类
*/
class User {
	
	// 将用户信息数组转为字符串
	public static function detailArraytoString($detailArray) {
		// 判断数组长度是否为5
		if (count($detailArray) != 5) {
			die('the length of user detail string is invaild(in array2string)...');
		}
		$format = '%-10s|%-20s|%-40s|%-50s|%s';
		$detailString = sprintf($format, $detailArray['id'], $detailArray['username'], 
			$detailArray['password'], $detailArray['ip'], $detailArray['anonymous']);
		return $detailString;
	}
	
	// 将用户资料字符串转为数组
	public static function detailStringtoArray($detailString) {
		$splitString = explode('|', $detailString);
		// 增加一个数组长度判断
		if (count($splitString) != 5) {
			die('the length of user detail string is invaild(in string2array)...');
		}
		
		$detailArray = Array(
			'id' => trim($splitString[0]),
			'username' => trim($splitString[1]),
			'password' => trim($splitString[2]),
			'ip' => trim($splitString[3]),
			'anonymous' => trim($splitString[4]),
		);
		return $detailArray;
	}
	
	// 更新用户信息
	public static function updateData($file, $content) {
		$fp = fopen($file, 'r+') or die('can not open file: ' . $file);
		
		// 跳过前面的#行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		// 跳过id不符合的若干行
		for ($line = fgets($fp), $id = self::detailStringtoArray($line)['id']; 
			$content['id'] < $id;
			$line = fgets($fp), $id = self::detailStringtoArray($line)['id']);
		$lineLength = strlen($line);

		if ($content['id'] == $id) {
			fseek($fp, 0 - $lineLength, SEEK_CUR);
			$write = self::detailArraytoString($content);
			fwrite($fp, $write) or die('write userdata file failed!');
		} else {
			die('seem not exist the line with id=' . $content['id']);
		}
		fclose($fp);
	}
	
	// 增加新用户信息
	public static function addData($file, $content) {
		$fp = fopen($file, 'a+') or die('can not open file: ' . $file);
		
		// 中间加1是|所占字节，最后加2为换行和回车(还是改为读到\r\n停止呢)
		// TODO: 更改为更合适的方法，目前方法需要上一行长度和下面相同
		fseek($fp, 0 - (10 + 1 + 20 + 1 + 40 + 1 + 40 + 1 + 1 + 2), SEEK_END);
		$line = fgets($fp);
		// 如果第一行没有的话，直接将id赋值为1
		if ($line[0] == '#') {
			$content['id'] = 1;
		} else {
			$id = self::detailStringtoArray($line)['id'];
			$content['id'] = intval($id) + 1;
		}
		
		fseek($fp, 0, SEEK_END);
		$write = self::detailArraytoString($content);
		fwrite($fp, $write) or die('write userdata file failed!');
		fwrite($fp, "\r\n");
		fclose($fp);
	}
	
	// 生成随机匿名帐号
	// 看起来目前$content里只需要提供ip
	public static function addAnonymous($file, $content) {
		if (!isset($content['ip'])) {
			die('the ip seems not to set...');
		}
		
		// 使用kotori+当前时间戳的md5的前20位作为用户名
		$content['username'] = substr(md5('kotori' + mktime()), 0, 20);
		$content['password'] = sha1('kotori');
		$content['anonymous'] = '1';
		
		self::addData($file, $content);
	}
	
	// 检查分配SESSION
	public static function sessionCheck() {
		
	}
}

?>