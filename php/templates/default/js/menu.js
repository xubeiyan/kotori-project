const menu_button = document.querySelector('.menu');		// 菜单按钮
const left_part = document.querySelector('.left-part');		// 菜单左边部分

menu_button.addEventListener('click', () => {
	left_part.classList.toggle('hide');
});
