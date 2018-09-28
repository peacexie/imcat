<?php
namespace imcat;

// Output
class basOut
{
    static $options = array(
        // 输出参数-jsonp
        'jsonp_callback' => 'callback',
        'jsonp_return' => 'callback',
        // 输出参数-xml
        'root_node' => 'root', // 根节点名
        'root_attr' => '', // 根节点属性
        'item_node' => 'item', //数字索引的子节点名
        'item_key'  => 'id', // 数字索引子节点key转换的属性名
        'encoding'  => 'utf-8', // 数据编码
    );

    static $contentType = ''; // application/json, application/javascript, text/xml

    // 格式化输出
    static function fmt($data,$type='json')
    {
        $type = in_array($type,array('jsonp','xml')) ? $type : 'json';
        $method = 'out'.ucfirst($type);
        $re = self::$method($data);
        return $re;
    }

    static function outJson($data)
    {
        try {
            // 返回JSON数据格式到客户端 包含状态信息
            $data = comParse::jsonEncode($data);
            return $data;
        } catch (Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
    }

    static function outJsonp($data)
    {
        $opt = self::$options;
        try {
            // 返回JSON数据格式到客户端
            $callback = basReq::val('callback',$opt['jsonp_callback']); 
            $handler = !empty($callback) ? $callback : $opt['jsonp_return'];
            $data = comParse::jsonEncode($data);
            $data = $handler . '(' . $data . ');';
            return $data;
        } catch (Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
    }

    static function outXml($data,$uopt=array())
    {
        $opt = self::$options;
        if(!empty($uopt)) $opt = array($opt, $uopt);
        $attr = $opt['root_attr'];
        if(is_array($attr)) {
            $arr = array();
            foreach ($attr as $key => $value) {
                $arr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $arr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml  = "<?xml version=\"1.0\" encoding=\"{$opt['encoding']}\"?>\n";
        $xml .= "<{$opt['root_node']}{$attr}>\n";
        $xml .= self::xmlRow($data, $opt['item_node'], $opt['item_key']);
        $xml .= "</{$opt['root_node']}>\n";
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed  $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id   数字索引key转换为的属性名
     * @return string
     */
    static function xmlRow($data, $item, $id)
    {
        $xml = $attr = '';
        $a1 = array("&","<",">");
        $a2 = array("&amp;","&lt;","&gt;");
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key         = $item;
            }
            $val = (is_array($val)||is_object($val)) ? self::xmlRow($val,$item,$id) : str_replace($a1,$a2,$val);
            $xml .= "<{$key}{$attr}>$val</{$key}>\n";
        }
        return $xml;
    }
}

/*
$res = array(
    'errno' => 0,
    'state' => 'success',
    'msg' => 'msg',
    'data' => array(
        'uid' => 1234,
        'uname' => 'peace',
    ),
);
*/
