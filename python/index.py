# coding: utf-8

# flask
from flask import Flask, redirect, url_for, request
app = Flask(__name__)

# 一些自定义库
import lib.image as Image
import lib.user as User
import lib.util as Util

# GET方法
# 根目录
@app.route('/', methods = ['GET'])
def index():
	return redirect(url_for('file_upload'))
	
# 上传文件 GET为显示上传页面，POST为上传文件
@app.route('/upload', methods = ['GET', 'POST'])
def file_upload():
	return 'upload...'
	
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
	return 'login...'
	
# 登出
@app.route('/logout', methods = ['GET'])
def logout():
	return 'logout...'
	
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
	app.debug = True
	app.run()