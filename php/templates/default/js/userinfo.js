var id = document.getElementById('id'),
	username = document.getElementById('username'),
	oldpass = document.getElementById('oldpass'),
	newpass = document.getElementById('newpass'),
	confirm = document.getElementById('confirm'),
	loginButton = document.getElementById('login-button'),
	errmsg = document.getElementById('error-msg');
	
loginButton.addEventListener('click', function () {
	errmsg.innerText = '还没准备好！';
});
	
	