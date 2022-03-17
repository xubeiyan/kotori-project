const area = document.getElementById('area');				// 点击或拖放区域
const image = document.getElementById('image-area');		// 图片预览区域
const details = document.getElementById('details-area');	// 图片详细区域
const file = document.getElementById('file');				// 上传input
const upload_button = document.getElementById('uploadButton');		// 上传按钮
const notice = document.getElementById('notice');			// 提示信息

let UPLOAD_FILE_SIZE = 1024 * 10;							// 上传文件大小限制
let imgObj = {};
let imgInfo = {											// 待上传文件信息
	img: undefined,
	filename: undefined,
	filesize: undefined,
};
// 显示提示信息
const showNotice = function (text) {
	notice.innerHTML = '<span>' + text + '</span>';
};
// 清除提示信息
const clearNotice = function () {
	notice.innerHTML = '';
};
// 检查图片
const imgValidate = function (fileList) {
	clearNotice();
	
	if (fileList.length == 0) {
		return false;
	}
	
	if (fileList[0].type.indexOf('image') == -1) {
		showNotice('注意：选择上传的文件不是图片');
		return false;
	}
	
	imgInfo.img = window.URL.createObjectURL(fileList[0]);
	imgInfo.filename = fileList[0].name;
	imgInfo.filesize = Math.floor((fileList[0].size) / 1024);
		
	if (imgInfo.filesize > UPLOAD_FILE_SIZE) {
		showNotice('注意：文件大小不能超过10M');
		return false;
	}
	return true;
};
const clearPreview = function () {
	image.innerHTML = '';
	details.innerHTML = '';
	imgInfo = {											
		img: undefined,
		filename: undefined,
		filesize: undefined,
	}
};
// 显示图片预览信息
const showUploadFileDetails = function (fileList) {
	var imgStr = '<img id="uploadImg" src="' + imgInfo.img + '">',
		detailStr = '<p class="details"><span>图片名称：' + imgInfo.filename + '</span></p>' +
			'<p class="details"><span>大小:' + imgInfo.filesize + 'KB</span></p>';
	imgObj = fileList[0];
	image.innerHTML = imgStr;
	details.innerHTML = detailStr;
	area.classList.add('preview');
	upload_button.classList.remove('hide');
};

fetch('?upload_limit_info')
	.then((res) => res.json())
	.then(data => {
		console.log(`从服务端获取的上传文件大小限制为：${data.upload_size_limit / 1024} KB`);
		UPLOAD_FILE_SIZE = data.upload_size_limit;
	});
	
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
	if (!imgValidate(fileList)) {
		clearPreview();
		return;
	}
	showUploadFileDetails(fileList);
});

area.addEventListener("click", function () {
	file.click();
});

file.addEventListener("change", function (e) {
	var fileList = e.target.files;
	if (!imgValidate(fileList)) {
		clearPreview();
		return;
	}
	showUploadFileDetails(fileList);
});

upload_button.addEventListener("click", function () {
	var xhr = new XMLHttpRequest(),
		formData = new FormData(),
		preview = document.getElementById("preview"),
		progress = preview.appendChild(document.createElement("p"));
		
	// 隐藏上传按钮
	upload_button.style.display = 'none';
	
	progress.appendChild(document.createTextNode("上传中"));
	progress.id = "progress";	
		
	xhr.upload.addEventListener("progress", function(e) {
		var pc = parseInt(e.loaded / e.total * 100);
		if (e.lengthComputable) {
			progress.style.backgroundSize = pc + "% 100%";
			progress.innerText = progress.innerText.split(' ')[0] + ' ' + e.loaded + '/' + e.total;
			console.log(e.loaded + '/' + e.total);
		}
	}, false);
		
	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && xhr.status == 200) {
			var responseArray = JSON.parse(xhr.responseText),
				p_result = document.createElement("p");
				p_result.className = 'result';
			var	folder = preview.appendChild(p_result);
				
			if (responseArray['result'] == 'upload success') {
				progress.className = "success";
				progress.innerHTML = "上传成功";
				
				folder.innerHTML = '上传路径: <a class="upload" href="' + responseArray['savePath'] + '">' + responseArray['savePath'] + '</a>';
			} else if (responseArray['result'] == 'upload fail'){
				progress.className = "failed";
				progress.innerHTML = "上传失败";

				folder.innerHTML = '出错原因:' + responseArray['error'];
			}
		}
	}
	
	xhr.open('POST', '?uploadpost', true);
	xhr.setRequestHeader('X-FILENAME', 'XMLHttpRequest');
	xhr.setRequestHeader('Kotori-Request', 'FileUpload');
	
	formData.append('img', imgObj);
	
	xhr.send(formData);
});