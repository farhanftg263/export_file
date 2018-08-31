<?php
/*
 * Function : Export csv and pdf file
 * Author : Farhan
 * Created_on : 2018-08-31
 */
require_once(ROOT .DS. 'vendor' . DS  . 'tecnickcom' . DS . 'tcpdf' . DS .'examples'.DS. 'tcpdf_include.php');
// extend TCPF with custom functions
class MYPDF extends TCPDF 
{
    // Colored table
    public function Table($headers,$data) 
    {
        // Colors, line width and bold font
        $this->SetFillColor(0, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
    
        // Header
        $w = array(40, 35, 40, 45);
        $num_headers = count($headers);
        foreach ($headers as $header )
        {
            $this->Cell($header['width'], 7, $header['label'], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        foreach ($data as $record)
        {
            foreach ($headers as $k => $header)
            {
                $this->Cell($header['width'], 6, $record[$k], 'LR', 0, 'L', $fill);
            }
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

class Export extends MYPDF
{
    /*
     * Export PDF
     */
    private function exportPdf($file_name,$header,$data,$title)
    {
        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Farhan');
        $pdf->SetTitle('Export PDF');
        $pdf->SetSubject('Listing');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

        // set default header data
        $pdf->SetHeaderData('', '', $title, '');

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf->setLanguageArray($l);
        }
        // set font
        $pdf->SetFont('helvetica', '', 12);
        // add a page
        $pdf->AddPage();
        // print colored table
        $pdf->Table($header, $data);
        // close and output PDF document
        $pdf->Output($file_name.'.pdf', 'I');
    }
    
    /*
     * Export CSV
     */
    private function exportCsv($file_name,$header,$data)
    {
        ini_set('max_execution_time', 1600); //increase max_execution_time to 10 min if data set is very large
        $fileContent = implode("\t ", $header)."\n";
        foreach($data as $result) {
           if(is_object($result))
           {
               $result = json_decode(json_encode($result), true);
           }
           $fileContent .=  implode("\t ", $result)."\n";
        }
        header('Content-type: application/ms-csv'); /// you can set csv format
        header('Content-Disposition: attachment; filename='.$file_name.'.csv');
        echo $fileContent;
        exit;
    }
    /*
     * Export Excel
     */
    function exportExcel($file_name,$header,$data)
    {
        $objPHPExcel = new \PHPExcel();
        $col = 0;
        foreach($header as $head)
        {
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $head);
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
            $col++;
        }
        $row = 2;
        foreach($data as $d)
        {
            $col = 0;
            foreach($d as $val)
            {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $val);
                $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(30);
                $col++;
            }
            $row++;
        }
        foreach(range('A','AF') as $columnID) 
        {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }
        $objPHPExcel->getActiveSheet()->getStyle('A1:AF1')->getFont()->setBold(true); //Make heading font bold
        $objPHPExcel->getActiveSheet()->setTitle('Report'); //give title to sheet
        
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;Filename=$file_name.xls");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    
    /*
     * Export file
     */
    public function exportFile($file_name,$header,$data,$ext = FILE_CSV,$title = '')
    {
        if(!empty($data))
        {
            if($ext == FILE_CSV)
            {
                $this->exportCsv($file_name,$header,$data);
            }
            
            if($ext == FILE_PDF)
            {
                $this->exportPdf($file_name,$header,$data,$title);
            }
            
            if($ext == FILE_EXCEL)
            {
                $this->exportExcel($file_name,$header,$data,$title);
            }
        }
    }    
}