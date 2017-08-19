var id = document.getElementById('id'),
	username = document.getElementById('username'),
	oldpass = document.getElementById('oldpass'),
	newpass = document.getElementById('newpass'),
	confirm = document.getElementById('confirm'),
	loginButton = document.getElementById('login-button'),
	errmsg = document.getElementById('error-msg'),
	xhr = new XMLHttpRequest(),
	form = new FormData();
	
loginButton.addEventListener('click', function () {
	if (oldpass.value == '') {
		console.log('请填写旧密码');
		errmsg.innerText = 'Old password is required!';
		return;
	}
	
	if (newpass.value == '') {
		console.log('请填写新密码');
		errmsg.innerText = 'New password is required!';
		return;
	}
	
	if (confirm.value == '') {
		console.log('请填写新密码确认');
		errmsg.innerText = 'Confirmation is required!';
		return;
	}
	
	if (confirm.value != newpass.value) {
		console.log('新密码和确认新密码不一致');
		errmsg.innerText = 'New password and confirmation are not the same!';
		return;
	}
	
	errmsg.innerText = '';
	
	form.append('userid', id.value);
	form.append('username', username.value);
	form.append('oldpass', oldpass.value);
	form.append('newpass', newpass.value);
	
	xhr.open('POST', '?userinfopost', true);
	xhr.setRequestHeader('Kotori-Request', 'UserInformationUpdate');
	xhr.send(form);
});

xhr.onreadystatechange = function () {
	if (xhr.readyState == 4 && xhr.status == 200) {
		var resp = JSON.parse(xhr.responseText);
		console.log(resp);
		if (resp['api'] == 'modify success') {
			// window.location.href = "?upload";
		} else {
			errmsg.innerText = '...';
		}
	}
}
	
	