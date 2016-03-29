<?php

#require("class_db.php");
require_once("php-user-agent/lib/phpUserAgent.php");
require_once("php-user-agent/lib/phpUserAgentStringParser.php");

#$obj_db_main = new deal_db(SYS_DB_MAIN_HOST,SYS_DB_MAIN_USER,SYS_DB_MAIN_PWD,SYS_DB_MAIN_NAME);	// 数据库操作对
#$obj_db_main->setNames('utf8');
#$request = json_encode($_GET);
#$rec_time = date('Y-m-d H:i:s');
#$sql = "INSERT INTO probe_debug (request,rec_time) VALUES ('$request','$rec_time') RETURNING id";
#$re=$obj_db_main->doquery_row($sql);

$ua_raw = $_SERVER['HTTP_USER_AGENT'];
$userAgent = new phpUserAgent($ua_raw);

#$userAgent->getBrowserName()      // firefox
#$userAgent->getBrowserVersion()   // 3.6
#$userAgent->getOperatingSystem()  // linux
#$userAgent->getEngine()           // gecko

#$ua = array('raw' => $ua_raw,
#            'os' => $userAgent->getOperatingSystem(),
#            'browser' => array(
#                'engine' => $userAgent->getEngine(),
#                'name' => $userAgent->getBrowserName(),
#                'version' => $userAgent->getBrowserVersion()
#                )
#            );
$host = "127.0.0.1";
$db_name = "probe";
date_default_timezone_set("Asia/Shanghai");
$date_str = date("Ymd");
$collection_name = "access_" . $date_str;
$conn = new Mongo($host);
$db = $conn->selectDB($db_name);
$collection = $db->selectCollection($collection_name);
$query = $_GET;
#$query['rec_time'] = date('Y-m-d H:i:s');
$query['rec_time'] = new MongoDate();
$query['ua'] = $ua_raw;
$query['os'] = $userAgent->getOperatingSystem();
$query['browser'] = array(
                'engine' => $userAgent->getEngine(),
                'name' => $userAgent->getBrowserName(),
                'version' => $userAgent->getBrowserVersion()
                );

$collection->insert($query);
$conn->close();

header("content-type:image/gif");
$str=file_get_contents("./track.gif");
echo $str;
exit();
