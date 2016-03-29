<?php
//****************************************************************
//功  能：数据库处理类
//****************************************************************
//deal_db 		类的构造，其中host,数据库服务器名;usr,登录数据库用户名;pwd,登录数据库用户密码;dbname,选择数据库名;
//doexec 		执行数据库添加、更新、删除,sql-SQL语句,flag,数据库类型标志
//doquery_row 	执行select查询(限返回一条记录数组),sql-SQL语句,flag,数据库类型标志
//doquery_rows	执行select查询(返回多条记录),sql-SQL语句,
//****************************************************************
/*
define('SYS_DB_MAIN_HOST','localhost');			// 数据库服务器ip
define('SYS_DB_MAIN_USER','robert');				// 登陆用户名
define('SYS_DB_MAIN_PWD','beinouvy');		// 登陆密码
define('SYS_DB_MAIN_NAME','withfi');					// 数据库名
*/

/*
define('SYS_DB_MAIN_HOST','localhost');			// 数据库服务器ip
define('SYS_DB_MAIN_USER','robert');				// 登陆用户名 withfi.com
define('SYS_DB_MAIN_PWD','beinouvy');		// 登陆密码	withfi.com
define('SYS_DB_MAIN_NAME','withfi');					// 数据库名
*/


define('SYS_DB_MAIN_HOST','localhost');			// 数据库服务器ip
define('SYS_DB_MAIN_USER','wifiadmin');				// 登陆用户名 weafee.net
define('SYS_DB_MAIN_PWD','wifiadmin');		// 登陆密码		weafee.net
define('SYS_DB_MAIN_NAME','withfi');					// 数据库名







class deal_db
{
	public $dbconn;
	private $execrt;
	private $queryrt;
	public $cok;
	
	//类的构造，其中host,数据库服务器名;usr,登录数据库用户名;pwd,登录数据库用户密码;dbname,选择数据库名;
	function __construct($host,$usr,$pwd,$dbname=""){
		$conn_string = "host=".$host." port=5432"." dbname=$dbname user=".$usr." password=".$pwd."";
	    $this->dbconn = pg_pconnect($conn_string);
	  	if ($this->dbconn==FALSE) $this->debug_error("connect fail");

		return $this->dbconn;
	}
	//执行数据库添加、更新、删除,sql-SQL语句
	function doexec($sql){
	  	$this->execrt=pg_query($this->dbconn,$sql);
	  	if ($this->execrt==FALSE) $this->debug_error($sql);
	}
	//执行select查询(限返回一条记录数组),sql-SQL语句
	function doquery_row($sql){

  		$this->queryrt=pg_query($this->dbconn, $sql);
	  	if ($this->queryrt==false) {
			$this->debug_error($sql);
		}

		@$row=pg_fetch_array($this->queryrt, 0, PGSQL_ASSOC);

		return $row;
	}
	//执行select查询(返回多条记录),sql-SQL语句
	function doquery_rows($sql){
	  	$this->queryrt=pg_query($this->dbconn, $sql);
	  	if ($this->queryrt==FALSE) $this->debug_error($sql);
		$row_num=pg_num_rows($this->queryrt);
	  	if ($row_num>20000) $this->debug_error("too much data,doquery_rows code support max result munber is 10000！<!--" . $sql . "-->");
		$rows=pg_fetch_all($this->queryrt);

		pg_free_result($this->queryrt);
		return $rows;
	}
	//调用编码
	function setNames($charset){
	   if(!empty($charset))	pg_query("set names '$charset'");	
	}
	//关闭连接释放内存
	function close(){
		@pg_close($this->dbconn);	
	
	}
	// 输出报错信息
	function debug_error($errstr=""){
		$errstr.="<br>[**SQL错误**]".": ".pg_last_error ($this->dbconn)."<br>\r\n";
		echo $errstr;
		exit;
	}

	//*******************************************************************
	// 1.检查用户名是否存在
	//*******************************************************************
	function userExsit($username){
		$sql="select id,puid  from w_users where username='$username';";
		$res=$this->doquery_row($sql);
		if($res['uid']) return false;
		return $res;
	}

	//添加用户
	function add_user($username,$password,$email,$regdate,$regip,$tel,$qq,$contactor_name,$contactor_tile,$shop_name,$addr,$shopmemo,$puid=0,$level=2){
	        $sql="INSERT INTO w_users (username , password , email , reg_date , reg_ip ,tel,qq,contactor_name,contactor_tile,shop_name,addr,shopmemo,puid,level) VALUES ('$username', '$password', '$email',  '$regdate', '$regip',$tel,$qq,'$contactor_name','$contactor_tile','$shop_name','$addr','$shopmemo',$puid,$level) RETURNING id";
			$result = $this->doquery_row($sql);
			return $result['id'];
	}
	//*******************************************************************
	// 3.用户修改密码
	//*******************************************************************
	function userPassUpdate($username,$user_pwd,$old_pwd){
		$sql="select count(*) AS sum from w_users where username='{$username}' and password='$old_pwd' ";
		$res=$this->doquery_row($sql);
		if($res['sum']>0){//找到了以后在使用update
		     $sqla="update w_users set password='$user_pwd' where username='{$username}' ";
			 $this->doexec($sqla);
			 return true;
		}else{  //无用户或是老密码不对
		      return false;
		}
	}
	//*******************************************************************
	// 3.子用户修改密码
	//*******************************************************************
	function cuserPassUpdate($username,$user_pwd){
		
		 $sqla="update w_users set password='$user_pwd' where username='{$username}' ";
		 $this->doexec($sqla);
		 return true;
		
	}
	//*******************************************************************
	// 4.取用户资料
	//*******************************************************************
	function getUserRs($username){
		$sql="select * from w_users where username='{$username}' LIMIT 1";
		$res=$this->doquery_row($sql);
		return $res;
	}
	//更新设备浏览次数
	function update_mac_count($uid,$mac){
		$sql = "update tb_router_ams set cou=cou+1 where uid={$uid} and mac='{$mac}'";
		$re=$this->doexec($sql);
	}
        //将登陆页面
        function update_login_pg_count($uid,$mac){
		$sql = "update tb_router_ams set cou=cou+1 where uid={$uid} and mac='{$mac}'";
		$re=$this->doexec($sql);
	}
        
        
        
	//登陆记录
	function add_login_log($para){
		$sql="select log_id,time from tb_log where uid={$para['uid']} and mac='{$para['mac']}' and gw_mac='{$para['gw_mac']}' and event='{$para['event']}' order by time desc limit 1";
		$res=$this->doquery_rows($sql);
		if(!$res){
			$sql="insert into tb_log(uid,mac,gw_mac,event,value,time,token, session_id, open_id) values({$para['uid']},'{$para['mac']}','{$para['gw_mac']}','{$para['event']}','{$para['value']}',".time().",'{$para['token']}', '{$para['session_id']}', '{$para['open_id']}')";
			$res=$this->doexec($sql);
		}else{
			$t=time();
			//if($res[0]['time']<$t-$t%86400){
				$sql="insert into tb_log(uid,mac,gw_mac,event,value,time,token, session_id, open_id) values({$para['uid']},'{$para['mac']}','{$para['gw_mac']}','{$para['event']}','{$para['value']}',".$t.",'{$para['token']}', '{$para['session_id']}', '{$para['open_id']}')";
				$res=$this->doexec($sql);
			//}
		}

	}
    
	function save_token($token, $gw_id, $url, $mac, $gw_mac) 
	{
		$sql="INSERT INTO tb_token (token, use, url, time, mac, username, gw_mac) VALUES ('$token', 0, '$url', LOCALTIMESTAMP, '$mac', '$gw_id', '$gw_mac');";
		$this->doexec($sql);
	}
	//显示登陆页
	function show_login_log($mac,$psize=12,$pstart=0){
		$sql = "select * from tb_log  where  uid={$this->cok['uid']} ";
		if($mac!=''){
			$sql.=' and gw_mac="'.$mac.'"';
		}
		$sql.="and event <> 'login' order by log_id desc limit {$psize} offset {$pstart}";

		$re=$this->doquery_rows($sql);

		$sql = "select count(*) as c from tb_log where uid={$this->cok['uid']}";
		if($mac!=''){
			$sql.=' and gw_mac="'.$mac.'"';
		}
		$co=$this->doquery_row($sql);
		$re['cou']=$co['c'];

		return $re;
	}

	function add_pic($uid, $raw_path, $small_path, $big_path, $orig_filename,$times)
	{
		$sql = "INSERT INTO tb_pic_resource (uid, orig_filename, raw_path, small_path, big_path, upload_time) VALUES ($uid, '$orig_filename', '$raw_path', '$small_path', '$big_path', '$times') RETURNING pic_id";
		$result=$this->doquery_row($sql);
		
		return $result['pic_id']; 
	}

	function add_order($price){
		$sql="INSERT INTO tb_orders (uid , price , sta , type, addtime) VALUES ({$this->cok['uid']}, $price, 0, 1,'".date('Y-m-d H:i:s',time())."') RETURNING oid";
		$result = $this->doquery_row($sql);
		return $result['oid'];
	}
	function update_order($para){
		$sql="select sta,uid from tb_orders where oid={$para['oid']}";
		$result = $this->doquery_row($sql);
		$uid=$result['uid'];
		if($result['sta']==0){
			$sql="update tb_orders set paymoney={$para['paymoney']},buyer_email='{$para['email']}',sta=1,paytime='{$para['paytime']}' where oid={$para['oid']}";
			$this->doexec($sql);
			$fen=$para['paymoney']*1000;
			$sql="update w_users set points=points+{$fen} where id={$uid}";
			$this->doexec($sql);
		}
	}
	function show_orders($oid,$type='',$psize=12,$pstart=0){
		$sql="select * from tb_orders where uid={$this->cok['uid']} ";
		if($oid!=''){
			$sql.=' and oid='.$oid;
		}
		if($type!=''){
			$sql.=' and type='.$type;
		}
		$sql.=" order by oid desc limit {$psize} offset {$pstart}";
		$result = $this->doquery_rows($sql);
		$sql = "select count(*) as c from tb_orders where uid={$this->cok['uid']}";
		if($oid!=''){
			$sql.=' and oid='.$oid;
		}
		if($type!=''){
			$sql.=' and type='.$type;
		}
		$co=$this->doquery_row($sql);
		$result['cou']=$co['c'];
		return $result;
	}

	function add_send_msg_log($pare){
		$sql="INSERT INTO tb_send_sms_log (uid , msg , dateblock , p_num,  addtime,price) VALUES ({$this->cok['uid']}, '{$pare['msg']}', '{$pare['dateblock']}', {$pare['p_num']},".time().",{$pare['price']}) RETURNING sms_id";
		$result = $this->doquery_row($sql);
		return $result['sms_id'];
	}
	function show_send_msg_log($psize,$pstart){
		$sql="select * from tb_send_sms_log where uid={$this->cok['uid']}  order by sms_id desc limit {$psize} offset {$pstart}";
		$result = $this->doquery_rows($sql);

		$sql = "select count(*) as c from tb_send_sms_log where uid={$this->cok['uid']}";
		$co=$this->doquery_row($sql);
		$result['cou']=$co['c'];

		return $result;
	}
	function get_count_log($s,$e){
		$sql="select value from tb_log where time>{$s} and time<{$e} and event='手机登陆' and uid={$this->cok['uid']}  group by value";
		$result = $this->doquery_rows($sql);
		return $result;
	}
	function fee($v){
		$sql="update w_users set points=points-{$v} where id={$this->cok['uid']}  ";
		$this->doexec($sql);
	}
	function tj($para){
		if($para['type']=='hour'){
			$sql="select time,sum(count) as count from tb_aggr_access_count where uid={$this->cok['uid']} and time>".(time()-3600*24)." group by time";
		}
		if($para['type']=='day'){
			$para['time']=$para['time']==7?7:30;
			$sql="select day,sum(count) as count from tb_aggr_access_count where uid={$this->cok['uid']} and time>".(time()-$para['time']*3600*24)." group by day";
		}
		$result = $this->doquery_rows($sql);
		return $result;
	}

}