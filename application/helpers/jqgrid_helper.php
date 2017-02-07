<?php

function jqgridGetParam($defaultSidx, $defaultSord='DESC', $defaultRows=50)
{
	$param['page'] = empty($_REQUEST['page']) ? 1 : $_REQUEST['page'];
	$param['rows'] = empty($_REQUEST['rows']) ? $defaultRows : $_REQUEST['rows']; // rows per page
	$param['sidx'] = empty($_REQUEST['sidx']) ? $defaultSidx : $_REQUEST['sidx'];
	$param['sord'] = empty($_REQUEST['sord']) ? $defaultSord : $_REQUEST['sord'];
	
	return $param;
}

function jqgridInitResp($param)
{
	$resp['total'] = ($param['count']>0) ? ceil($param['count']/$param['rows']) : 0;
	$resp['page'] = ($param['page'] > $resp['total']) ? $resp['total'] : $param['page'];
	if($resp['page'] < 1) $resp['page'] = 1;
	$resp['records'] = $param['count'];
	
	return $resp;
}

function jqgridEmptyResp()
{
	$resp['total'] = 0;
	$resp['page'] = 1;
	$resp['records'] = 0;
	
	return $resp;
}

function jqgridGetStart($param, $resp)
{
	if(empty($param['rows']) || empty($resp['page'])) return false;
	
	return $param['rows'] * ($resp['page'] - 1);
}

function jqgridDownloadExcel($dataInfo)
{
	ini_set('memory_limit', '1000M');
	set_time_limit ( 0 );
	
	$ci =& get_instance();
	$ci->load->library('PHPExcel');
	$objPHPExcel = new PHPExcel();
	//PHPExcel_Settings::setCacheStorageMethod(PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip);
	
	$objPHPExcel->getDefaultStyle()->getFont()->setName("맑은 고딕")->setSize(10);
	$objPHPExcel->setActiveSheetIndex(0);
	$activeSheet = $objPHPExcel->getActiveSheet();
	
	$activeSheet->setTitle($dataInfo['filename']);

	// 테이블 헤더
	$row = 1;
	$colCnt = 0;
	foreach($dataInfo['colModel'] as $cm) {
		$colChar = PHPExcel_Cell::stringFromColumnIndex($colCnt++);
		$width = empty($cm['width']) ? 30 : round($cm['width'] / 3);
		$activeSheet->getColumnDimension($colChar)->setWidth($width);
		$activeSheet->setCellValue($colChar.$row, $cm['label']);
		$activeSheet->getStyle($colChar.$row)
			->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()->setARGB('FFA0A0A0');
	}

	// 정보확장함수 체크
	if(!empty($dataInfo['extendFunc'])) {
		foreach($dataInfo['extendFunc'] as $func)
			$ci->load->model($func[0]);
	}

	// 실제 데이터 읽어오기
	$row = 2;
	$sth = ezdb_query($dataInfo['sql']);
	while($arr = ezdb_fetch($sth)) {
#log_message('debug',  __METHOD__ . ': ' . var_export($arr,true));
		if(!empty($dataInfo['extendFunc'])) {
			foreach($dataInfo['extendFunc'] as $func)
				$ci->$func[0]->$func[1]($arr);
		}
		
		$activeSheet->fromArray(array(filterArray($arr, $dataInfo['colModel'])), ' ', "A{$row}");
#log_message('debug',  __METHOD__ . ': ' . var_export($arr,true));		
		$row++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename='. $dataInfo['filename'] .'.xlsx');
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

	// 계산 기능 방지
	$objWriter->setPreCalculateFormulas(false);
	
	$objWriter->save('php://output');
	
	// garbage collection
	$objPHPExcel->disconnectWorksheets();
	unset($objPHPExcel);
}

function filterArray($row, $colModel)
{
	$filteredArr = array();
	foreach($colModel as $key=>$cm) {
		$key = $cm['index'];
		if(!isset($row[$key])) continue;
		
		$filteredArr[$key] = $row[$key];
	}

	return $filteredArr;
}

function jqgridParseFilters($filters)
{
	$whereArray = array();
	
	$groupOperation = $filters->groupOp;
	$rules = $filters->rules;
	foreach($rules as $rule) {
		$fieldName = $rule->field;
		$fieldData = $rule->data;
		switch ($rule->op) {
			case "eq":
				$fieldOperation = " = '".$fieldData."'";
				break;
			case "ne":
				$fieldOperation = " != '".$fieldData."'";
				break;
			case "lt":
				$fieldOperation = " < '".$fieldData."'";
				break;
			case "gt":
				$fieldOperation = " > '".$fieldData."'";
				break;
			case "le":
				$fieldOperation = " <= '".$fieldData."'";
				break;
			case "ge":
				$fieldOperation = " >= '".$fieldData."'";
				break;
			case "nu":
				$fieldOperation = " = ''";
				break;
			case "nn":
				$fieldOperation = " != ''";
				break;
			case "in":
				$fieldOperation = " IN (".$fieldData.")";
				break;
			case "ni":
				$fieldOperation = " NOT IN '".$fieldData."'";
				break;
			case "bw":
				$fieldOperation = " LIKE '".$fieldData."%'";
				break;
			case "bn":
				$fieldOperation = " NOT LIKE '".$fieldData."%'";
				break;
			case "ew":
				$fieldOperation = " LIKE '%".$fieldData."'";
				break;
			case "en":
				$fieldOperation = " NOT LIKE '%".$fieldData."'";
				break;
			case "cn":
				$fieldOperation = " LIKE '%".$fieldData."%'";
				break;
			case "nc":
				$fieldOperation = " NOT LIKE '%".$fieldData."%'";
				break;
			default:
				$fieldOperation = "";
				break;
		}
		if($fieldOperation != "") $whereArray[] = $fieldName.$fieldOperation;
	}
	if(count($whereArray)>0)
		$where = ' AND ' . join(" ".$groupOperation." ", $whereArray);
	else $where = '';

	return $where;
}

function jqgridMakeOptionListByArray($arr, $valCol='', $txtCol='')
{
	$rtnOption	=	"";
	foreach($arr as $key=>$item)
	{
		$val = ($valCol=='') ? $key : $item[$valCol];
		$txt = ($txtCol=='') ? $item : $item[$txtCol];
		
		if($rtnOption!='') $rtnOption .= ';';
		$rtnOption	.=	"{$val}:{$txt}";
	}
	
	return $rtnOption;
}

function jqgridMakeEditOptionByQuery($query, $valCol, $txtCol)
{
	$rows = ezdb_select($query);
	$arr = array();
	foreach($rows as $row) {
		$arr[$row[$valCol]] = $row[$txtCol];
	}
	
	$rtnOption	=	"";
	foreach($arr as $val=>$txt)
	{
		if($rtnOption!='') $rtnOption .= ';';
		$rtnOption	.=	"{$val}:{$txt}";
	}
	
	return $rtnOption;
}
