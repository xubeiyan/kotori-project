<?php
$imageDataFile = 'data/imagedata';
$userDataFile = 'data/userdata';
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

$fpr = fopen($userDataFile, 'r');

for($line = fgets($fpr), $all = ''; $line['0'] == '#'; $line = fgets($fpr)) {
	$all .= $line;
}

fclose($fpr);

$fpw = fopen($userDataFile, 'w');

fwrite($fpw, $all);

fclose($fpw);

echo '<p>已清空userdata</p>';

array_map('unlink', glob($imageFolder . '/*'));

echo '<p>已清空uploads目录</p>';

array_map('unlink', glob($thumbFolder . '/*'));

echo '<p>已清空thumbs目录</p>';

?>