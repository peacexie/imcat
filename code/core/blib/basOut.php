<?php

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

    /**
     * 处理数据
     * @param mixed $data 要处理的数据
     * @return mixed
     * @throws \Exception
     */
    static function outJson($data)
    {
        try {
            // 返回JSON数据格式到客户端 包含状态信息
            $data = comParse::jsonEncode($data);
            /*if ($data === false) {
                throw new Exception(json_last_error_msg());
            }*/
            return $data;
        } catch (Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
    }

    /**
     * 处理数据
     * @param mixed $data 要处理的数据
     * @return mixed
     * @throws \Exception
     */
    static function outJsonp($data)
    {
        $opt = self::$options;
        try {
            // 返回JSON数据格式到客户端
            $callback = req('callback',$opt['jsonp_callback']); 
            $handler = !empty($callback) ? $callback : $opt['jsonp_return'];
            $data = comParse::jsonEncode($data);
            /*if ($data === false) {
                throw new Exception(json_last_error_msg());
            }*/
            $data = $handler . '(' . $data . ');';
            return $data;
        } catch (Exception $e) {
            if ($e->getPrevious()) {
                throw $e->getPrevious();
            }
            throw $e;
        }
    }

    /**
     * 处理数据
     * @param mixed $data 要处理的数据
     * @return mixed
     */
    static function outXml($data)
    {
        $opt = self::$options;
        // XML数据转换
        return self::xmlEncode($data, $opt['root_node'], $opt['item_node'], $opt['root_attr'], $opt['item_key'], $opt['encoding']);
    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    static function xmlEncode($data, $root, $item, $attr, $id, $encoding)
    {
        if (is_array($attr)) {
            $array = array();
            foreach ($attr as $key => $value) {
                $array[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $array);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml  = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>\n";
        $xml .= "<{$root}{$attr}>\n";
        $xml .= self::dataToXml($data, $item, $id);
        $xml .= "</{$root}>\n";
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed  $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id   数字索引key转换为的属性名
     * @return string
     */
    static function dataToXml($data, $item, $id)
    {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key         = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (is_array($val) || is_object($val)) ? self::dataToXml($val, $item, $id) : $val;
            $xml .= "</{$key}>\n";
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
