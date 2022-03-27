const username = document.getElementById('username');
const password = document.getElementById('password');
const passwordConfirm = document.getElementById('password-confirm');
const registerButton = document.getElementById('register-button-s');
const errmsg = document.getElementById('error-msg');
	
let	xhr = new XMLHttpRequest();
let	form = new FormData();
	
const showErrMsg = function (msg) {
	errmsg.style.display = 'block';
	errmsg.innerText = msg;
};
	
registerButton.addEventListener("click", function() {
	var un = username.value,
		pw = password.value,
		pc = passwordConfirm.value;
	
	// 检查用户名长度
	if (un.length > 20 || un.length < 1) {
		console.warn('用户名长度必须为1~20');
		showErrMsg('Length of username must to be 1 to 20');
		return;
	} 

	// 是否格式正确
	var pattr = new RegExp("^[a-zA-Z0-9]+$");
	if(!pattr.test(un)) {
		console.warn('不符合大小写字母及数字的用户名');
		showErrMsg('Username characters must to be a-z, A-Z and 0-9');
		return;
	}

	// 检查密码是否过长
	if (pc.length > 256 || pw.length > 256) {
		console.warn('密码过长超过了256');
		showErrMsg('Password too long than 256');
		return;
	}

	
	// 检查密码是否填写
	if (pw == '' || pc == '') {
		console.warn('密码或者确认密码不能为空');
		showErrMsg('Password or Confirmation is empty');
		return;
	}
	
	// 检查两次密码一致性	
	if (pw !== pc) {
		console.warn('两次密码不一样！');
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
			if (resp['error'] == 'user exists') {
				console.warn('该用户已存在');
				showErrMsg('user has existed');
			} else if (resp['error'] == 'it is admin user') {
				console.warn('很遗憾这是管理员账号');
				showErrMsg(`"${username.value}" is admin user`);
			} else if (resp['error'] == 'not allow to register') {
				console.warn('现在不能够注册');
				showErrMsg('not allow to register');
			}
		}
	}
}
	