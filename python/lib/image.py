# coding: utf-8

'''
图像处理类
'''


class image:
	@staticmethod
	def upload_file(file, destination, temp_folder):
		# 将其保存到临时文件夹
		from util import util
		import os
		tmp_filename = util.random_filename() + '.tmp'
		full_tmp_filename = os.path.join(temp_folder, tmp_filename)
		file.save(full_tmp_filename)
		file_real_type = image.file_type(full_tmp_filename)
		if file_real_type != 'NOT SUPPORT':
			import shutil
			new_full_filename = util.random_filename() + '.' + file_real_type.lower()
			new_dst = destination + '/' + new_full_filename
			shutil.move(full_tmp_filename, new_dst)
			return util.success('upload', new_dst)
		else:
			os.remove(full_tmp_filename)
			return util.error('post_format_error')
			
		
	# 文件类型检测
	@staticmethod
	def file_type(file):
		type_str = ''
		with open(file, 'rb') as f:
			# 读取前两字节
			type_str = [hex(ord(x)) for x in f.read(2)]
			
		if 'app' in vars():
			allow_extensions = app.config['UPLOAD_ALLOW_EXTENSION']
		else:
			allow_extensions = ['png', 'gif', 'jpg', 'webp']
			
		# webp	
		if 'webp' in allow_extensions and \
			type_str[0] == '0x52' and type_str[1] == '0x49':
			return 'WEBP'
		# jpeg
		elif 'jpg' in allow_extensions and \
			type_str[0] == '0xff' and type_str[1] == '0xd8':
			return 'JPG'
		# png
		elif 'png' in allow_extensions and \
			type_str[0] == '0x89' and type_str[1] == '0x50':
			return 'PNG'
		# gif
		elif 'gif' in allow_extensions and \
			type_str[0] == '0x47' and type_str[1] == '0x49':
			return 'GIF'
		else:
			return 'NOT SUPPORT'
			
	# 写入记录到数据库中
	@staticmethod
	def add_db_record(filename):
		sql = 'INSERT INTO'