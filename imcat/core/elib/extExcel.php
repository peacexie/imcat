<?php
namespace imcat;

// 数据导出成Excel文件,暂不支持中文文件名称。尽量生成UTF-8编码的excel
// 包括从数据库取出来的数据转成UTF-8,测试时某些WPS版本只支持UTF-8的编码

/**
* 导出 XML格式的 Excel 数据
*/
class extExcel{

    /*
    $head1 = array('AA1','BB1');
    $data1 = array(array('1a','1ba'),array('1c','1d'));
    $head2 = array('AA2','BB2');
    $data2 = array(array('2a','2bb'),array('2c','2d'));
    a: ($head1,$data1,1);
    $head = array(head1,head2,...);
    $data = array(data1,data2,...);
    b: ($head,$data,2);
    */
    static function exWrite($head,$data,$tcnt=1,$encode='utf-8',$pre='Excel_'){
        include_once DIR_VENDOR.'/Excel/writer.php'; 
        $xls = new \ExcelWriter($encode); //默认UTF-8编码
        $xls->generateXMLHeader($pre.date('Y-md-His'));  //excel文件名
        if($tcnt==1){
            $xls->setTable($head,$data);
        }else{
            foreach ($head as $key => $val) {
                $xls->setTable($val,$data[$key]);
            }
        }
        $xls->generateXMLFoot();
    }

    // $data = exRead('./@note/163data-1zz.xls','gbk'); 
    static function exRead($file, $encode='utf-8', $rtb=0){ 
        include_once DIR_VENDOR.'/Excel/reader.php'; 
        $data = new \Spreadsheet_Excel_Reader(); 
        $data->setOutputEncoding($encode);
        $data->read($file); 
        $sheets = $data->sheets; 
        return isset($sheets[$rtb]) ? $sheets[$rtb] : $sheets;
    }

    // 建议用: exRead轻量的类
    static function peRead($file, $encode='utf-8', $rtb=0){ 
        include_once DIR_VENDOR.'/Excel/PHPExcel.php'; // 自行下载PHPExcel到相关目录
        $type = strpos(strtolower($file),'.xlsx') ? 'Excel2007' : 'Excel5';
        $Reader = \PHPExcel_IOFactory::createReader($type); 
        $Reader->setReadDataOnly(true);
        $PHPExcel = $Reader->load($file);
        $sheet = $PHPExcel->getSheet($rtb); //$PHPExcel->getActiveSheet();
        $hRow = $sheet->getHighestRow(); 
        $hColumn = $sheet->getHighestColumn(); 
        $hCIndex = \PHPExcel_Cell::columnIndexFromString($hColumn); 
        $data = ['hColumn'=>$hColumn, 'numRows'=>$hRow, 'numCols'=>$hCIndex]; $cells = []; 
        for($row = 1; $row <= $hRow; $row++) { 
            for ($col = 0; $col < $hCIndex; $col++) { 
                $cells[$row][] = (string)$sheet->getCellByColumnAndRow($col, $row)->getValue();
           } 
        } 
        $data['cells'] = $cells;
        return $data;
    }


}

/*
    ### ExcelReader 小问题：

    (1)出现Deprecated: Function split() is deprecated in 。。。错误
      解决：将excel_reader2.php源码中split改为explode,详情点击php中explode与split的区别介绍

    (2)出现Deprecated: Assigning the return value of new by reference is deprecated in错误
      解决：将excel_reader2.php源码中$this->_ole =& new OLERead()中 &去掉，因为php5.3中废除了=& 符号直接用=引用
*/
