<?php
/**
* User类
*/

class User {
	
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
			// TODO: to json
			die('username or password is not set!');
		}
		
		global $config;
		
		// admin登录
		if ($content['username'] == $config['user']['adminUserName']) {
			if ($content['password'] == $config['user']['adminPassword']) {
				$returnArray = Array(
					'api' => 'adminLogin',
					'result' => 'login success',
				);
				$content['id'] = 0;
				$content['anonymous'] = 0;
				$content['admin'] = 'kotori';
				$_SESSION['currentUser'] = $content;
				
				return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
			} else {
				$returnArray = Array(
					'api' => 'adminLogin',
					'result' => 'login fail',
					'error' => 'password wrong',
				);
				
				return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
			}
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
		
		// 不允许注册管理员重名的账号
		global $config;
		if ($content['username'] == $config['user']['adminUserName']) {
			$returnArray['result'] = 'register fail';
			$returnArray['error'] = 'it is admin user';
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}
	
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
	public static function generateAnonymous() {
		// 使用kotori+当前时间戳的md5的前20位作为用户名
		$content['username'] = substr(md5('kotori' + time()), 0, 20);
		$content['password'] = sha1('kotori');
		return $content;
	}
	
	// 检查分配SESSION
	public static function sessionCheck($currentIP) {
		// 如果$_SESSION['current']有，则什么都不做
		if (isset($_SESSION['currentUser'])) {
			return;
		}
		
		// 从无到有
		global $config;
		
		$db = DB::database();
		$userTable = $config['database']['userTableName'];
		// 查询对应的IP是否有用户
		$stmt = $db ->prepare("SELECT `id`, `username` FROM $userTable WHERE `ip` = :ip LIMIT 1");
		$stmt ->bindParam(':ip', $currentIP);
		$result = $stmt ->execute();
		$row = $result ->fetchArray();

		// 没有则增加
		if (!$row) {
			$content = self::generateAnonymous();
			$username = $content['username'];
			$insert_sql = sprintf('INSERT INTO `%s` (`username`, `password`, `ip`, `anonymous`) 
				VALUES ("%s", "%s", "%s", 1)',
				$userTable, $content['username'], $content['password'], $currentIP);
			$db ->exec($insert_sql); 
			$id = $db ->lastInsertRowID();
		} else {
			$username = $row['username'];
			$id = $row['id'];
		}
		$_SESSION['currentUser']['id'] = $id;
		$_SESSION['currentUser']['anonymous'] = 1;
		$_SESSION['currentUser']['username'] = $username;
		$_SESSION['currentUser']['ip'] = $currentIP;

	}
	
	/** 
	* 生成header栏的右端部分
	*/
	public static function generateRegisterandLoginList() {
		$returnArray = Array();
		global $config;
		// 如果匿名用户
		if ($_SESSION['currentUser']['anonymous'] == 1) {
			$templateFile = sprintf('templates/%s/header_uinf_anonymous.html', 
			$config['site']['templateName']);
		// 如果管理员
		} else if (isset($_SESSION['currentUser']['admin'])) {
			$templateFile = sprintf('templates/%s/header_uinf_admin.html', 
			$config['site']['templateName']);
		// 如果一般用户
		} else {
			
		}
		// $username = '<span class="admin">KOTORI</span>' : $_SESSION['currentUser']['username'];
		
		/* $listLogout = '<a href="?userinfo" title="userinfo"><li class="right username">' . $username . '</li></a>
				<a href="?userupload" title="userupload"><li class="right">我上传的</li></a>
				<a href="?logout" title="logout"><li class="right quit">注销</li></a>';
				*/
		// 是否登录，等了后只渲染登出按钮
		$returnStr = file_get_contents($templateFile);
		return $returnStr;
	}
	
	// 生成用户信息
	public static function getUserInfo() {
		$returnArray = Array();
		
		$returnArray['id'] = $_SESSION['currentUser']['id'];
		$returnArray['username'] = $_SESSION['currentUser']['username'];
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
	
	// 清除所有用户信息（跑路用
	public static function clearAllUserData($file) {
		$fp = fopen($file, 'r+');
		# 读取前面的#行
		$temp = '';
		for ($line = fgets($fp); $line[0] == '#'; $line = fgets($fp)) {
			$temp .= $line;
		}
		fclose($fp);
		unlink($file);
		file_put_contents($file, $temp);
		// echo 'all user data cleared!<br>';
	}
}

?>