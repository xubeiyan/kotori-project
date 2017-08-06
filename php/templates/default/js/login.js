var username = document.getElementById('username'),
	password = document.getElementById('password'),
	loginButton = document.getElementById('login-button'),
	errmsg = document.getElementById('error-msg'),
	xhr = new XMLHttpRequest(),
	form = new FormData();
	
loginButton.addEventListener("click", function() {
	var un = username.value,
		pw = password.value;
		
	// 检查用户名
	if (un == '') {
		console.log('请填写用户名');
		errmsg.innerText = 'Please input username';
		return;
	}
	
	// 检查密码
	if (pw == '') {
		console.log('请填写密码');
		errmsg.innerText = 'Please input password';
		return;
	}
	
	errmsg.innerText = '';
	form.append('username', un);
	form.append('password', pw);
	
	xhr.open('POST', '?loginpost', true);
	xhr.setRequestHeader('Kotori-Request', 'Login');
	xhr.send(form);
});

xhr.onreadystatechange = function () {
	if (xhr.readyState == 4 && xhr.status == 200) {
		var resp = JSON.parse(xhr.responseText);
		// console.log(resp);
		if (resp['info'] == 'right') {
			window.location.href = "?upload";
		} else if (resp['info'] == 'no user'){
			console.log('该用户不存在');
			errmsg.innerText = 'not exist such user';
		} else if (resp['info'] == 'password wrong') {
			console.log('密码错误');
			errmsg.innerText = 'invalid password';
		}
	}
}