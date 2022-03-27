var username = document.getElementById('username'),
	password = document.getElementById('password'),
	loginButton = document.getElementById('login-button'),
	clearButton = document.getElementById('clear-button'),
	errmsg = document.getElementById('error-msg'),
	xhr = new XMLHttpRequest(),
	form = new FormData(),
	showErrMsg = function (msg) {
		errmsg.style.display = 'block';
		errmsg.innerText = msg;
	};
const loginPost = () => {
	var un = username.value,
		pw = password.value;
		
	// 检查用户名
	if (un == '') {
		console.log('用户名不能为空');
		showErrMsg('Username can not be empty');
		return;
	}
	
	// 检查密码
	if (pw == '') {
		console.log('密码不能为空');
		showErrMsg('Password can not be empty');
		return;
	}
	
	errmsg.innerText = '';
	form.append('username', un);
	form.append('password', pw);
	
	xhr.open('POST', '?loginpost', true);
	xhr.setRequestHeader('Kotori-Request', 'Login');
	xhr.send(form);
};

// 清除所有的数据
clearButton.addEventListener('click', () => {
	username.value = '';
	password.value = '';
});
	
window.addEventListener("keypress", function (e) {
	// console.log(e);
	if (e.key == 'Enter') {
		loginButton.click();
	}
});
	
loginButton.addEventListener("click", loginPost);

xhr.onreadystatechange = function () {
	if (xhr.readyState == 4 && xhr.status == 200) {
		var resp = JSON.parse(xhr.responseText);
		// console.log(resp);
		if (resp['result'] == 'login success') {
			window.location.href = "?upload";
		} else if (resp['result'] == 'login fail'){
			if (resp['error'] == 'no user') {
				console.warn('该用户不存在');
				showErrMsg('not exist such user');
			} else if (resp['error'] == 'password wrong') {
				console.warn('密码错误');
				showErrMsg('invalid password');
			}
		}
	}
}