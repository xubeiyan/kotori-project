# Kotori Project --- python version

### 安装需求

使用了`Flask`框架    
安装方法：`$ pip install Flask`    

### 调用接口标准

#### 上传文件
返回值(成功)：    
```javascript
{
	"api": "upload",
	"result": "success",
	"add_info": {
		"save_path": "uploads/bc9bd284f672e75bbc86a1d0aef09104.jpg"
	}
}
```

返回值(图片格式不符合)：    
```javascript
{
	"api": "upload",
	"result": "format error",
	"add_info": {
		"error_msg": "only support jpg, png and gif file"
	}
}
``` 

返回值(超过了规定大小)：    
```javascript
{
	"api": "upload",
	"result": "size error",
	"add_info": {
		"error_msg": "5MiB limit for logged user, 2MiB limit for anonymous"
	}
}
```

#### 用户登录
请求：    
```javascript
{
	"api": "login",
	"username": "abc",
	"password": "def"
}
```

返回值(登录成功)：    
```javascript
{
	"api": "login",
	"result": "success"
}
```

返回值(用户名不存在)：    
```javascript
{
	"api": "login",
	"result": "username error",
	"add_info": {
		"error_msg": "username not exist"
	}
}
```

返回值(密码错误)：    
```javascript
{
	"api": "login",
	"result": "password error",
	"add_info": {
		"error_msg": "password invalid"
	}
}
```

返回值(管理员登录失败)：    
```javascript
{
	"api": "login",
	"result": "admin login error",
	"add_info": {
		"error_msg": "username or password invalid",
		"attempts": "there are %d time(s) to attempt"
	}
}
```

返回值(IP被阻止)：    
```javascript
{
	"api": "login",
	"result": "IP blocked",
	"add_info": {
		"error_msg": "retry 1 hours later"
	}
}
```

#### 用户登出

请求：    
```javascript
{
	"api": "logout",
	"session": "1234567890"
}
```
返回值(登出成功)；    
```javascript
{
	"api": "logout",
	"result": "success"
}
```

返回值(未登录)；    
```javascript
{
	"api": "logout",
	"result": "not login",
	"add_info": {
		"error_msg": "session for user not login"
	}
}
```

#### 用户注册
请求:    
```javascript
{
	"api": "register",
	"username": "abc",
	"password": "def"
}
```

返回值(注册成功)：    
```javascript
{
	"api": "register",
	"result": "success"
}
```

返回值(用户名为空)：    
```javascript
{
	"api": "register",
	"result": "username error",
	"add_info": {
		"error_msg": "username empty"
	}
}
```

返回值(用户名中有不符合规定的字符)
```javascript
{
	"api": "register",
	"result": "username error",
	"add_info": {
		"error_msg": "it can only use a-z, A-Z and 0-9"
	}
}
```

返回值(用户名为管理员账号):    
```javascript
{
	"api": "register",
	"result": "username error",
	"add_info": {
		"err_msg": "it is the admin account"
	}
}
```

### 数据库设计
#### user表
* id int
* username varchar(64)
* password varchar(64)
* lastuploadid int
* lastuploadtime datetime
* authority int

#### image表
* id int
* filename varchar(128)
* uploader int
* limit int