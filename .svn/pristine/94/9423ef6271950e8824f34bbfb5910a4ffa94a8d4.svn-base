<?php


require("class_db.php");


define('SYS_DB_MAIN_HOST','localhost');	// 数据库服务器ip
define('SYS_DB_MAIN_USER','wifiadmin');	// 登陆用户名
define('SYS_DB_MAIN_PWD','wifiadmin');   // 登陆密码
define('SYS_DB_MAIN_NAME','withfi');	// 数据库名

$obj_db_main = new deal_db(SYS_DB_MAIN_HOST,SYS_DB_MAIN_USER,SYS_DB_MAIN_PWD,SYS_DB_MAIN_NAME);	// 数据库操作对
$obj_db_main->setNames('utf8');
$rec_time = date('Y-m-d H:i:s');
$request = json_encode($_GET);

$sql = "INSERT INTO probe_debug (request,rec_time) VALUES ('$request','$rec_time') RETURNING id";

echo $sql; exit();

$re=$obj_db_main->doquery_row($sql);

$html = '<!DOCTYPE html><html><head><title></title><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head> <body><div>probe</div></body></html>';

echo $html;
exit();
