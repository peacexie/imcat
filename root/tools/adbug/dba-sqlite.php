<?php    

# Adminer - dostosowanie
# https://www.adminer.org/pl/extension/

function adminer_object() {
  
  class AdminerSQLite extends Adminer {
    
    function name() {
      // custom name in title and heading
      return 'SQLite';
    }
    
    function permanentLogin($i=false) {
      // key used for permanent login
      return '123456';
    }
    
    function credentials() {
      // server, username and password for connecting to database
      return array('localhost', 'ODBC', '');
    }
    
    function database() {
      // database name, will be escaped by Adminer
      return 'db-SQLite';
    }
    
    function login($login, $password) {
      // validate user submitted credentials
      #return true;
      return ($login == 'imcat' && $password == 'sqlite');
    }
    
    function tableName($tableStatus) {
      // tables without comments would return empty string and will be ignored by Adminer
      return $tableStatus['Name']; 
      # return h($tableStatus['Comment']);
    }
    
    function fieldName($field, $order = 0) {
      // only columns with comments will be displayed and only the first five in select
      return $field['field'];
      #($order <= 5 && !preg_match('~_(md5|sha1)$~', $field['field']) ? h($field['comment']) : '');
    }
    
  }
  
  return new AdminerSQLite;
}

include dirname(__FILE__).'/dbadm.php';  
