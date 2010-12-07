<?php

// backup
$ssh_host = 'root@server01.com';

$pgsql_db_host = 'your_pgsql_db_host';
$mysql_db_host = 'your_mysql_db_host';

$dest_dir = date("Ymd-Hi");

// 用來決定scp是否要加上參數r
$filetype = array(
	'file' => '1', // 檔案
	'dir' => '2', // 資料夾
);

// 預設的檔案或資料夾擁有者
$fileattr = array(
	// filetype只是讓我能夠簡單的判斷，scp指令要不要加上-r的引數而以
	'filetype' => '1',

	// 這是用在備份的時候，所要設定的
	'owner' => 'root',
	'group' => 'root',

	// 等於壹的時候，代表啟動restore.php的時候，會ignore那個項目
	'backuponly' => '0',
);

$config = array(
	'config' => array(
		'/etc/passwd' => array('backuponly' => '1'),
		'/etc/shadow' => array('backuponly' => '1'),
		'/etc/rc.local' => array(),
		'/etc/hosts' => array(),
		'/etc/resolv.conf' => array(),
		'/etc/sysconfig/network' => array(),
		'/etc/sysconfig/network-scripts/ifcfg-ppp0' => array(),
		'/etc/sysconfig/network-scripts/ifcfg-eth0' => array(),
		'/etc/sysconfig/network-scripts/ifcfg-eth1' => array(),
		'/etc/httpd/conf' => array('filetype' => '2'),
		'/etc/httpd/conf.d' => array('filetype' => '2'),
		'/etc/httpd/ssl' => array('filetype' => '2'),
	), // config
	'html' => array(
		'/var/www/html' => array(
							'filetype' => '2',
							'owner' => 'apache',
							'group' => 'apache',
						),
	), // html
	'ftp' => array(
		'/var/ftp' => array(
							'filetype' => '2',
							'owner' => 'apache',
							'group' => 'apache',
						),
	), // ftp
	'gisanfu' => array(
		'/home/gisanfu/.ssh' => array(
							'filetype' => '2',
							'owner' => 'gisanfu',
							'group' => 'gisanfu',
						),
		'/home/gisanfu/.bashrc' => array(
							'owner' => 'gisanfu',
							'group' => 'gisanfu',
						),
		'/home/gisanfu/.vimrc' => array(
							'owner' => 'gisanfu',
							'group' => 'gisanfu',
						),
	), // gisanfu
	'root' => array(
		'/root/.bashrc' => array(),
		'/root/.vimrc' => array(),
	), // root
	'pgsql' => array(
		'pgsql-db-01',
		'pgsql-db-02',
	), // pgsql 
	'mysql' => array(
		'mysql-db-01',
		'mysql-db-02',
	), // db
);

?>
