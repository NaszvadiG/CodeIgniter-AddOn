<?php

function phpexcelReadFile($filename, $sheet=0)
{
	$objReader = PHPExcel_IOFactory::createReaderForFile($filename);
	$objReader->setReadDataOnly(true);
	$objPHPExcel = $objReader->load($filename);
	
	$sheet = $objPHPExcel->getSheet($sheet);
	
	$highestRow = $sheet->getHighestRow(); 
	$highestColumn = $sheet->getHighestColumn();
	$rows = array();
	for($row=1; $row<=$highestRow; $row++){ 
		$arr = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, '', TRUE, FALSE);
		$rows[] = $arr[0];
	}
//log_message('debug', __METHOD__ . ': ' . var_export($rows,true));
	return $rows;
}
