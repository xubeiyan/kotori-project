var id = document.getElementById('id'),
	username = document.getElementById('username'),
	oldpass = document.getElementById('oldpass'),
	newpass = document.getElementById('newpass'),
	confirm = document.getElementById('confirm'),
	loginButton = document.getElementById('register-button-s'),
	errmsg = document.getElementById('error-msg'),
	xhr = new XMLHttpRequest(),
	form = new FormData(),
	showErrMsg = function (msg) {
		errmsg.style.display = 'block';
		errmsg.innerText = msg;
	},
	showOKMsg = function (msg) {
		errmsg.style.display = 'block';
		errmsg.style.border = '1px solid #0C0';
		errmsg.innerText = msg;
	};
	
loginButton.addEventListener('click', function () {
	if (oldpass.value == '') {
		console.log('请填写旧密码');
		showErrMsg('Old password is required!');
		return;
	}
	
	if (newpass.value == '') {
		console.log('请填写新密码');
		showErrMsg('New password is required!');
		return;
	}
	
	if (confirm.value == '') {
		console.log('请填写新密码确认');
		showErrMsg('Confirmation is required!');
		return;
	}
	
	if (confirm.value != newpass.value) {
		console.log('新密码和确认新密码不一致');
		showErrMsg('New password and confirmation are not the same!');
		return;
	}
	
	errmsg.innerText = '';

	form.append('userid', id.value.split(' ')[2]);
	form.append('username', username.value.split(' ')[1]);
	form.append('oldpass', oldpass.value);
	form.append('newpass', newpass.value);
	
	xhr.open('POST', '?userinfopost', true);
	xhr.setRequestHeader('Kotori-Request', 'UserInformationUpdate');
	xhr.send(form);
});

xhr.onreadystatechange = function () {
	if (xhr.readyState == 4 && xhr.status == 200) {
		var resp = JSON.parse(xhr.responseText);
		// console.log(resp);
		if (resp['result'] == 'modify success') {
			showOKMsg('user info modifies success');
		} else if (resp['result'] == 'modify fail') {
			showErrMsg(resp['error']);
		}
	}
}
	
	