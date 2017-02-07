<?php

function ezdb_connect_db($dbconfig)
{
	$ci =& get_instance();
	
	$config = array(
		'dsn'	=> '',
		'hostname' => '',
		'username' => '',
		'password' => '',
		'database' => '',
		'dbdriver' => 'pdo',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8',
		'dbcollat' => 'utf8_unicode_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => TRUE
	);
	
	foreach($dbconfig as $key=>$val) $config[$key] = $val;
	
	if(empty($dbconfig['dsn']))
		$config['dsn'] = "{$dbconfig['dbtype']}:host={$dbconfig['hostname']};port=3306;dbname={$dbconfig['database']};charset=utf8";
	
	return $ci->load->database($config, true);
}

function ezdb_close_db($db)
{
	$db->close();
}
 
function ezdb_begin_transaction($mode='auto', $db='')
{
	if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
	
	if($mode=='auto') $db->trans_start();
	else $db->trans_begin();
}

function ezdb_end_transaction($mode='auto', $status=true, $db='')
{
	if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
	
	if($mode=='auto') {
		$db->trans_complete();
		return $db->trans_status();
	} 
	// mode == manual
	else {
		$ret = $ci->db->trans_status();
		if($ret==true) {
			if($status==true) {
				$ci->db->trans_commit();
				return true;
			}
			else {
				$ci->db->trans_rollback();
				return false;
			}
		} else {
			$ci->db->trans_rollback();
			return false;
		}
	}
}

function ezdb_retmsg($ret, $successMsg="정상적으로 처리되었습니다.", $failMsg="처리중 에러가 발생했습니다.")
{
	if($ret===false) return array('success'=>0, 'msg'=>$failMsg /*. ' ' . ezdb_error_message() */);
	else return array('success'=>1, 'msg'=>$successMsg);
}
 
function ezdb_error_message($db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
#log_message('debug', __METHOD__ . ': ' . var_export($db,true));
    //return $db->_error_message();
	$err = $db->error();
	return $err['message'] . '(' . $err['code'] . ')';
}

function ezdb_query($qry, $db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
    return $db->query($qry);
}

// $sth : query(), prepare()로 리턴된 값
function ezdb_fetch(&$sth)
{
	return $sth->unbuffered_row('array');
}

// 사용하지 말고 ezdb_query 사용할 것 : 실사용시 undefined method 에러 발생
function ezdb_exec($qry, $db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
    return $db->exec($qry);
}

// $mode : array, object
function ezdb_select($qry, $data=array(), $db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
	
    $res = $db->query($qry, $data);
    if($res===false) return false;
    
    return $res->result_array();
}

function ezdb_select_one($qry, $data=array(), $db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
	
    $res = $db->query($qry, $data);
    if($res===false) return false;
    
    return $res->row_array();
}

// 사용예: $cnt = ezdb_select_data("SELECT COUNT(*) FROM some_table");
function ezdb_select_data($qry, $db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}

    $res = $db->query($qry);
    if($res===false) return false;
    
    $row = $res->row_array();
#log_message('debug', __METHOD__ . ': ' . var_export($row,true));	
	// reset($row)는 array_shift()를 이용한 것과 동일한 역할을 함
	return empty($row) ? '' : reset($row);
}

function ezdb_insert($table, $data, $db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
    
    $fieldDetails = NULL;
    foreach($data as $key=> $value) {
        if(substr($value,0,2)=='-|' && substr($value,-2)=='|-') {
			$fieldDetails .= "`$key`=" . substr($value,2,-2) . ',';
			unset($data[$key]);
		}
		else $fieldDetails .= "`$key`=?,";
    }
    $fieldDetails = rtrim($fieldDetails, ',');
    
    $qry = "INSERT INTO {$table} SET {$fieldDetails}";
	$ret = $db->query($qry, $data);
    if($ret===false) return false;
	
    return $db->insert_id();
}

function ezdb_update($table, $data, $where, $db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}
    
    $fieldDetails = NULL;
    foreach($data as $key=> $value) {
        if(substr($value,0,2)=='-|' && substr($value,-2)=='|-') {
			$fieldDetails .= "`$key`=" . substr($value,2,-2) . ',';
			unset($data[$key]);
		}
		else $fieldDetails .= "`$key`=?,";
    }
    $fieldDetails = rtrim($fieldDetails, ',');
    
    $qry = "UPDATE {$table} SET {$fieldDetails} WHERE {$where}";
#log_message('debug', __METHOD__ . ': ' . $qry);
    return $db->query($qry, $data);
}

function ezdb_delete($table, $where, $db='')
{
    if(empty($db)) {
		$ci =& get_instance();
		$db = $ci->db;
	}

    $qry = "DELETE FROM {$table} WHERE {$where}";
    return $db->query($qry);
}
