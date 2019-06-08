# coding: utf-8

'''
图像处理类
'''


class image:
	# 上传文件
	@staticmethod
	def upload_file(file, temp_folder, upload_folder, database):
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
			new_dst = upload_folder + '/' + new_full_filename
			shutil.move(full_tmp_filename, new_dst)
	
			# 插入数据库记录
			file_info = {
				'filename': new_full_filename,
				'filetype': file_real_type				
			}
			user = {'id': 1}
			image.add_db_record(file_info, user, database)
			add_info = {
				'saved_path': '/uploads?name=' + new_full_filename
			}
			return util.success('upload', add_info)
		else:
			os.remove(full_tmp_filename)
			return util.error('upload_image_format_error')
			
		
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
	def add_db_record(file, user, database):
		import sqlite3, time
		uid = user['id']
		filename = file['filename']
		filetype = file['filetype']
		timestamp = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
		sql = 'INSERT INTO image (filename, filetype, uploader, uploadtime, limits) VALUES ' + \
			'("%s", "%s", "%d", "%s", "%d")' % (filename, filetype, uid, timestamp, 0)
			
		conn = sqlite3.connect('db/' + database)
		c = conn.cursor()
		c.execute(sql)
		conn.commit()
		conn.close()
		return 'success'
	
	# 获取图片列表
	@staticmethod
	def get_image_list(page_num, per_page, database):
		import sqlite3
		page_num = page_num if page_num > 0 else 1;
		per_page = per_page if per_page > 0 and per_page < 50 else 20;
		
		conn = sqlite3.connect('db/' + database)
		c = conn.cursor()
		c.execute('SELECT filename, limits FROM image LIMIT ? OFFSET ?', [per_page, (page_num - 1) * per_page])
		list = c.fetchall();
		conn.commit()
		conn.close()
		return list
		
	@staticmethod
	def image_exist(filename):
		import os, shutil
		thumb_file = './thumbs/' + filename
		upload_file = './uploads/' + filename
		if os.path.exists(thumb_file):
			return True
		elif os.path.exists(upload_file):
			shutil.copyfile(upload_file, thumb_file)
			return True
		else:
			return False