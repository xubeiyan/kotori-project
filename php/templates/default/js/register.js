var username = document.getElementById('username'),
	password = document.getElementById('password'),
	passwordConfirm = document.getElementById('password-confirm'),
	registerButton = document.getElementById('register-button'),
	errmsg = document.getElementById('error-msg'),
	xhr = new XMLHttpRequest(),
	form = new FormData();
	
registerButton.addEventListener("click", function() {
	var un = username.value,
		pw = password.value,
		pc = passwordConfirm.value;
	
	// 检查用户名长度
	if (un.length > 20 || un.length < 1) {
		console.log('用户名长度为1~20');
		errmsg.innerText = 'Length of username must to be 1 to 20';
		return;
	} else {
		// 是否格式正确
		var pattr = new RegExp("^[a-zA-Z0-9]+$");
		if(!pattr.test(un)) {
			console.log('不符合大小写字母及数字的用户名');
			errmsg.innerText = 'Username characters must to be a-z, A-Z and 0-9';
			return;
		}
	}
	
	// 检查密码是否填写
	if (pw == '' || pc == '') {
		console.log('密码或者不能为空');
		errmsg.innerText = 'Password or Confirmation is empty';
		return;
	}
	
	// 检查两次密码一致性	
	if (pw !== pc) {
		console.log('两次密码不一样！');
		errmsg.innerText = 'Password and Confirmation is not same';
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
		console.log(xhr.responseText);
	}
}
	