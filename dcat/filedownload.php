<?php
    ob_start();
    include("config.php");

    require_once 'PHPExcel.php';
    require_once 'PHPExcel/IOFactory.php';

    $projectid = $_GET['projectid'];
    $fileid = $_GET['fileid'];
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Create a first sheet, representing sales data

    $objPHPExcel->setActiveSheetIndex(0);
    // Create a new worksheet, after the default sheet
    $objPHPExcel->createSheet();
    $sql = pg_query("select id, name from themes where project_id = $projectid");
    while($row_val1 = pg_fetch_array($sql)) {
        $row_dynamic_col[] = $row_val1['name'];
    }

    // Add some data to the second sheet, resembling some different data types
    $sql_detail = pg_query("select * from return_theme_based_data_json($projectid,$fileid)");

    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Date');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Full Text');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'URL');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', 'Domain');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', 'Page Type');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', 'Language');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', 'Country');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', 'Author');
    $col = 9;
    foreach ($row_dynamic_col as $key => $value) {
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $row_dynamic_col[$key]);
        $col++;
    }

    $i=2;
    while($row = pg_fetch_array($sql_detail)) {
        $decode = json_decode($row['result'],true);
        // print_r($decode);
        for($j=0;$j<count($decode);$j++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $decode['date']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $decode['full_text']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $decode['url']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $decode['domain']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $decode['page_type']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $decode['language']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $decode['country']);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $decode['author']);
            $col = 9;
            foreach ($row_dynamic_col as $key => $value) {
                $pos = $row_dynamic_col[$key];
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $i, $decode[$pos]);
                $col++;
            }
        }
        $i++;
    }
    // Rename 2nd sheet
    $objPHPExcel->getActiveSheet()->setTitle('Sheet 1');

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="ProcessedExcel.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
?>