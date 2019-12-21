var username = document.getElementById('username'),
	password = document.getElementById('password'),
	loginButton = document.getElementById('login-button'),
	registerButton = document.getElementById('register-button'),
	errmsg = document.getElementById('error-msg'),
	xhr = new XMLHttpRequest(),
	form = new FormData(),
	beEmpty = function (word) {
		errmsg.style.display = 'block';
		errmsg.innerText = `${word} can not be empty`;
	},
	loginPost =	function () {
		var un = username.value,
			pw = password.value;
			
		// 检查用户名
		if (un == '') {
			console.log('用户名不能为空');
			beEmpty('Username');
			return;
		}
		
		// 检查密码
		if (pw == '') {
			console.log('密码不能为空');
			beEmpty('Password');
			return;
		}
		
		errmsg.innerText = '';
		form.append('username', un);
		form.append('password', pw);
		
		xhr.open('POST', '?loginpost', true);
		xhr.setRequestHeader('Kotori-Request', 'Login');
		xhr.send(form);
	},
	register = function () {
		window.location.href = '?register';
	};
	
window.addEventListener("keypress", function (e) {
	if (e.keyCode == 13) {
		loginPost();
	}
});
	
loginButton.addEventListener("click", loginPost);
registerButton.addEventListener('click', register);
xhr.onreadystatechange = function () {
	if (xhr.readyState == 4 && xhr.status == 200) {
		var resp = JSON.parse(xhr.responseText);
		// console.log(resp);
		if (resp['result'] == 'login success') {
			window.location.href = "?upload";
		} else if (resp['result'] == 'login fail'){
			if (resp['error'] == 'no user') {
				console.log('该用户不存在');
				errmsg.innerText = 'not exist such user';
			} else if (resp['error'] == 'password wrong') {
				console.log('密码错误');
				errmsg.innerText = 'invalid password';
			}
		}
	}
}