<?php
(!defined('RUN_INIT')) && die('No Init');
include_once(DIR_STATIC.'/ximp/class/Medoo.cls_php'); 

class extMedoo extends medoo{
    
    // 表后缀
    protected $surfix;

    public function __construct($options = null)
    {

        parent::__construct($paras);
        if (isset($options[ 'surfix' ]))
        {
            $this->surfix = $options[ 'surfix' ];
        }

    }

    protected function table_quote($table)
    {
        return '"' . $this->prefix . $table . $this->surfix . '"';
    }

    protected function column_quote($string)
    {
        preg_match('/(\(JSON\)\s*|^#)?([a-zA-Z0-9_]*)\.([a-zA-Z0-9_]*)/', $string, $column_match);

        if (isset($column_match[ 2 ], $column_match[ 3 ]))
        {
            return '"' . $this->prefix . $column_match[ 2 ] . $this->surfix . '"."' . $column_match[ 3 ] . '"';
        }

        return '"' . $string . '"';
    }

}

/*
*/
