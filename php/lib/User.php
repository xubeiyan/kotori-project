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
	public static function login($userInfo) {
		$db = DB::database();
		// 返回
		$returnArray = Array(
			'api' => 'login',
		);

		global $config;
		// 检查是否管理员
		if ($userInfo['username'] == $config['user']['adminUserName']) { 
			if ($userInfo['password'] == $config['user']['adminPassword']) {
				// session增加内容
				$_SESSION['currentUser'] = Array(
					'id' => 0,
					'anonymous' => 0,
					'admin'	=> 1,
					'username'	=> $userInfo['username'],
				);

				$returnArray['result'] = 'login success';
				return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
			}
			// 密码错误
			$returnArray['result'] = 'login fail';
			$returnArray['error'] = 'password wrong';
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}

		// 一般用户检查
		$userTable = $config['database']['userTableName'];
		$stmt = $db ->prepare("SELECT `id`, `password` FROM $userTable 
			WHERE `username` = :username LIMIT 1");
		$stmt ->bindParam(':username', $userInfo['username']);
		$result = $stmt ->execute();
		$row = $result ->fetchArray(SQLITE3_ASSOC);
		// 结果为空
		if (!$row) {
			$returnArray['result'] = 'login fail';
			$returnArray['error'] = 'no user';
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}
		
		// 查询到结果
		if (!password_verify($userInfo['password'], $row['password'])) {
			$returnArray['result'] = 'login fail';
			$returnArray['error'] = 'password wrong';
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}

		
		// session增加内容
		$_SESSION['currentUser'] = Array(
			'id' => $row['id'],
			'anonymous' => 0,
			'username'	=> $userInfo['username'],
		);

		$returnArray['result'] = 'login success';
		return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
	}
	
	// 增加新用户信息
	public static function addUser($userInfo) {
		$db = DB::database();
		// 返回的JSON数组
		$returnArray = Array(
			'api' => 'register',
		);

		global $config;
		// 检查是否是管理员用户名
		if ($userInfo['username'] == $config['user']['adminUserName']) {
			$returnArray['result'] = 'register fail';
			$returnArray['error'] = 'it is admin user';
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}

		$userTable = $config['database']['userTableName'];
		// 检查是否已经存在用户
		$stmt = $db -> prepare("SELECT `id` FROM $userTable WHERE `username` = :username LIMIT 1");
		$stmt ->bindParam(':username', $userInfo['username']);

		$ret = $stmt ->execute();
		$row = $ret ->fetchArray(SQLITE3_ASSOC);
	
		if ($row) {
			$returnArray['result'] = 'register fail';
			$returnArray['error'] = 'user exists';
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}

		$stmt = $db -> prepare("INSERT INTO $userTable 
			(`username`, `password`, `ip`, `anonymous`) VALUES 
			(:username, :pass, :ip, 0)");
		$stmt ->bindParam(':username', $userInfo['username']);
		$hash = password_hash($userInfo['password'], PASSWORD_DEFAULT);
		$stmt ->bindParam(':pass', $hash);
		$stmt ->bindParam(':ip', $userInfo['ip']);

		$stmt ->execute() -> finalize();
		if ($db ->lastErrorMsg() != 'not an error') {
			$returnArray['result'] = 'reigster fail';
			$returnArray['error'] = $db ->lastErrorMsg();
			return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
		}

		// session增加内容
		$_SESSION['currentUser'] = Array(
			'id' => $db -> lastInsertRowID(),
			'anonymous' => 0,
			'username'	=> $userInfo['username'],
		);

		$returnArray['result'] = 'register success';
		return json_encode($returnArray, JSON_UNESCAPED_UNICODE);
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
			$templateFile = sprintf('templates/%s/header_uinf_user.html', 
			$config['site']['templateName']);
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