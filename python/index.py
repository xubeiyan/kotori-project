# coding: utf-8
import os
import sqlite3
import time
# werkzeug
from werkzeug import secure_filename

# flask
from flask import Flask, redirect, url_for, request, session, g, render_template, send_file
app = Flask(__name__, static_folder='templates/default/static', template_folder='templates/default')

# 配置文件
default_settings_file = 'config/settings.py'
if not os.path.exists(default_settings_file):
	print('the settings file: ' + default_settings_file + ' not exist!')
	exit()
	
app.config.from_pyfile(default_settings_file)


# 一些自定义库
from lib.image import *
from lib.user import *
from lib.util import *

motto_list = util.gal_motto()

# GET方法
# 根目录
@app.route('/', methods = ['GET'])
def index():
	return redirect(url_for('file_upload'))
	
# 上传文件 GET为显示上传页面，POST为上传文件
@app.route('/upload', methods = ['GET', 'POST'])
def file_upload():
	if request.method == 'GET':
		motto = util.select_motto(motto_list)
		year = time.strftime("%Y", time.localtime())
		# print(motto.decode('utf-8').encode('gbk'))
		return render_template('uploadFile.html', motto=motto, current_year=year).encode('utf-8')
	elif request.method == 'POST':
		img_folder = app.config['UPLOAD_FOLDER']
		thumb_folder = app.config['THUMB_FOLDER']
		if not request.files['img']:
			return 'there not an image'

		# 某个检测header里有没有指定字段的
		if not app.config.__contains__('UPLOAD_STRING_CHECK') or \
			not app.config['UPLOAD_STRING_CHECK'] == request.headers.get('Kotori-Request'):
			return 'not pass the check'
			
		file = request.files['img']
		json_resp = image.upload_file(file, app.config['TEMP_FOLDER'], app.config['UPLOAD_FOLDER'], app.config['DATABASE'])
		return util.make_json_resp(json_resp)
		
# 随机访问
@app.route('/random', methods = ['GET'])
def random_visit():
	return 'random...'
	
# 注册 GET为显示注册页面，POST为注册
@app.route('/register', methods = ['GET', 'POST'])
def register():
	return 'register...'
	
# 登录 GET为显示登录页面，POST为登录
@app.route('/login', methods = ['GET', 'POST'])
def login():
	if request.method == 'GET':
		return render_template('login.html').encode('utf-8')
	elif request.method == 'POST':
		import json
		try:
			json_data = json.loads(request.data)
		except (ValueError, TypeError):
			print('1')
			return
			
		# user.login(request.data)
		return request.data
	
# 用户信息
@app.route('/userinfo', methods = ['GET'])
def userinfo():
	return 'userinfo...'

# 列出图片
@app.route('/list', methods = ['GET'])
def list():
	page = request.args.get('page', '') 
	page = page if page.isdigit() and page >= 1 else 1;
	motto = util.select_motto(motto_list)
	year = time.strftime("%Y", time.localtime())
	file_list = image.get_image_list(int(page), app.config['IMAGE_PER_PAGE'], app.config['DATABASE'])
	filename_list = [e[0] for e in file_list]
	return render_template('list.html', motto=motto, current_year=year, fl=filename_list).encode('utf-8');
	
# 管理
@app.route('/manage', methods = ['GET', 'POST'])
def manage():
	if request.method == 'GET':
		page = request.args.get('page', '')
		
		
	elif request.method == 'POST':
		return 'manage post'
		
# 图
@app.route('/uploads', methods = ['GET'])
def uploads():
	name = request.args.get('name')
	if name == None:
		name = 'not exist'
	if image.image_exist(name):
		return send_file('./uploads/' + name)
	else:
		return 'not found file with name:' + name	
		
# 略缩图
@app.route('/thumbs', methods = ['GET'])
def thumbs():
	name = request.args.get('name')
	if name == None:
		name = 'not exist'
	if image.image_exist(name):
		return send_file('./thumbs/' + name)
	else:
		return 'not found file with name:' + name
	
if __name__ == '__main__':
	app.run()