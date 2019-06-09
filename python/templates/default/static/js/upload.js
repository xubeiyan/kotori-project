'use strict'

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
	area.className = "little";
	upload.style.display = "block";
	imgObj = fileList[0];
});

upload.addEventListener("click", function () {
	var xhr = new XMLHttpRequest(),
		formData = new FormData(),
		preview = document.getElementById("preview"),
		progress = preview.appendChild(document.createElement("p"));
		
	// 隐藏上传按钮
	uploadButton.style.display = 'none';
	
	progress.appendChild(document.createTextNode("上传中"));
	progress.id = "progress";	
		
	xhr.upload.addEventListener("progress", function(e) {
		var pc = parseInt(100 - (e.loaded / e.total * 100));
		if (e.lengthComputable) {
			progress.style.backgroundPosition = pc + "% 0";
			progress.innerText = progress.innerText.split(' ')[0] + ' ' + e.loaded + '/' + e.total;
			console.log(e.loaded + '/' + e.total);
		}
	}, false);
		
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && xhr.status == 200) {
			var responseArray = JSON.parse(xhr.responseText),
				folder = preview.appendChild(document.createElement("p"));
				
			if (responseArray['result'] == 'success') {
				console.log(responseArray);
				progress.className = "success";
				progress.innerHTML = "上传成功";
				
				folder.innerHTML = '上传路径: <a class="upload" href="' + responseArray['add_info']['saved_path'] + '">' + responseArray['add_info']['saved_path'] + '</a>';
			} else if (responseArray['result'] == 'upload fail'){
				progress.className = "failed";
				progress.innerHTML = "上传失败";

				folder.innerHTML = '出错原因:' + responseArray['error'];
			}
		}
	}
	
	xhr.open('POST', 'upload', true);
	xhr.setRequestHeader('Kotori-Request', 'FileUpload');
	
	formData.append('img', imgObj);
	console.log('start send');
	xhr.send(formData);
	
});
