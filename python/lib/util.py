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
		
	# 生成错误信息
	@staticmethod
	def error(name, type='json'):
		import json
		# 上传文件格式错误
		if name == 'post_format_error':
			return_dict = {
				"api": "upload",
				"result": "format error",
				"add_info": {
					"error_msg": "only support jpg, png and gif file"
				}
			}
		# 上传文件大小
		elif name == 'post_size_error':
			return_dict = {
				"api": "upload",
				"result": "size error",
				"add_info": {
					"error_msg": "logged user for 5M, anonymous for 2M"
				}
			}
			
		else:
			return_dict = {
				'api': name,
				'info': 'unknown api...'
			}
			
		return json.dumps(return_dict)
			
	# 生成成功信息
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
				