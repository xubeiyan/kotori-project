var confirm = document.getElementById('confirm'),
	r18select = document.getElementsByClassName('r18select'),
	xhr = new XMLHttpRequest(),
	form = new FormData();

for (var i = 0; i < r18select.length; ++i) {
	// console.log('listener added...');
	r18select[i].addEventListener('change', function () {
		console.log(this.value);
		form.append(this.id, this.value);
	});
}
	
confirm.addEventListener('click', function () {

	xhr.open('POST', '?managepost', true);
	xhr.setRequestHeader('Kotori-Request', 'ManageUpdate');
	xhr.send(form);
})