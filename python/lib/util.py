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
			
	@staticmethod
	def random_filename():
		import hashlib, time
		return hashlib.md5(str(time.time())).hexdigest()