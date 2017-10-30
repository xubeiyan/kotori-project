# coding: utf-8

from flask import Flask, redirect, url_for
app = Flask(__name__)

@app.route('/', methods = ['GET'])
def index():
	return redirect(url_for('file_upload'))
	
# 上传文件	
@app.route('/upload', methods = ['GET'])
def file_upload():
	return 'upload...'
	
# 随机访问
@app.route('/random', methods = ['GET'])
def random_visit():
	return 'random...'
	
# 注册
@app.route('/register', methods = ['GET'])
def register():
	return 'register...'
	
# 登录
@app.route('/login', methods = ['GET'])
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
@app.route('/list/<page>', methods = ['GET'])
def list(page):
	if page == 'last':
		return 'visit last page'
	else:
		return 'visit page ' + page
	
# 管理
@app.route('/manage', methods = ['GET'])
def manage():
	return 'manage...'

if __name__ == '__main__':
	app.debug = True
	app.run()