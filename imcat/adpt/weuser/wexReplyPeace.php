<?php
namespace imcat;
(!defined('RUN_INIT')) && die('No Init');
// 消息回复（被动回复）

class wexReplyPeace extends wexReplyAdmin{
    
    public $_db = NULL;
    
    function __construct($post,$cfg,$re=0){ 
        parent::__construct($post,$cfg); 
    }
    
}
