<?php
class bookingExportForm extends Form {
    function __construct(){ }

    function draw(){

		//default
		$cart = array(
			1 => array(
				'type' => 1,
				'fullname' => "Hà",
				'email' => "lymanhha@gmail.com",
				'phone' => "0906122309",
				'address' => "Phạm Ngọc Thạch",
				'start_time' => 1461257999,
				'created' => 1460480400,
				'tour_id' => 1,
				'title' => "Cáp treo"
			),
			2 => array(
				'type' => 0,
				'fullname' => "Hà PAM",
				'email' => "lymanhha@gmail.com",
				'phone' => "0906122309",
				'address' => "Phạm Ngọc Thạch",
				'start_time' => 1461257999,
				'created' => 1460480400,
				'tour_id' => 2,
				'title' => "Tour du lịch"
			)
		);

        /** PHPExcel */
		include ROOT_PATH.'includes/PHPExcel.php';
		
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set properties
		$userName = User::username();
		$objPHPExcel->getProperties()->setCreator($userName)
									 ->setLastModifiedBy($userName);
		
		$arrayTitle = array(
			'A1'	=> 'ID',
			'B1'	=> 'Phân loại',
			'C1'	=> 'Họ tên',
            'D1'	=> 'Email',
            'E1'	=> 'Điện thoại',
            'F1'	=> 'Ngày dự đi',
            'G1'	=> 'Ngày booking',
            'H1'	=> 'Địa chỉ',
            'I1'	=> 'ID Tour',
            'J1'	=> 'Tên Tour',
		);
		$objPHPExcel->setActiveSheetIndex(0);
		
		// first row
		foreach ($arrayTitle as $k=>$a){
			$objPHPExcel->getActiveSheet()->SetCellValue($k, $a);
			$objPHPExcel->getActiveSheet()->getColumnDimension(substr($k, 0,1))->setAutoSize(true);
		}
		
		//fill item
		$line = 2;
		foreach ($cart as $k=>$i){
			$char = 'A';

			//id
			$objPHPExcel->getActiveSheet()->SetCellValue($char.$line, $k);
			$char++;

			//phan loai
			$objPHPExcel->getActiveSheet()->SetCellValue($char.$line, $i['type'] == 0 ? 'Tour' : 'Cáp treo');
			$char++;

            //ho ten
            $objPHPExcel->getActiveSheet()->SetCellValue($char.$line, $i['fullname']);
            $char++;

            //email
            $objPHPExcel->getActiveSheet()->SetCellValue($char.$line, $i['email']);
            $char++;

            //phone
            $objPHPExcel->getActiveSheet()->SetCellValue($char.$line, $i['phone']);
            $char++;

            //ngay di
            $objPHPExcel->getActiveSheet()->SetCellValue($char.$line, FunctionLib::dateFormat($i['start_time'], 'd/m/Y'));
            $char++;

			//ngay tao
			$objPHPExcel->getActiveSheet()->SetCellValue($char.$line, FunctionLib::dateFormat($i['created'],"d/m/Y"));
            $char++;

            //dia chi
            $objPHPExcel->getActiveSheet()->SetCellValue($char.$line, $i['address']);
            $char++;

            //tour ID
            $objPHPExcel->getActiveSheet()->SetCellValue($char.$line, $i['tour_id']);
            $char++;

            //ten tour
            $objPHPExcel->getActiveSheet()->SetCellValue($char.$line, $i['title']);
			$line++;
		}
		$file = "BookingList".date("-d/m/Y").".xls";
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$file.'"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		
		exit();
    }
}