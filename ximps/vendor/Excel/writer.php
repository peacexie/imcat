<?php
/**
* 导出 XML格式的 Excel 数据
*/
class ExcelWriter{

    // 文档头标签
    private $header = "<?xml version=\"1.0\" encoding=\"%s\"?\>\n<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\" xmlns:html=\"http://www.w3.org/TR/REC-html40\">";
    // 文档尾标签
    private $footer = "</Workbook>";

    // 内容编码
    private $sEncoding;
    // 是否转换特定字段值的类型
    private $bConvertTypes;
    // 生成的Excel内工作簿的个数
    private $cntSheet = 0;

    /** 构造函数
     * @param string $sEncoding 内容编码
     * @param boolean $bConvertTypes 是否转换特定字段值的类型
     */
    function __construct($sEncoding='UTF-8', $bConvertTypes=false){
        $this->bConvertTypes = $bConvertTypes;
        $this->sEncoding = $sEncoding;
    }
    // 
    function setTable($head,$data,$title=''){
        $this->worksheetStart($title);
        $this->setTableHeader($head);  //表字段名
        $this->setTableRows($data); //内容字段
        $this->worksheetEnd();
    }
   
    // 向客户端发送Excel头信息; $fname:文件名称,不能是中文 
    function generateXMLHeader($fname=''){
        $fname = $fname ? $fname : 'Book-'.date('md-His',$_SERVER["REQUEST_TIME"]); // preg_replace('/[^aA-zZ0-9\_\-]/', '', $fname)
        header("Pragma: public");   header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/vnd.ms-excel; charset={$this->sEncoding}");
        header("Content-Transfer-Encoding: binary");
        header("Content-Disposition: attachment; filename={$fname}.xls");
        echo stripslashes(sprintf($this->header, $this->sEncoding));
    }
    // 向客户端发送Excel结束标签
    function generateXMLFoot(){
        echo $this->footer;
    }
   
    // 开启工作簿
    function worksheetStart($title=''){
        $this->cntSheet++;
        $title = preg_replace("/[\\\|:|\/|\?|\*|\[|\]]/", "", empty($title) ? 'Sheet'.($this->cntSheet) : $title);
        echo "\n<Worksheet ss:Name=\"" . substr($title, 0, 31) . "\">\n<Table>\n";
    }
    // 结束工作簿
    function worksheetEnd(){
        echo "</Table>\n</Worksheet>\n";
    }
    // 设置表头信息
    function setTableHeader($header=array()){
        echo $this->_parseRow($header);
    }
    // 设置表内行记录数据
    function setTableRows($rows=array()){
        foreach ($rows as $row) echo $this->_parseRow($row);
    }
   
    // 将传人的单行记录数组转换成 xml 标签形式
    private function _parseRow($row=array()){
        $cells = "";
        foreach ($row as $k => $v){
            $type = 'String';
            if ($this->bConvertTypes === true && is_numeric($v))
                $type = 'Number';
            $v = htmlentities($v, ENT_COMPAT, $this->sEncoding);
            $cells .= "<Cell><Data ss:Type=\"$type\">" . $v . "</Data></Cell>\n";
        }
        return "<Row>\n" . $cells . "</Row>\n";
    }

}
