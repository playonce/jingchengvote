<?php 
/**
 * 连接数据库
 * @return resource
 */
 error_reporting(E_ALL ^ E_DEPRECATED);
 
function updatedb($table,$where){
	if($table=="vote_admin"){
		@$sql="update {$table} set username=0,password=0 {$where}";
	}
	mysql_query(@$sql);
	return 1;
} 

function updatecate($table,$where){
	if($table=="vote_cate"){
		@$sql="update {$table} set cName=0 {$where}";
	}
	mysql_query(@$sql);
	return 1;
} 

function updatescate($table,$where){
	if($table=="vote_suncate"){
		@$sql="update {$table} set sName=0 {$where}";
	}
	mysql_query(@$sql);
	return 1;
}



 
function connect(){
	$link=mysql_connect(DB_HOST,DB_USER,DB_PWD) or die("数据库连接失败Error:".mysql_errno().":".mysql_error());
	mysql_set_charset(DB_CHARSET);
	mysql_select_db(DB_DBNAME) or die('指定数据库打开失败');
	return $link;
}

/**
 * 完成记录插入的操作
 * @param string $table
 * @param array $array
 * @return number
 */
function insert($table,$array){
	$keys=join(",",array_keys($array));
	$vals="'".join("','",array_values($array))."'";
	$sql="insert {$table}($keys) values({$vals})";
	echo ($sql)	;	//打印标记，记得删除echo ($sql)	;
	mysql_query($sql);
	return mysql_insert_id();
}

/**
 * 记录的更新操作
 * @param string $table
 * @param array $array
 * @param string $where
 * @return number
 */
function update($table,$array,$where=null){
	$str=null;
	if (is_array($array))foreach((array)$array as $key=>$val){
		if($str==null){
			$sep="";
		}else{
			$sep=",";
		}
		if($key=="id"||is_numeric($key)){
			continue;
		}else{
			$str.=$sep.$key."='".$val."'";
		}
	}
		$sql="update {$table} set {$str} ".($where==null?null:" where ".$where);
		echo ($sql)	;//打印数据，记得删除echo ($sql)	;
		$result=mysql_query($sql);
		if($result){
			return true;
		}else{
			return false;
		}
}

/**
 *	删除记录
 * @param string $table
 * @param string $where
 * @return number
 */
 
function delete($table,$where=null){
	$where=$where==null?null:" where ".$where;
	$sql="delete from {$table} {$where}";
	echo ($sql);//-------------------------------------------打印记得删除
	mysql_query($sql);
	return mysql_affected_rows();
}
 
function deletele($table,$where=null){
	$sql = "select * from {$table} {$where}";
	$res = mysql_query($sql);
	$row=mysql_fetch_array($res);
	$i= $row['id'];
	if($i==1){
			if($table=="vote_admin"){
				alertMes("无法删除！","listAdmin.php");
			}elseif($table=="vote_cate"){
				alertMes("无法删除！","listCate.php");
			}elseif($table=="vote_suncate"){
				alertMes("无法删除！","listCate.php");
			}
		}else{
	$sql="delete from {$table} {$where}";
		mysql_query($sql);}
	return true;
}


/**
 *	删除管理员记录
 * @param string $table
 * @param string $where
 * @return number
 */
function deletedb($table,$where=null){
	$where=$where==null?null:" where ".$where;
	if(!$where==null){
		$sql="select * from {$table}";
		$topRows=getResultNum($sql);
		$sql = "select * from {$table} {$where}";
		$res = mysql_query($sql);
		$row=mysql_fetch_array($res);
		$i= $row['id'];
		
		$where1="where id = {$i}";
		if($i==1){
			if($table=="vote_admin"){
				alertMes("无法删除！","listAdmin.php");
			}elseif($table=="vote_cate"){
				alertMes("无法删除！","listCate.php");
			}elseif($table=="vote_suncate"){
				alertMes("无法删除！","listCate.php");
			}
			exit;
		}
		for($i;$i<$topRows;$i++){
			$where = "id = {$i}";
			$where1 ="where id = {$i}+1";
			$sql = "select * from {$table} {$where1}";
			$rs=mysql_query($sql);
			$terow = mysql_fetch_array($rs);
			if($table=="vote_admin"){
				updatedb($table,$where1);
			}elseif($table=="vote_cate"){
				updatecate($table,$where1);
			}elseif($table=="vote_suncate"){
				updatescate($table,$where1);
			}
			update($table,$terow,$where);
		}
		if($i==$topRows){
			deletele($table,$where1);
			$sql="alter table {$table} auto_increment = {$topRows}";
			mysql_query($sql);
		}
	}else{
			if($table=="vote_admin"){
				alertMes("删除失败！","listAdmin.php");
			}elseif($table=="vote_cate"){
				alertMes("删除失败！","listCate.php");
			}elseif($table=="vote_suncate"){
				alertMes("删除失败！","listCate.php");
			}
		}
	return true;
}



/**
 *得到指定一条记录
 * @param string $sql
 * @param string $result_type
 * @return multitype:
 */
function fetchOne($sql,$result_type=MYSQL_ASSOC){
	$result=mysql_query($sql);
    @$row=mysql_fetch_array($result,$result_type);   
	
	return $row;
	
}


/**
 * 得到结果集中所有记录 ...
 * @param string $sql
 * @param string $result_type
 * @return multitype:
 */
function fetchAll($sql,$result_type=MYSQL_ASSOC){
	$result=mysql_query($sql);
	while(@$row=mysql_fetch_array($result,$result_type)){
		$rows[]=$row;
	}
	return @$rows;
}

/**
 * 得到结果集中的记录条数
 * @param unknown_type $sql
 * @return number
 */
function getResultNum($sql){
	$result=mysql_query($sql);
	return mysql_num_rows($result);
}

/**
 * 得到上一步插入记录的ID号
 * @return number
 */
function getInsertId(){
	return mysql_insert_id();
}

