<?php
class My_Helper_Excelexport extends Zend_Controller_Action_Helper_Abstract {
	private $_exportPath = "";
	
	public function export(array $data, array $fieldsMap, $format = 'xls', $exportPath = null, $downloadFile = true) {
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Aplikacja Mailbox")
							 ->setTitle("Eksport danych");
     	$objPHPExcel->setActiveSheetIndex(0);
		
		$activeSheet = $objPHPExcel->getActiveSheet();
		
		$columnNumber = 0;
		$rowNumber = 1;
		// zapisanie w pierwszym wierszu nazw kolumn (kluczy podtablicy)
		$row = reset($data);
		foreach (array_keys($row) as $columnName) {
			if(array_key_exists($columnName, $fieldsMap)) {
				// zamiana nazwy pola na etykiete z tablicy mapujacej
				$columnName = $fieldsMap[$columnName];
			}
			$activeSheet->setCellValueByColumnAndRow($columnNumber, $rowNumber, $columnName);
			$columnNumber++;
		}
		
		$rowNumber = 2;
		
		foreach($data as $rowKey=>$row) {
			$columnNumber = 0;

			foreach($row as $cellKey=>$cell) {
					
				// obsluga przypadkow szczegolnych
				if($cellKey == 'city' || $cellKey == 'group') {
					$cell = join("\n", $cell);
				}
					
				if($cellKey == 'send_emails' || $cellKey == 'open_emails') {
					$cell = $cell[0];
				}
					
				$activeSheet->setCellValueByColumnAndRow($columnNumber, $rowNumber, $cell);
				$columnNumber++;
			}
			
			$rowNumber++;
			
		} // end foreach $data
		
		if($format == 'xls') {
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		} elseif($format == 'csv') {
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		} else {
			throw new Exception("Nieznany format dla eksportu.");
		}
		
		$filename = 'export_' . date("Y-m-d_H-i-s") . '.xls';
		
		if(!$downloadFile && $exportPath != null) {
			// zapisanie na dysk
			$objWriter->save($exportPath . '/' . $filename);
			return $filename;
			
		} else {
		
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			
			$objWriter->save('php://output');
			exit();
		}

	
	} // end export

}