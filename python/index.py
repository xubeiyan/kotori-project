# coding: utf-8
import os
import sqlite3
# werkzeug
from werkzeug import secure_filename

# flask
from flask import Flask, redirect, url_for, request, session, g, render_template
app = Flask(__name__, static_folder='templates/default/static', template_folder='templates/default')

# 配置文件
default_settings_file = 'config/settings.py'
if not os.path.exists(default_settings_file):
	print('the settings file: ' + default_settings_file + ' not exist!')
	exit()
	
app.config.from_pyfile(default_settings_file)


# 一些自定义库
from lib.image import image
from lib.user import user
from lib.util import util

# GET方法
# 根目录
@app.route('/', methods = ['GET'])
def index():
	return redirect(url_for('file_upload'))
	
# 上传文件 GET为显示上传页面，POST为上传文件
@app.route('/upload', methods = ['GET', 'POST'])
def file_upload():
	if request.method == 'GET':
		# if session.has_key['login']:
			# temp['userinfo'] = '1'
			
		return render_template('uploadFile.html').encode('utf-8')
	elif request.method == 'POST':
		img_folder = app.config['UPLOAD_FOLDER']
		thumb_folder = app.config['THUMB_FOLDER']
		if not request.files['img']:
			return 'there not an image'
		# print request.headers.get('Kotori-Request')
		# 某个检测header里有没有指定字段的
		if not app.config.has_key('UPLOAD_STRING_CHECK') or \
			not app.config['UPLOAD_STRING_CHECK'] == request.headers.get('Kotori-Request'):
			return 'not pass the check'
			
		file = request.files['img']
		image.upload_file(file, app.config['UPLOAD_FOLDER'])
		
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
			print '1'
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
	if page == 'last':
		return 'visit last page'
	elif page.isdigit():
		return 'visit page ' + page
	else:
		return 'invalid page value "' + page + '"'
	
# 管理
@app.route('/manage', methods = ['GET', 'POST'])
def manage():
	if request.method == 'GET':
		page = request.args.get('page', '')
		
		
		if page == 'last':
			return 'manage last page'
		elif page.isdigit():
			return 'visit page ' + page
		else:
			return 'invalid page value "' + page + '"'
	elif request.method == 'POST':
		return 'manage post'
	
if __name__ == '__main__':
	app.run()