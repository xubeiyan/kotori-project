<?php
/**
* 简单的页面渲染实现方法
*/
class Template {
	private static $templatesFolder = 'templates/';	// 模板文件位置
	
	public static function render($page, $template, $param = Array()) {
		$completePath = self::$templatesFolder . $page . '.html';
		
		if (!file_exists($completePath)) {
			die(sprintf('page template %s not exists', $page));
		}
		
		$templateFile = file_get_contents($completePath);
		foreach ($template as $key => $value) {
			$from = '{% ' . $key . ' %}';
			$to = $value;
			$templateFile = str_replace($from, $to, $templateFile);
		}
		
		$templateFile = str_replace('<% script %>', '', $templateFile);
		// 处理jump参数
		if (isset($param['jump'])) {
			$meta = sprintf('<meta http-equiv="refresh" content="%d;url=%s" />', $param['jump']['time'], $param['jump']['url']);
			$templateFile = str_replace('{jump_time}', $param['jump']['time'], $templateFile);
		} else {
			$meta = '';
		}
		$templateFile = str_replace('<% meta %>', $meta, $templateFile);
		
		return $templateFile;
	}
	
}
?>