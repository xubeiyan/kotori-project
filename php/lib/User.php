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
		$format = '%-10s|%-20s|%-40s|%-40s|%s';
		$detailString = sprintf($format, $detailArray['id'], $detailArray['username'], 
			$detailArray['password'], $detailArray['ip'], $detailArray['anonymous']);
		return $detailString;
	}
	
	// 将用户资料字符串转为数组
	public static function detailStringtoArray($detailString) {
		//print($detailString);
		$splitString = explode('|', $detailString);
		//print_r($splitString);
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
	
	// 用户登录
	public static function login($file, $content) {
		if (!isset($content['username']) || !isset($content['password'])) {
			die('username or password is not set!');
		}
		
		$fp = fopen($file, 'r') or die('can not open file: ' . $file);
	
		// 跳过前面的#行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		$returnArray = Array(
			'api' => 'login',
		);
		
		for (; !feof($fp); $line = fgets($fp)) {
			$userdataArray = self::detailStringtoArray($line);
			if ($content['username'] == $userdataArray['username']) {
				if (sha1($content['password']) == $userdataArray['password']) {
					$_SESSION['currentUser'] = $userdataArray;
					$returnArray['result'] = 'login success';
					return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
				} else {
					$returnArray['result'] = 'login fail';
					$returnArray['error'] = 'password wrong';
					return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
				}
			}
			continue;
			
		}
		
		$returnArray['result'] = 'login fail';
		$returnArray['error'] = 'no user';
		return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
	}
	
	// 增加新用户信息
	public static function addData($file, $content) {
		//print('enter addData');
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
		return $content;
	}
	
	/** 
	* 增加用户（仅实名，匿名添加在sessionCheck中）
	* 返回JSON
	* 
	*/
	public static function addUserData($file, $content) {
		// 检查各项是否设置
		if (!isset($content['username'])) {
			die('the username seems not to set...');
		}
		
		if (!isset($content['password'])) {
			die('the password seems not to set...');
		}
		
		if (!isset($content['ip'])) {
			die('the ip seems not to set...');
		}
		
		$fp = fopen($file, 'r') or die('can not open file: ' . $file);
		
		// 跳过前面的#行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		$returnArray = Array(
			'api' => 'register',
		);
	
		// 貌似登入之前必然可能写入一行匿名用户信息，所以认为必然不可能到达文件结尾
		for (; !feof($fp); $line = fgets($fp)) {
			if ($content['username'] == self::detailStringtoArray($line)['username']) {
				$returnArray['result'] = 'register fail';
				$returnArray['error'] = 'user exits';
				return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
			}
		}
		
		if (feof($fp)) {
			$content['password'] = sha1($content['password']);
			
			if ($result = self::addData($file, $content)) {
				$_SESSION['currentUser'] = $result;
				$returnArray['result'] = 'register success';
			} else {
				die('write to userdata file failed');
			}
		}
		return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
	}
	
	// 生成随机匿名帐号
	// 看起来目前$content里只需要提供ip
	public static function addAnonymous($file, $content) {
		if (!isset($content['ip'])) {
			die('the ip seems not to set...');
		}
		
		// 使用kotori+当前时间戳的md5的前20位作为用户名
		$content['username'] = substr(md5('kotori' + time()), 0, 20);
		$content['password'] = sha1('kotori');
		$content['anonymous'] = '1';
		
		$content = self::addData($file, $content);
		return $content;
	}
	
	// 检查分配SESSION
	public static function sessionCheck($file, $currentIP) {
		if (isset($_SESSION['currentUser'])) {
			return;
		}
		
		$fp = fopen($file, 'r+') or die('can not open file: ' . $file);
	
		// 跳过前面的#行
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp));
		
		// 若是没有记录则生成一行然后返回
		if (feof($fp)) {
			fclose($fp);
			$content = self::addAnonymous($file, Array('ip' => $currentIP));
			$_SESSION['currentUser'] = $content;
			return;
		} 
		
		// 跳过ip不符合的几行
		
		$ip = self::detailStringtoArray($line)['ip'];
		
		for (; $ip != $currentIP; ) {
			// print('line:' . $line);
			$line = fgets($fp);
			if ($line == '') {
				break;
			}
			$ip = self::detailStringtoArray($line)['ip'];
		}
		
		// 如果已经到了文件结尾
		if (feof($fp)) {
			fclose($fp);
			$content = self::addAnonymous($file, Array('ip' => $currentIP));
		} else if ($currentIP == $ip) {
			$content = self::detailStringtoArray($line);
			fclose($fp);
		}
		$_SESSION['currentUser'] = $content;
		
	}
	
	// 生成注册登录列表
	public static function generateRegisterandLoginList($uri) {
		$returnArray = Array();
		$listRegister = '<li><a href="?register" title="register">想签定契约</a></li>';
		$listLogin = '<li><a href="?login" title="login">想传更大文件</a></li>';
		$listLogout = '<li><a href="?userinfo" title="userinfo">' . $_SESSION['currentUser']['id'] . '</a></li>
				<li><a href="?logout" title="logout">注销</a></li>';
		// 是否登录
		if ($_SESSION['currentUser']['anonymous'] == '0') {
			$returnArray['%userinfo%'] = $listLogout;
			return $returnArray;
		} else {
			if ($uri == 'login') {
				$returnArray['%userinfo%'] = $listRegister;
				return $returnArray;
			} else if ($uri == 'register') {
				$returnArray['%userinfo%'] = $listLogin;
				return $returnArray;
			}
		}
		
		$returnArray['%userinfo%'] = $listRegister . "\n" . $listLogin;
		return $returnArray;
	}
	
	// 生成用户信息
	public static function getUserInfo() {
		$returnArray = Array();
		
		$returnArray['%id%'] = $_SESSION['currentUser']['id'];
		$returnArray['%username%'] = $_SESSION['currentUser']['username'];
		return $returnArray;
	}
	
	// 更新用户资料
	public static function updateUserInfo($file, $content) {
		$returnArray = Array(
			'api' => 'userinfo',
		);
		
		if (isset($content['error'])) {
			$returnArray['result'] = 'modify fail';
			$returnArray['error'] = $content['error'];
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}
		
		self::updateData($file, $content);
		$_SESSION['currentUser'] = $content;
		$returnArray['result'] = 'modify success';
		return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
	}
}

?>