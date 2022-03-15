# README for PHP Version

#### 目录构成

* `index.php` 入口文件
* `lib/Image.php` 图片类Image
* `lib/User.php` 用户类User
* `lib/Util.php` 杂项类Util
* `config/conf.php` 配置文件
* `uploads/` 图文件目录
* `thumbs/` 缓存图目录
* `templates/default/*` default主题的模板
* `data/userdata` 用户数据
* `data/imagedata` 图片数据

#### 用户数据字段划分

这部分写入userdata表中    

* id(自增，从1开始)
* username(用户名，没有的话以创建时间生成一个)
* password(没有的话为空，加密算法为sha1)
* ip(ipv4和ipv6视为两个用户)
* anonymous(是否匿名，0为否，1为是)

#### 图片数据字段划分

这部分写入imagedata表中

* id(自增，从1开始)
* size(图片大小)
* filename(文件名)
* uploader(上传用户id)
* uploadtime(上传时间)
* nsfw(not safe for work)

#### 统计数据字段划分

这部分写入statistics表中

* id
* name(目前有image和user这两项)
* value(对应的值)

#### 路由

>暂时先想到这么多    
>目前暂时未使用目录重写(Rewrite)规则，主要考虑到Apache和nginx实现方式有区别……    

###### GET方法

* `/` 跳转到`/upload`
* `/upload` 上传图片
* `/random` 随机访问个图片
* `/register` 注册新账户
* `/login` 登录
* `/logout` 注销登录
* `/userinfo` 用户信息
* `/list` 列出图片
* `/userupload` 查看用户上传的图片
* `/manage` 管理页面


###### POST方法

* `/uploadpost` 上传文件     
	上传成功
	```javascript
	{
		"api": "upload",
		"result": "upload success",
		"savePath": "11223344.jpg"
	}
	```
	上传失败
	```javascript
	{
		"api": "upload",
		"result": "upload fail",
		"error": "xxxxx"
	}
	```
* `/registerpost` 注册用户

	注册成功    
	```javascript
	{
		"api": "register",
		"result": "register success",
	}
	```
	注册失败（用户存在/此用户为管理员账户）    
	```javascript
	{
		"api": "register",
		"result": "register fail",
		"error": "user exits/it is admin user",
	}
	```
* `/loginpost` 登录
    
	登录成功    
	```javascript
	{
		"api": "login",
		"result": "login success",
	}
	```
	登录失败（密码错误/无此用户）    
	```javascript
	{
		"api": "login",
		"result": "login fail",
		"error": "password wrong/no user",
	}
	```
* `/userinfopost` 修改用户信息   

	用户信息修改成功    
	```javascript
	{
		"api": "userinfo",
		"result": "modify success",
	}
	```
	用户信息修改失败（id不匹配/用户名不匹配/旧密码不匹配）    
	```javascript
	{
		"api": "userinfo",
		"result": "modify fail",
		"detail": "the id/username/password seems not match...",
	}
	```
* `/managepost` 修改管理信息
	```javascript
	{
		"api": "manageinfo",
		"result": "success/fail",
		"detail": ""
	}
	```
