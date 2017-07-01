<?php
$imageDataFile = 'data/imagedata';
$imageFolder = 'uploads';
$thumbFolder = 'thumbs';

$fpr = fopen($imageDataFile, 'r');

for($line = fgets($fpr), $all = ''; $line['0'] == '#'; $line = fgets($fpr)) {
	$all .= $line;
}

fclose($fpr);

$fpw = fopen($imageDataFile, 'w');

fwrite($fpw, $all);

fclose($fpw);

echo '<p>已清空imagedata</p>';

array_map('unlink', glob($imageFolder . '/*'));

echo '<p>已清空uploads目录</p>';

?>