<?php

// 這個檔案是要做回復的動作
// 在使用這個檔案之前，請先想清楚

require 'config.php';


// 這裡會複寫config.php的變數
$ssh_host = 'root@server01.com';
$pgsql_db_host = 'your_pgsql_db_host';
$mysql_db_host = 'your_mysql_db_host';

$source_dir = $argv[1];

if($source_dir == ''){
	echo 'please input source dir'."\n";
	exit;
}

if(!is_dir($source_dir)){
	echo 'source dir is not exist'."\n";
	exit;
}

foreach($config as $class => $items){

	$source = $source_dir.'/'.$class;

	if($class == 'pgsql'){
		foreach($items as $key => $item){
			$cmd = 'createdb -Uyour_user -h'.$pgsql_db_host.' '.$item.' --owner your_owner';
			echo $cmd."\n";
			`$cmd`;
			$cmd = 'psql -Uyour_user -h'.$pgsql_db_host.' '.$item.' < '.$source.'/'.$item.'.sql';
			echo $cmd."\n";
			`$cmd`;
		}
	// This is not test
	} elseif($class == 'mysql'){
		foreach($items as $key => $item){
			$cmd = 'mysqladmin create '.$item.' -p -Uyour_user -h'.$mysql_db_host;
			echo $cmd."\n";
			`$cmd`;
			$cmd = 'mysql -Uyour_user -h'.$mysql_db_host.' '.$item.' < '.$source.'/'.$item.'.sql';
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

			// 如果有純備份的Flag，那就跳過
			if($attrs['backuponly'] == '1')	continue;

			// 取得最右邊的資料夾名稱或是檔名
			$item_split = split('/', $item);
			$item_name = $item_split[count($item_split) - 1];

			// 取得最右邊以外的資料夾名稱
			array_shift($item_split);
			array_pop($item_split);
			$item_dir = join('/', $item_split);
			$item_dir = '/'.$item_dir;

			// 組合scp指令串
			$cmds = array('scp');
			if($attrs['filetype'] == '2') array_push($cmds, '-r');
			array_push($cmds, $source.'/'.$item_name, $ssh_host.':'.$item_dir);
			$cmd = join(' ', $cmds);
			echo $cmd."\n";
			`$cmd`;
			
			// 組合chown指令串
			$cmds = array('ssh', $ssh_host, "'chown");
			if($attrs['filetype'] == '2') array_push($cmds, '-R');
			array_push($cmds, $attrs['owner'].':'.$attrs['group'], $item."'");
			$cmd = join(' ', $cmds);
			echo $cmd."\n";
			`$cmd`;

		}
	}
}

?>
