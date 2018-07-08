# coding: utf-8
'''
工具类
'''
class util:
	# 检查是否是允许的文件类型
	@staticmethod
	def allowed_file_ext(filename, allowed_file_exts):
		return '.' in filename and \
			filename.rsplit('.', 1)[1].lower() in allowed_file_exts
			
	# 生成随机的文件名
	@staticmethod
	def random_filename():
		import hashlib, time
		return hashlib.md5(str(time.time())).hexdigest()
		
	# 生成json返回
	@staticmethod
	def make_json_resp(json_resp):
		from flask import make_response
		resp = make_response(json_resp)
		resp.headers['Content-Type'] = 'application/json'
		return resp
		
	# 生成错误信息
	@staticmethod
	def error(name, extra_info = {}, type='json'):
		import json
		# 上传文件格式错误
		if name == 'upload_image_format_error':
			return_dict = {
				"api": "upload",
				"result": "image format error",
				"add_info": {
					"error_msg": "only support jpg, png, gif and webp file"
				}
			}
		# 上传文件大小超过了限制
		elif name == 'upload_size_error':
			return_dict = {
				"api": "upload",
				"result": "size error",
				"add_info": {
					"error_msg": "logged user for 5M, anonymous for 2M"
				}
			}
		# 上传API调用错误
		elif name == 'upload_post_format_error':
			return_dict = {
				"api": "upload",
				"result": "post format error",
				"add_info": {
					"error_msg": "seems not contain an image or not contain the auth string"
				}
			}
		# 用户不存在
		elif name == 'login_username_error':
			return_dict = {
				"api": "login",
				"result": "username error",
				"add_info": {
					"error_msg": "username not exist"
				}
			}
		# 密码错误	
		elif name == 'login_password_error':
			return_dict = {
				"api": "login",
				"result": "password error",
				"add_info": {
					"error_msg": "password invalid"
				}
			}
		# 管理员登录失败
		elif name == 'login_admin_login_error':
			attempts = extra_info['times'] 
			return_dict = {
				"api": "login",
				"result": "admin login error",
				"add_info": {
					"error_msg": "username or password invalid",
					"attempts": "there are " + attempts + " time(s) to attempt"
				}
			}
		# IP被阻止
		elif name == 'login_ip blocked':
			return_dict = {
				"api": "login",
				"result": "IP blocked",
				"add_info": {
					"error_msg": "retry 1 hours later"
				}
			}
		# 用户未登录调用登出
		elif name == 'logout_not_login':
			return_dict = {
				"api": "logout",
				"result": "not login",
				"add_info": {
					"error_msg": "session for user not login"
				}
			}
		# 注册用户名为空
		elif name == 'reigster_username_empty':
			return_dict = {
				"api": "register",
				"result": "username error",
				"add_info": {
					"error_msg": "username empty"
				}
			}
		# 注册用户名有不合规定的字符
		elif name == 'register_username_invalid':
			return_dict = {
				"api": "register",
				"result": "username error",
				"add_info": {
					"error_msg": "it can only use a-z, A-Z and 0-9"
				}
			}
		# 注册用户名为管理员
		elif name == 'register_username_admin':
			return_dict = {
				"api": "register",
				"result": "username error",
				"add_info": {
					"err_msg": "it is the admin account"
				}
			}
		else:
			return_dict = {
				'api': name,
				'info': 'unknown api...'
			}
			
		return json.dumps(return_dict)
			
	# 生成成功信息
	@staticmethod
	def success(name, add_info, type='json'):
		import json
		if name == 'upload':
			return_dict = {
				'api': 'upload',
				'result': 'success',
				'add_info': add_info
			}
		elif name == 'login':
			return_dict = {
				'api': 'login',
				'result': 'success'
			}
		elif name == 'register':
			return_dict = {
				'api': 'register',
				'result': 'success'
			}
		else:
			return_dict = {
				'info': 'unknown success info'
			}
			
		return json.dumps(return_dict)
				