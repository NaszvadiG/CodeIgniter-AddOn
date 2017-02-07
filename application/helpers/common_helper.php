<?php

// 테마 로딩
function loadTheme($data=array(), $themeName='universal')
{
	$ci =& get_instance();
	
	$loadTheme = $ci->input->get_post('loadTheme');
	if(isset($loadTheme) && $loadTheme=='0') return;

	$ci->output->set_template($themeName);
	
	$ci->load->section('header', "themes/{$themeName}/header", $data);
	if(isset($data['title'])) 
		$ci->load->section('breadcrumb', "themes/{$themeName}/breadcrumb", $data);
	//$ci->load->section('left', "themes/{$themeName}/left", $data);
	//$ci->load->section('right', "themes/{$themeName}/right", $data);
	$ci->load->section('footer', "themes/{$themeName}/footer", $data);
}

function loadTemplate($data=array(), $themeName='universal')
{
	$ci =& get_instance();
	$ci->output->set_template($themeName);
}

// 배열 정렬
// SORT_ASC, SORT_DESC
// ex : arrayOrderby($arr, 'price', SORT_DESC)
function arrayOrderBy()
{
	$args = func_get_args();
	$data = array_shift($args);
	foreach ($args as $n => $field) {
		if (is_string($field)) {
			$tmp = array();
			foreach ($data as $key => $row)
				$tmp[$key] = $row[$field];
			$args[$n] = $tmp;
		}
	}
	$args[] = &$data;
	call_user_func_array('array_multisort', $args);
	return array_pop($args);
}

function jsAlertAndGo($msg='', $url='') 
{
    $ci =& get_instance();
	
	$go = ($url) ? "location.replace('".$url."');" : ''; //"history.go(-1);"
	$doAlert = empty($msg) ? 0 : 1;
    echo "
		<meta http-equiv=\"content-type\" content=\"text/html; charset=".$ci->config->item('charset')."\">
		<script type='text/javascript'>
			if({$doAlert}) alert('{$msg}');
			{$go}
		</script>
	";
    exit;
}

function checkLogin()
{
	$ci =& get_instance();
	
	if($ci->session->userdata('logged_in')!=true) {
		$ci->load->helper('url');
		redirect('/member/login');
	}
}

function checkAuth($authInfo, $siud='s')
{
	// 접근제어가 정의되어 있으면 그 값을 리턴함
	if(isset($authInfo[$siud])) return $authInfo[$siud];
	// 접근제어가 정의되어 있지 않으면 권한없음으로 설정
	else return array('auth'=>0, 'range'=>0);
}

function checkAccessAuth($preventList=array())
{
	$ci =& get_instance();
	
	if(empty($preventList)) $preventList[] = '[a-zA-Z]';
	
	$pageInfo = array(
		"controller" 	=> $ci->uri->segment(1),
		"func" 			=> $ci->uri->segment(2)
	);
	
	$isAjax = substr($ci->uri->segment(2),0,4)=='ajax' ? 1 : 0;
	
	//	@	로그인 쿠키 아이디값 존재 여부 검사		
	if( !$ci->session->userdata('logged_in')) {
		$doPrevent = 0;
		// 로그인 전이면 접근방지가 아닌지 체크
		foreach($preventList as $funcPrefix) {
			$doPrevent = preg_match("/^{$funcPrefix}/", $ci->uri->segment(2));
			if($doPrevent) break;
		}
		
		if($doPrevent) {
			if($isAjax) {
				return 0;
			} else {
				jsAlertAndGo('로그인 후 사용해주시기 바랍니다.' . $ci->uri->segment(2));
				exit;
			}
		}
	}
	return 1;
}
	
// 페이지 접근권한 체크
function checkAccessAuthOrg()
{
	$ci =& get_instance();
	
	$pageInfo = array(
		"controller" 	=> $ci->uri->segment(1),
		"func" 			=> $ci->uri->segment(2)
	);
	
	//	@	로그인 쿠키 아이디값 존재 여부 검사		
	if( !$ci->session->userdata('logged_in') ) {
		//jsAlertAndGo('로그인이 필요한 서비스입니다. 로그인 후 사용해주시기 바랍니다.', '/member/login');
		jsAlertAndGo('', '/member/login');
		exit;
		/*
		return array(
			'success'	=> 0,
			'msg'		=> '로그인이 필요한 서비스입니다. 로그인 후 사용해주시기 바랍니다.',
			'page'		=> '/member/login',
			'haveAuth'	=> 0
		);
		*/
	}
	$loginId = $ci->session->userdata('id');
	
	//	@	컨트롤러 / 함수 등록 여부 검사 - 미등록시 등록처리
	$sql = "
		SELECT p.*, u.idx AS userIdx
		FROM swcms_page p
		LEFT JOIN swcms_user u ON (u.id = '{$loginId}' AND FIND_IN_SET(p.idx, u.pageIdx)>0)
		WHERE p.controller = '{$pageInfo['controller']}'
			AND p.func = '{$pageInfo['func']}'
	";
	$row = ezdb_select_one($sql);
	
	// 페이지가 존재하지 않으면 신규입력
	if(empty($row)) {
		$row = array(
			'seq'			=> 1000,
			'controller'	=> $pageInfo['controller'],
			'func'			=> $pageInfo['func'],
			'memo'			=> '',
			'inuse'			=> '0'
		);
		if(!empty($pageInfo['controller']) && !empty($pageInfo['func']))
			$row['idx'] = ezdb_insert('swcms_page', $row);
	}//	end if
	
	// 관리계정은 무조건 허가함
	if($loginId == 'admin') return array(
		'success' 	=> 1,
		'page' 		=> "{$pageInfo['controller']}/{$pageInfo['func']}",
		'haveAuth' 	=> 1
	);;


	//	@	페이지 접근 권한 검사 실행
	if($row['inuse'] == '1') {
		// 사용자가 페이지 권한이 없으면
		if(empty($row['userIdx'])) {
			### Ajax 로 된 function일 경우는 Json으로 리턴한다.
			if(preg_match("(ajax)", $pageInfo['func'])){
				returnJson(array('success'=>0, 'msg'=>"{$ci->session->userdata('name')}님은  해당 기능을 사용할 권한이 없습니다."));
				exit;
			} else{
				jsAlertAndGo("{$ci->session->userdata('name')}님은  해당 페이지를 사용할 권한이 없습니다.");
				exit;
			}
		}
		
		// 페이지에 권한이 있으면
		$auth = array(
			'success' 	=> 1,
			'page' 		=> "{$pageInfo['controller']}/{$pageInfo['func']}",
			'haveAuth' 	=> 1
		);
	}
	// 권한사용을 하지 않는 페이지는 권한이 있는 것으로 간주함
	else return array(
		'success' 	=> 1,
		'page' 		=> "{$pageInfo['controller']}/{$pageInfo['func']}",
		'haveAuth' 	=> 1
	);
}





// 인사사진(images)가 설정된 경우 맨 첫컬럼에 사진이 나온다
function downloadExcelByDB($ci, $excelInfo, $filename, $incPhoto=0)
{
	ini_set('memory_limit', '1000M');
	set_time_limit ( 0 );
	
	$filename = $ci->input->get_post('filename') ? $ci->input->get_post('filename') : '';
	$filename .= date('Ymd');

	$ci->load->library('PHPExcel');
	$objPHPExcel = new PHPExcel();
	//PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip);
	
	$objPHPExcel->getDefaultStyle()->getFont()->setName("맑은 고딕")->setSize(10);
	$objPHPExcel->setActiveSheetIndex(0);
	$activeSheet = $objPHPExcel->getActiveSheet();
	
	// 시트 이름 설정
	$activeSheet->setTitle($filename);
	
#log_message('debug', __METHOD__ . ': 사진포함 = ' . $incPhoto);	
	// 테이블 헤더
	$row = 1;
	$colCnt = 0;
	if($incPhoto) {
		$colChar = PHPExcel_Cell::stringFromColumnIndex($colCnt++);
		$activeSheet->getColumnDimension($colChar)->setWidth(30);
		$activeSheet->setCellValue($colChar.$row, '사진');
		$activeSheet->getStyle($colChar.$row)
			->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setARGB('FFA0A0A0');
	}
	foreach($excelInfo['colModel'] as $key => $cm) {
		$colChar = PHPExcel_Cell::stringFromColumnIndex($colCnt++);
		$activeSheet->getColumnDimension($colChar)->setWidth($cm['excel-width']);
		$activeSheet->setCellValue($colChar.$row, $cm['name']);
		$activeSheet->getStyle($colChar.$row)
			->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setARGB('FFA0A0A0');
	}
	
	
	// 정보확장함수 체크
	if(!empty($excelInfo['extendFunc'])) {
		foreach($excelInfo['extendFunc'] as $func)
			$ci->load->model($func[0]);
	}
	

	$row = 2;
#log_message('debug', __METHOD__ . ': ' . $excelInfo['sql']);
	//$rows = ezdb_select($excelInfo['sql']);
	$sth = ezdb_query($excelInfo['sql']);
//log_message('debug', __METHOD__ . ': ' . $excelInfo['sql']);
//log_message('debug',  __METHOD__ . ': ' . var_export($sth,true));
	//foreach($rows as $arr) {

	$startCol = ($incPhoto==0) ? 'A' : 'B';
	while($arr = ezdb_fetch($sth)) {
#log_message('debug',  __METHOD__ . ': ' . var_export($arr,true));
		if(!empty($excelInfo['extendFunc'])) {
			foreach($excelInfo['extendFunc'] as $func)
				$ci->$func[0]->$func[1]($arr);
		}
		
		$filteredArr = filterArray($arr, $excelInfo['colModel']);
#log_message('debug', __METHOD__ . ': ' . var_export($filteredArr,true));
		$fromArrayData = array($filteredArr);
		$activeSheet->fromArray($fromArrayData, ' ', "{$startCol}{$row}");
#log_message('debug',  __METHOD__ . ': ' . var_export($arr,true));		
		//=================== $images[] = $arr['profile_photo_src'];
//log_message('debug', __METHOD__ . ': ' . FCPATH . $arr['profile_photo_src']);		
		$row++;
		
		unset($fromArrayData);
		//if($row>=290) break;
	}
/* ===========================
	// 사진처리
	if($incPhoto) {
		$row = 2;
		$objDrawingPType = new PHPExcel_Worksheet_Drawing();
		$objDrawingPType->setWorksheet($activeSheet);
		foreach($images as $idx => $image) {
			
			$objDrawingPType->setName("Pareto By Type");
			$objDrawingPType->setPath(FCPATH . $image);
			$objDrawingPType->setCoordinates("A".$row++);
			$objDrawingPType->setHeight(80);
			$objDrawingPType->setOffsetX(1);
			$objDrawingPType->setOffsetY(5);
			
log_message('debug', __METHOD__ . ': ' . FCPATH . $image);
		}
		unset($objDrawingPType);
	}
=========================== */
//log_message('debug',  __METHOD__ . ': ' . ezdb_error_message());
	$excelInfo['sth'] = null;

$debug = false;
if($debug==false) {
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename='. $filename .'.xlsx');
	header('Cache-Control: max-age=0');
}
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	// 계산 기능 방지
	$objWriter->setPreCalculateFormulas(false);
	
	$objWriter->save('php://output');
	
	// garbage collection
	$objPHPExcel->disconnectWorksheets();
	unset($objPHPExcel);
}


// 인사사진(images)가 설정된 경우 맨 첫컬럼에 사진이 나온다
function downloadExcelByDB2($ci, $excelInfo, $filename)
{
	ini_set('memory_limit', '-1');
	set_time_limit ( 0 );

	$filename = $ci->input->get_post('filename') ? $ci->input->get_post('filename') : '';
	$filename .= date('Ymd') . '.xlsx';

	//$ci->load->library('Spout');
	$objPHPExcel = Box\Spout\Writer\WriterFactory::create(Box\Spout\Common\Type::XLSX);
	
	$defaultStyle = (new Box\Spout\Writer\Style\StyleBuilder())
						->setFontSize(10)
						->build();
						
	$headerStyle = (new Box\Spout\Writer\Style\StyleBuilder())
						->setFontBold()
						->setFontSize(12)
						->setFontColor(Box\Spout\Writer\Style\Color::WHITE)
						->setBackgroundColor(Box\Spout\Writer\Style\Color::toARGB('A0A0A0'))
						->build();
	
	//$objPHPExcel->setDefaultRowStyle($defaultStyle)->openToFile('php://output');
	$objPHPExcel->openToBrowser($filename);


	$filteredArr = filterHeaderArr($excelInfo['colModel']);
	$objPHPExcel->addRowWithStyle($filteredArr, $headerStyle);
		
	// 정보확장함수 체크
	if(!empty($excelInfo['extendFunc'])) {
		foreach($excelInfo['extendFunc'] as $func)
			$ci->load->model($func[0]);
	}
	

#log_message('debug', __METHOD__ . ': ' . $excelInfo['sql']);
	//$rows = ezdb_select($excelInfo['sql']);
	$sth = ezdb_query($excelInfo['sql']);
//log_message('debug', __METHOD__ . ': ' . $excelInfo['sql']);
//log_message('debug',  __METHOD__ . ': ' . var_export($sth,true));

	while($arr = ezdb_fetch($sth)) {
#log_message('debug',  __METHOD__ . ': ' . var_export($arr,true));
		if(!empty($excelInfo['extendFunc'])) {
			foreach($excelInfo['extendFunc'] as $func)
				$ci->$func[0]->$func[1]($arr);
		}
		
		$filteredArr = filterArr($arr, $excelInfo['colModel']);
#log_message('debug', __METHOD__ . ': ' . var_export($filteredArr,true));
		$objPHPExcel->addRow($filteredArr);
#log_message('debug',  __METHOD__ . ': ' . var_export($arr,true));		
		//=================== $images[] = $arr['profile_photo_src'];
//log_message('debug', __METHOD__ . ': ' . FCPATH . $arr['profile_photo_src']);		
		
		unset($fromArrayData);
		//if($row>=290) break;
	}

//log_message('debug',  __METHOD__ . ': ' . ezdb_error_message());
	$excelInfo['sth'] = null;
/*
$debug = false;
if($debug==false) {
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename='. $filename .'.xlsx');
	header('Cache-Control: max-age=0');
}
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	$objWriter->save('php://output');
	*/

	$objPHPExcel->close();
}

function filterHeaderArr($colModel)
{
	$filteredArr = array();
	foreach($colModel as $key=>$cm) {
		if($cm['excel-inc']!=true) continue;
		
		$filteredArr[] = $cm['name'];
	}

	return $filteredArr;
}

function filterArr($row, $colModel)
{
	$filteredArr = array();
	foreach($colModel as $key=>$cm) {
		if($cm['excel-inc']!=true || !isset($row[$key])) continue;
		
		$filteredArr[] = $row[$key];
	}

	return $filteredArr;
}

function returnJson($arg, $contentType='application/json')
{
	$i=0;
	foreach($arg as $key => $val)
	{
		$argMsg[$i] = '"'.$key.'": ';
		if(is_numeric($val)) $argMsg[$i] .= $val;
		else if(is_array($val)) $argMsg[$i] .= json_encode($val);
		else $argMsg[$i] .= '"'.$val.'"';
		$i++;
	}
	$jsonMsg = '{' . implode(', ', $argMsg) . '}';
	
	header("Content-type: {$contentType}; charset=UTF-8");
	echo $jsonMsg;
}

function returnDataJson($arg, $contentType='application/json')
{
	header("Content-type: {$contentType}; charset=UTF-8");
	echo json_encode($arg);
}


// API Call을 할 때 사용할 수 있음
function httpRequest($url, $requestData, $contentType='application/json')
{
	$cs = curl_init($url);
	
	if($contentType=='application/json') $reqData = json_encode($requestData);
	else $reqData = $requestData;
	
	curl_setopt_array($cs, array(
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $reqData,
		CURLOPT_HTTPHEADER => array('Content-Type: '.$contentType),
		CURLOPT_RETURNTRANSFER => true
	));
	$responseData = curl_exec($cs);
	curl_close($cs);
	
	return json_decode($responseData, true);
}


// null이면 replace로 변환, null이 아니면 replace2로 변환
function nvl($val, $replace='', $replace2='')
{
	if( !isset($val) || is_null($val) || $val === '' )  return $replace;
	else return ($replace2=='') ? $val : $replace2;
}

// $row 베열에서 $val이 정의되어 있는지 체크
function anvl($row, $val, $replace='')
{
	if( !isset($row[$val]) || is_null($row[$val]) || $row[$val] === '' )  return $replace;
	else return $row[$val];
}

/**
* Returns SQL WHERE clause
* @param string $col sql column name
* @param string $oper operator from jqGrid
* @param string $val value (right hand side)
*/
function jqGridGetWhereClause($col, $oper, $val)
{
	$ops = array(
		'eq'=>'=', //equal
		'ne'=>'<>',//not equal
		'lt'=>'<', //less than
		'le'=>'<=',//less than or equal
		'gt'=>'>', //greater than
		'ge'=>'>=',//greater than or equal
		'bw'=>'LIKE', //begins with
		'bn'=>'NOT LIKE', //doesn't begin with
		'in'=>'LIKE', //is in
		'ni'=>'NOT LIKE', //is not in
		'ew'=>'LIKE', //ends with
		'en'=>'NOT LIKE', //doesn't end with
		'cn'=>'LIKE', // contains
		'nc'=>'NOT LIKE'  //doesn't contain
	);   
	if($oper == 'bw' || $oper == 'bn') $val .= '%';
	if($oper == 'ew' || $oper == 'en' ) $val = '%'.$val;
	if($oper == 'cn' || $oper == 'nc' || $oper == 'in' || $oper == 'ni') $val = '%'.$val.'%';
	return "$col {$ops[$oper]} '$val'";
}

function jgGridGetWhereClauseMultiple($filtersEnc)
{
	$filters = json_decode($filtersEnc, true);
	
	$where = array();
	foreach($filters['rules'] as $rule) {
		$where[] = jqGridGetWhereClause($rule['field'], $rule['op'], $rule['data']);
	}
	$w = '(' . implode(" {$filters['groupOp']} ", $where) . ')';
	return $w;
}

// mode: key 유일한 키 배열 리턴, 아니면 원본 유일한 배열 리턴
// 참조: unique_multidim_array() -> http://php.net/manual/kr/function.array-unique.php
function get_unique_array($array, $key, $mode='key')
{
	$temp_array = array();
	$i = 0;
	$key_array = array();
	
	foreach($array as $val){
		if(!in_array($val[$key],$key_array)){
			$key_array[$i] = $val[$key];
			$temp_array[$i] = $val;
		}
		$i++;
	}
	return ($mode=='key') ? $key_array : $temp_array;
}


////////////////////////////////////////////////////////////////////////////////////////////////////
//	
function AddHeadZero($Length, $String)
{
	$StrZero	=	"";
	switch(STRLEN($String))
	{
		case $Length:
			RETURN $String;
			break;
		default:
			for($Cnt = 0; ($Length - STRLEN($String)) > $Cnt ; $Cnt++)
			{
				$StrZero	=	$StrZero."0";
			}//	end function
			$String		=	$StrZero.$String;
			RETURN $String;
	}//	end switch
}//	end Function
////////////////////////////////////////////////////////////////////////////////////////////////////	

////////////////////////////////////////////////////////////////////////////////////////////////////
//	 
function mobileNumberFormat($mobileNumber)
{
	$mobileNumber	=	preg_replace("/[^0-9]*/s", "", $mobileNumber);
	switch(strlen($mobileNumber))
	{
		case 10:
			$rtn_mobile	=	substr($mobileNumber,0,3)."-".substr($mobileNumber,3,3)."-".substr($mobileNumber,6,4);
			break;
		case 11:
			$rtn_mobile	=	substr($mobileNumber,0,3)."-".substr($mobileNumber,3,4)."-".substr($mobileNumber,7,4);
			break;
		case 0:
			$rtn_mobile	=	"";
			break;
		default:
			$rtn_mobile	=	$mobileNumber;
	}//	end switch
	
	return $rtn_mobile;
}//	end function	
////////////////////////////////////////////////////////////////////////////////////////////////////	

////////////////////////////////////////////////////////////////////////////////////////////////////	
function dateNumberForm($str_number, $delimiter)
{
	switch(strlen($str_number))
	{
		case 0:
			$rtnValue	=	"noData";
			break;
		case 8:
			$rtnValue	=	substr($str_number,0,4).$delimiter.substr($str_number,4,2).$delimiter.substr($str_number,6,2);
			break;
		default:
			$rtnValue	=	$str_number."[format error]";
	}//	end switch
	
	return $rtnValue;
	
}//	end function
////////////////////////////////////////////////////////////////////////////////////////////////////	

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//	입력 인수값 정의
//	$datetime1	=	"2009-10-11"; - 시작일
//	$datetime2	=	"2009-10-12"; - 종료일
//	$opt		=	원하는 출력 포맷 ( 'd' 일자단위  )
function getDateTimeDiff( $datetime1, $datetime2, $opt='d' )
{	
	$rtnVal	=	null;

	$obj_datetime1	=	new DateTime( $datetime1 );
	$obj_datetime2	=	new DateTime( $datetime2 );
	
	// echo $obj_datetime1."<BR>";
	// echo $obj_datetime2."<BR>";
	
	$interval	=	$obj_datetime2->diff($obj_datetime1);
	
	switch($opt)
	{
		case 'y':	$rtnVal	=	$interval->format('%y');  	break;
		case 'm':	$rtnVal	=	$interval->format('%m');	break;
		case 'd':	$rtnVal	=	$interval->format('%d');	break;
		case 'h':	$rtnVal	=	$interval->format('%h');	break;
		case 'i':	$rtnVal	=	$interval->format('%i');	break;
		case 's':	$rtnVal	=	$interval->format('%s');	break;
		default:
			$rtnVal	=	var_dump( $interval );
	}//	end switch
	
	//	var_dump( $interval );

	return $rtnVal;
}//	end function
//
////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////
//
function dateNumberForm_1($dateTime)
{
	$rtnValue	=	"";
	if(substr($dateTime,0,10) == date("Y-m-d"))
	{
		$rtnValue	=	substr($dateTime, 11 , 8);
	}else{
		$rtnValue	=	substr($dateTime, 0 , 10);
	}//	end if
	
	return $rtnValue;
}//	end function	
////////////////////////////////////////////////////////////////////////////////////////////////////

function getCodeByName($step, $name, $col='value', $level=1)
{
	$row = ezdb_select_one("SELECT * FROM swcms_code WHERE step='{$step}' AND level={$level} AND {$col}='{$name}'");
	if(empty($row)) return '';
	
	return $row['code'];
}

// col : name, ename만 가능함
function getCountryCodeByName($name, $col='name')
{
	$row = ezdb_select_one("SELECT * FROM swcms_country WHERE {$col}='{$name}'");
	if(empty($row)) return '';
	
	return $row['code'];
}

////////////////////////////////////////////////////////////////////////////////////////////////////	
//	html select Box option
function makeOptionList($arrValue, $arrText, $selectedVal)
{		
	$rtnOption	=	"";
	for($cnt = 0; COUNT($arrValue) > $cnt; $cnt++)
	{
		if($arrValue[$cnt] == $selectedVal)
		{
			$rtnOption	=	$rtnOption."<option value=\"".$arrValue[$cnt]."\" selected=\"selected\">".$arrText[$cnt]."</option>\n";
		}else{
			$rtnOption	=	$rtnOption."<option value=\"".$arrValue[$cnt]."\">".$arrText[$cnt]."</option>\n";
		}//	end if
	}//	end for
	
	return $rtnOption;
}//	end function

function makeSelectOptionList($arr, $selectedVal='') 
{
	$rtnOption	=	"";
	foreach($arr as $val=>$txt)
	{
		$sel = ($val == $selectedVal) ? 'selected' : '';
		$rtnOption	=	$rtnOption."<option value='{$val}' {$sel}>{$txt}</option>\n";
	}
	
	return $rtnOption;
}

function makeOptionListByArray($arr, $valCol='', $txtCol='', $selectedVal='')
{
	$rtnOption	=	"";
	foreach($arr as $key=>$item)
	{
		$val = ($valCol=='') ? $key : $item[$valCol];
		$txt = ($txtCol=='') ? $item : $item[$txtCol];
		
		$sel = ($val == $selectedVal) ? 'selected' : '';
		$rtnOption .= "<option value='{$val}' {$sel}>{$txt}</option>\n";
	}
	
	return $rtnOption;
}

function makeOptionListByQuery($query, $valCol, $txtCol, $selectedVal='')
{
	$rows = ezdb_select($query);
	$arr = array();
	foreach($rows as $row) {
		$arr[$row[$valCol]] = $row[$txtCol];
	}
	
	$rtnOption	=	"";
	foreach($arr as $val=>$txt)
	{
		$sel = ($val == $selectedVal) ? 'selected' : '';
		$rtnOption	=	$rtnOption."<option value='{$val}' {$sel}>{$txt}</option>\n";
	}
	
	return $rtnOption;
}

function StringCutting($String, $Length, $Ext)
{
	
	//	$TempString = SUBSTR($String, 0, $Length);
	//	PREG_MATCH('/^([\x00-\x7e]|.{2})*/', $TempString, $RtnArray);
	//	$RtnValue = $RtnArray[0];
	
	$RtnValue = SUBSTR($String, 0, $Length);
	
	switch(STRLEN($String) > $Length)
	{
	case TRUE: $RtnValue = $RtnValue.$Ext;	break;
	case FALSE: $RtnValue = $RtnValue;		break;
	}// end switch

	return $RtnValue;

}// End function

// 바이트수. 글자수 아님
function strlen_utf8($str, $checkmb=true) 
{
	preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $str, $match); // target for BMP
 
	$m = $match[0];
	$mlen = count($m); // length of matched characters
 
	if (!$checkmb) return $mlen;
 
	$count=0;
	for ($i=0; $i < $mlen; $i++) {
		$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
	}
 
	return $count;
}

function strcut_utf8($str, $len, $checkmb=false, $tail='...')
{
	preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);
 
	$m    = $match[0];
	$slen = strlen($str);  // length of source string
	$tlen = strlen($tail); // length of tail string
	$mlen = count($m); // length of matched characters
 
	if ($slen <= $len) return $str;
	if (!$checkmb && $mlen <= $len) return $str;
 
	$ret   = array();
	$count = 0;
 
	for ($i=0; $i < $len; $i++) {
		$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
 
		if ($count + $tlen > $len) break;
		$ret[] = $m[$i];
	}
 
	return join('', $ret).$tail;
}	//	end function

// $start : 시작 바이트
// $len : 바이트 수
function substr_utf8($str, $start, $len, $checkmb=true)
{
	preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);
 
	$m    = $match[0];
//echo var_export($m, true) . '<br>';
	$slen = strlen($str);  // length of source string
	$mlen = count($m); // length of matched characters

	// 문자열 길이 체크
	if (!$checkmb) $strlen = $mlen;
	else {
		$count=0;
		for ($i=0; $i < $mlen; $i++) {
			$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
		}

		$strlen = $count;
	}
	if($start>$strlen) return '';

	$realstart = 0;
	$count = 0;
	if($realstart!=$start) {
		// $start까지 skip
		for ($i=0; $i < $mlen; $i++) {
			if($count > $start) break;
			else {
				$realstart = $i;
				if($count==$start) break;
				
				$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
			}
		}
	}

	$ret   = array();
	$count = 0;
	$end = min($realstart+$len, $mlen);
//echo "start = $start, realstart = $realstart, end = $end <br>";
	for ($i=$realstart; $i < $end; $i++) {
		$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
		if ($count > $len) break;
		$ret[] = $m[$i];
	}
 
	return join('', $ret);
}

function getOnlyNumberData($str)
{
	$rtnValue	=	preg_replace("/[^0-9]/", "", $str);
	return $rtnValue;
}	// end function

function fileWrite_mode_W($filePath, $fileData)
{
	// A. 동일경로에 존재하는 기존파일 삭제처리
	// B. fopen w 모드 	
	// 파일을 쓰기 모드로 연다.
	// 파일이 존재하면 기존내용을 지우고 그 위에 새로운 내용을 쓴다.
	// 그러나 존재하지 않은면 해당 파일을 생성한다.
	// 이때 파일 포인터는 해당 파일의 처음에 위치한다.
	
	$rtnValue	=	"FAIL";
	
	if( file_exists($filePath) )
	{
		unlink($filePath);	
	}// end if
	
	$fp	=	fopen($filePath, 'w');
			fwrite($fp, $fileData);
			fclose($fp);
			
	if( file_exists($filePath) )
	{
		$rtnValue	=	"SUCCESS";
	}// end if			
			
	return $rtnValue;
	
}// end function

function getUserID($userRecordNum)
{
	$qry = "
	SELECT p01_id
	FROM personal_01_info_default
	WHERE p01_recordNum = '$userRecordNum'
	";
	$ci =& get_instance();
	$res = $ci->db->query($qry);
	if($res===false) return false;
	
	$user = $res->row_array();
	
	return $user['p01_id'];
}	// end function

// 이미지의 위치를 읽어옴
function getImagePath($table, $col, $val='', $etc='1') 
{
	$debug = false;
	$imagePath = '';
	
	$ci =& get_instance();
	$ci->load->model('commonModel');
	$photoInfo = $ci->commonModel->getUploadConfig($table, $col);
	
if($debug) log_message('debug', __METHOD__ . ':' . var_export($photoInfo,true));
	if(!empty($val)) {
if($debug) log_message('debug', __METHOD__ . ':' . "SELECT * FROM {$photoInfo['file_table']} WHERE idx = {$val}");		
		if(!empty($photoInfo['file_table'])) {
			$row = ezdb_select("SELECT * FROM {$photoInfo['file_table']} WHERE idx = {$val}");
			if(empty($row)) return '';
		
			$filename = $row['unique_file_name'];
		} else $filename = $val;
	
		$imagePath = $photoInfo['upload_path'] . '/' . $filename;
		if($imagePath[0]=='.') $imagePath = substr($imagePath,1);
		$realImagePath = FCPATH . $imagePath;
if($debug) log_message('debug', __METHOD__ . ': ' . "{$col}, {$filename}, {$imagePath}, " . $realImagePath);
		if(file_exists($realImagePath) && is_file($realImagePath)){
if($debug) log_message('debug', __METHOD__ . ': image exist');
			return $imagePath;
		}
	}

	if(empty($photoInfo['default_image'])) return '';
	
	// 파일이 존재하지 않으면 디폴트 이미지 패스를 리턴함
if($debug) log_message('debug', __METHOD__ . ': image not exist');		
	if(is_array($photoInfo['default_image']))
		$imagePath =  isset($photoInfo['default_image'][$etc]) ? $photoInfo['default_image'][$etc] : '';
	else $imagePath = $photoInfo['default_image'];
if($debug) log_message('debug', __METHOD__ . ':' . $imagePath . ' - ' . $etc);
	return $imagePath;

}
	
function listGeneralFile($table, $col, $idx, $vals)
{
	if(empty($vals)) return '없음';
	
	$rows = ezdb_select("SELECT * FROM swcms_registration_file WHERE idx IN ({$vals})");

	$html = '';
	foreach($rows as $i => $row) {
		$html .= "
			<p>
				<a href='/common/downloadFile?idx={$row['idx']}'>{$row['real_file_name']}</a>
				<a href='javascript:removeFile(\"{$table}\",\"{$col}\",\"{$row['ret_idx']}\",\"{$row['idx']}\")'>
					<i class='fa fa-trash'></i>
				</a>
			</p>
		";
	}
	//$html .= '</ul>';
#log_message('debug', __METHOD__ . ': ' . $html);
	return $html;
}

function listImageFile($table, $col, $idx, $vals)
{
	if(empty($vals)) return '파일없음';
	
	$rows = ezdb_select("SELECT * FROM swcms_registration_file WHERE idx IN ({$vals})");
	$liList = $divList = '';
	foreach($rows as $i => $row) {
		$active = ($i==0) ? ' active' : '';
		$liList .= '<li data-target="#carousel-generic" data-slide-to="' . $i . '" class="' . $active . '"></li>';
		$src = $row['path'] . '/' . $row['unique_file_name'];
//						<!-- a href='{$src}' class='fancybox' rel='group'><span class='glyphicon glyphicon-zoom-in'></span></a -->
		$divList .= "
			<div class='item {$active}'>
				<a href='{$src}' class='fancybox' rel='group'>
					<img src='{$src}' class='thumbnail' width='140'>
				</a>
				<div class='carousel-caption'>
					<p> 
						<!-- {$row['real_file_name']} -->
						<a href='/common/downloadFile?idx={$row['idx']}' title='다운로드'>
							<span class='glyphicon glyphicon-download-alt'></span>
						</a>
						<a href='javascript:removeFile(\"{$table}\",\"{$col}\",\"{$row['ret_idx']}\",\"{$row['idx']}\")' title='삭제'>
							<span class='glyphicon glyphicon-remove'></span>
						</a>
					</p>
				</div>
			</div>
		";
	}
	$html = '
		<div id="carousel-generic" class="carousel slide" data-ride="carousel" style="width:140px">
			<ol class="carousel-indicators">'
			. $liList .
			'</ol>
			<div class="carousel-inner" role="listbox">'
			. $divList .
			'</div>
			<a class="left carousel-control" href="#carousel-generic" role="button" data-slide="prev">
				<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
				<span class="sr-only">이전</span>
			</a>
			<a class="right carousel-control" href="#carousel-generic" role="button" data-slide="next">
				<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
				<span class="sr-only">다음</span>
			</a>
		</div>
	';
	$js = '
		<script>
			$(document).ready(function(){
				$("a.fancybox").fancybox({
					"transitionIn"	: "elastic",
					"transitionOut"	: "elastic",
					"cyclic"		: true,
					"speedIn"		: 600,
					"speedOut"		: 200,
					"overlayShow"	: false
				});
			})
		</script>
	';
#log_message('debug', $html);
	return $html . $js;
}

function getRecentFile($nameList)
{
	$nameArr = explode(',', $nameList);
	if(count($nameArr)==0) return '';
	
	return $nameArr[count($nameArr)-1];
}
?>
