# coding: utf-8
'''
用户类
'''
class user:
	@staticmethod
	def get_cookie(session):
		if session.has_key('uid'):
			pass
		elif session.has_key('logged'):
			pass
		return session

	@staticmethod
	def register(info):
		username = info['username']
		sql = 'SELECT username FROM USER WHERE username="%s"' % username
	# 登录
	# info包含username和password两项
	# times包含limit（允许尝试次数）和
	@staticmethod
	def login(info, times):
		# 已超过尝试次数
		if times.limit < times.tries:
			pass