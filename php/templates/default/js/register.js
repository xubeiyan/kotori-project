var username = document.getElementById('username'),
	password = document.getElementById('password'),
	passwordConfirm = document.getElementById('password-confirm'),
	registerButton = document.getElementById('register-button-s'),
	errmsg = document.getElementById('error-msg'),
	xhr = new XMLHttpRequest(),
	form = new FormData(),
	showErrMsg = function (msg) {
		errmsg.style.display = 'block';
		errmsg.innerText = msg;
	};
	
registerButton.addEventListener("click", function() {
	var un = username.value,
		pw = password.value,
		pc = passwordConfirm.value;
	
	// 检查用户名长度
	if (un.length > 20 || un.length < 1) {
		console.log('用户名长度为1~20');
		showErrMsg('Length of username must to be 1 to 20');
		return;
	} else {
		// 是否格式正确
		var pattr = new RegExp("^[a-zA-Z0-9]+$");
		if(!pattr.test(un)) {
			console.log('不符合大小写字母及数字的用户名');
			showErrMsg('Username characters must to be a-z, A-Z and 0-9');
			return;
		}
	}
	
	// 检查密码是否填写
	if (pw == '' || pc == '') {
		console.log('密码或者不能为空');
		showErrMsg('Password or Confirmation is empty');
		return;
	}
	
	// 检查两次密码一致性	
	if (pw !== pc) {
		console.log('两次密码不一样！');
		showErrMsg('Password and Confirmation is not same');
		return;
	}
	
	errmsg.innerText = '';
	form.append('username', un);
	form.append('password', pw);
	
	xhr.open('POST', '?registerpost', true);
	xhr.setRequestHeader('Kotori-Request', 'Register');
	xhr.send(form);
});
	
xhr.onreadystatechange = function () {
	if (xhr.readyState == 4 && xhr.status == 200) {
		var resp = JSON.parse(xhr.responseText);
		// console.log(resp);
		if (resp['result'] == 'register success') {
			window.location.href = "?upload";
		} else if (resp['result'] == 'register fail') {
			if (resp['error'] == 'user exits') {
				console.log('该用户已存在');
				showErrMsg('user has existed');
			} else if (resp['error'] == 'it is admin user') {
				console.log('很遗憾这是管理员账号');
				showErrMsg('"' + username.value + '" is admin user');
			} else if (resp['error'] == 'not allow to register') {
				console.log('现在不能够注册');
				showErrMsg('not allow to register');
			}
		}
	}
}
	