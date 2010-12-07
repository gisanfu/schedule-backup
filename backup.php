<?php

// 這一個是備份用的程式

require 'config.php';

// 備份的週期
// 如果沒有定義，就代表全部
$cycles = array(
	'day' => array('pgsql','mysql'),
	'week' => array('config', 'html'),
	'month' => array('ftp', 'gisanfu', 'root'),
	'all' => array(), // 這是完整備份，通常都是主機還有一口氣的時候
);

$cycle = $argv[1];

if($cycle == ''){
	echo 'please input cycle:'."\n";
	foreach($cycles as $cyclekey => $cyclevalue){
		echo $cyclekey."\n";
	}
	exit;
}

$classes = array();

// 這裡會決定要處理那些分類
if(count($cycles[$cycle]) <= 0){
	$classes = $config;
} else {
	foreach($cycles[$cycle] as $key => $class){
		$classes[$class] = $config[$class];
	}
}

foreach($classes as $class => $items){

	// 先建立分類的資料夾
	$dest = $dest_dir.'-'.$cycle.'/'.$class;
	mkdir($dest, 0777, true);

	if($class == 'pgsql'){
		foreach($items as $key => $item){
			$cmd = 'pg_dump -Uyour_user -h'.$pgsql_db_host.' '.$item.' > '.$dest.'/'.$item.'.sql';
			echo $cmd."\n";
			`$cmd`;
		}
	} elseif($class == 'mysql'){
		foreach($items as $key => $item){
			$cmd = 'mysqldump -u backup -h '.$mysql_db_host.' '.$item.' > '.$dest.'/'.$item.'.sql';
			echo $cmd."\n";
			`$cmd`;
		}
	} else {
		foreach($items as $item => $attrs){
			// 檢查有沒有定義，沒有的話，就用預設的
			foreach($fileattr as $attr => $attr_value){
				if($attrs[$attr] == ''){
					$attrs[$attr] = $attr_value;
				}
			} // fileattr	
			$cmds = array('scp');
			if($attrs['filetype'] == '2') array_push($cmds, '-r');
			array_push($cmds, $ssh_host.':'.$item, $dest);
			$cmd = join(' ', $cmds);
			echo $cmd."\n";
			`$cmd`;
		}
	}
}


?>
