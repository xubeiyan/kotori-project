var confirm = document.getElementById('confirm'),
	r18select = document.getElementsByClassName('r18select'),
	errmsg = document.getElementById('errmsg'),
	xhr = new XMLHttpRequest(),
	form = new FormData();

for (var i = 0; i < r18select.length; ++i) {
	// console.log('listener added...');
	r18select[i].addEventListener('change', function () {
		console.log('add ' + this.id + ' with status ' + this.value);
		form.append(this.id, this.value);
	});
}
	
confirm.addEventListener('click', function () {

	xhr.open('POST', '?managepost', true);
	xhr.setRequestHeader('Kotori-Request', 'ManageUpdate');
	xhr.send(form);
})

xhr.onreadystatechange = function () {
	if (xhr.readyState == 4 && xhr.status == 200) {
		errmsg.innerText = '';
		var resp = JSON.parse(xhr.responseText);
		// console.log(resp);
		if (resp['result'] == 'success') {
			for (var i = 0; i < resp['details'].length; ++i) {
				errmsg.innerHTML += resp['details'][i] + '<br />';
			}
		} else if (resp['result'] == 'fail') {
			errmsg.innerText = 'seems no file to change status';
		} else if (resp['result'] == 'set status fail') {
			errmsg.innerText = resp['error'];
		}
	}
}
	