# Kotori-Project

### 写在前面

>我决定又开坑了（喂），这次是一个图床（啪）……    
>写这个的原因是没有一个好用的图床啊……不要跟我提新浪的，详情请见下一段……github那种方法还是饶了我把……  

#### 关于新浪图床的问题

>有个重要的问题就是不匿名！    
>虽然看起来是匿名的，但根据如此方法即可得到上传者的UID……    
例如http://wx3.sinaimg.cn/mw690/78f2cc43ly1fj8mi593ilj206y09vmxc.jpg ，取图片编码的前8位78f2cc43以16进制转换成10进制，就是上传者的UID了……    
>如果前面的数字是以00开头的则使用62进制转换为10进制http://wx1.sinaimg.cn/mw690/006r2HqOgy1fj7dxg3zuxj30p02a1wry.jpg
  
>好图床应该具有以下几个特点：    
>* 上传图简单，github上传很麻烦，新浪还好，但是需要一个小号才能发出(现在新浪需要实名认证，完蛋)，忘了说最重要的一点，就是后者不能发R18，而且会打上莫名奇妙的水印，这是一个好图床不具备的，至于那些在墙外的google等等，就更谈不上竞争力了    
>* 有权限控制，最开始觉得这些都是很麻烦的，借鉴一下匿名版的思想，传图是无需注册或者说是匿名的，但是可以增加黑名单和白名单，让某些IP无法传图（喂），也可以增加需要登录的部分，登录之后能传不让大家看的图（喂），至于图是什么内容可以发挥你的想象力    
>* 稳定，稳定才是王道，不能像某些免费图床一样，今天能用，明天就跪了；前面也说到g+的相册，但是保不好哪天就被墙了（或者已经被墙了233），另外能支持cdn当然最好，但是我不会配置cdn啊~   
 
>kotori当然是小鸟的意思啦~图床写好之后我马上粘个图片到这儿(done)    
>![kotori](http://imghost.chenhai.net/uploads/c8f74e2c57d9abc3d6892cf08415f228.jpg)    

### 准备实现功能

>其实[tmp.is](http://tmp.is)这个站已经很接近我的想法了，可惜作者好像不知道干啥去了，现在处于传不上图片的状态233    

* 匿名传图，初步支持jpg, png, webp（看起来要php5.5以上，不过应该都没有用5.3或者5.2的吧）, gif（gif压缩这个php的GD库还是有只会留取1帧的问题待解决）
* 支持登录传图，支持更大的图（好像没什么用），支持不显示在略缩图中（你懂的），还有就是匿名用户是看不到的图（喂）

### 功能划分
>先写个php版本吧（后续的之后再说），看向kagari匿名版    
>完全是静态文件的玩意，用不用数据库呢（试着不用一次试试    
>说着要加上读取文件的锁，结果也没加……    

* `index.php` 入口文件
* `lib/Image.php` 图片类Image
* `lib/User.php` 用户类User
* `lib/Util.php` 杂项类Util
* `config/conf.php` 配置文件
* `uploads/` 图文件目录
* `thumbs/` 缓存图目录
* `templates/default/uploadFile.html` 上传文件
* `templates/default/random.html` 随机访问
* `templates/default/login.html` 登录
* `templates/default/register.html` 注册
* `templates/default/userinfo.html` 用户信息
* `templates/default/list.html` 图片列表 
* `templates/default/error.html` 错误页面
* `data/userdata` 用户数据
* `data/imagedata` 图片数据

#### 用户数据字段划分

这部分写入userdata文件中    

* id(自增，从1开始)
* username(用户名，没有的话以创建时间生成一个)
* password(没有的话为空，加密算法为sha1)
* ip(ipv4和ipv6视为两个用户)
* anonymous(是否匿名，0为否，1为是)

#### 图片数据字段划分

这部分写入imagedata文件中

* id(估计是按照某个散列函数随机生成)
* size(图片大小)
* filename(文件名)
* uploader(上传用户id)
* uploadtime(上传时间)
* r18(咳咳咳，你懂的)

#### 路由

>暂时先想到这么多    

###### GET方法

* `/` 跳转到`/upload`
* `/upload` 上传图片
* `/random` 随机访问个图片
* `/register` 注册新账户
* `/login` 登录
* `/logout` 注销登录
* `/userinfo` 用户信息
* `/list` 列出图片
* `/manage` 管理页面


###### POST方法

* `/uploadpost`
```javascript
{
	"api": "upload",
	"result": "upload success/fail"
}
```
* `/registerpost`
```javascript
{
	"api": "register",
	"result": "register success",
}
```
```javascript
{
	"api": "register",
	"result": "register fail",
	"error": "user exits/it is admin user",
}
```
* `/loginpost`
```javascript
{
	"api": "login",
	"result": "login success",
}
```
```javascript
{
	"api": "login",
	"result": "login fail",
	"error": "password wrong/no user",
}
```
* `/userinfopost`
```javascript
{
	"api": "userinfo",
	"result": "modify success",
}
```
```javascript
{
	"api": "userinfo",
	"result": "modify fail",
	"detail": "the id/username/password seems not match...",
}
```
* `/managepost`
```javascript
{
	"api": "manageinfo",
	"result": "success/fail",
	"detail": ""
}
```

### 目前进展

* upload页面基本完成
* random页面基本完成
* list页面基本完成（list=[数字]这种可以跳转页面，已加上前后跳转页面按钮）    
* register和login页面基本完成（验证码？不想做这个啊~）
* userinfo页面基本完成（对，现在可以改了）
* manage页面基本完成了（manage=[数字]可以跳转页面，现在能够修改文件的可见性了）
* 已修改检测上传文件的信息而不是文件名判断是否是图片文件（  
* 已增加某智障的图片作为列表的cover图片        

### 遇到问题

* webp有些图会无法压缩，提示格式不对（估计是图片文件的格式有误

### 接下来想做的

* register需要验证码？但是要区分是否恶意注册那个又需要维护一个列表（不想弄啊
* list=last以及manage=last跳转到最后一页（不想弄啊
