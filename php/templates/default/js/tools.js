var area = document.getElementById('area'),
	preview = document.getElementById('preview'),
	file = document.getElementById('file'),
	upload = document.getElementById('uploadButton'),
	imgObj = {};
	
area.addEventListener("dragleave", function(e) {
	e.preventDefault();
});
area.addEventListener("dragenter", function(e) {
	e.preventDefault();
});
area.addEventListener("dragover", function(e) {
	e.preventDefault();
});
	
area.addEventListener("drop", function (e) {
	e.preventDefault();
	var fileList = e.dataTransfer.files;
	
	if (fileList.length == 0) {
		return false;
	}
	
	if (fileList[0].type.indexOf('image') == -1) {
		console.log('拖放的不是图片...');
		return false;
	}
	
	var img = window.URL.createObjectURL(fileList[0]),
		filename = fileList[0].name,
		filesize = Math.floor((fileList[0].size) / 1024);
		
	if (filesize > 5000) {
		console.log('文件大小不能超过5000K！');
		return false;
	}
	
	var str = '<img id="uploadImg" src="' + img + '"><p>图片名称：' + filename + '</p>' +
			'<p>大小:' + filesize + 'KB</p>';
	preview.innerHTML = str;
	upload.style.display = "block";
	imgObj = fileList[0];
});

area.addEventListener("click", function () {
	file.click();
});

file.addEventListener("change", function (e) {
	var fileList = e.target.files;
	
	if (fileList.length == 0) {
		return false;
	}
	
	if (fileList[0].type.indexOf('image') == -1) {
		console.log('选择的不是图片...');
		return false;
	}
	
	var img = window.URL.createObjectURL(fileList[0]),
		filename = fileList[0].name,
		filesize = Math.floor((fileList[0].size) / 1024);
		
	if (filesize > 10240) {
		console.log('文件大小不能超过10M！');
		return false;
	}
	
	var str = '<img id="uploadImg" src="' + img + '"><p>图片名称：' + filename + '</p>' +
			'<p>大小:' + filesize + 'KB</p>';
	preview.innerHTML = str;
	upload.style.display = "block";
	imgObj = fileList[0];
});

upload.addEventListener("click", function () {
	var xhr = new XMLHttpRequest(),
		formData = new FormData();
		
	xhr.open('POST', '?uploadpost', true);
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	
	formData.append('img', imgObj);
	
	xhr.send(formData);
	
	xhr.onreadystatechange = function() {
		// 隐藏上传按钮
		uploadButton.style.display = 'none';
		
		if (xhr.readyState == 4 && xhr.status == 200) {
			var responseArray = JSON.parse(xhr.responseText);
			if (responseArray['status'] == 'success') {
				preview.innerHTML = '<p>上传成功！</p>' +
					'<p>上传路径:' + responseArray['savePath'] + '</p>';
			} else if (responseArray['status'] == 'fail'){
				preview.innerHTML = '<p>上传失败！</p>' +
					'<p>出错原因:' + responseArray['error'] + '</p>';
			}
		} else {
			preview.innerHTML = 'AJAX readyState:' + xhr.readyState + ' AJAX Status:' + xhr.status;
		}
	}
});
