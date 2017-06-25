var area = document.getElementById('area'),
	preview = document.getElementById('preview');
	
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
});