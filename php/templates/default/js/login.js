var username = document.getElementById('username'),
	oldpass = document.getElementById('oldpass'),
	newpass = document.getElementById('newpass'),
	passconfirm = document.getElementById('newpass-confirm'),
	loginButton = document.getElementById('login-button'),
	errmsg = document.getElementById('error-msg'),
	xhr = new XMLHttpRequest(),
	form = new FormData();