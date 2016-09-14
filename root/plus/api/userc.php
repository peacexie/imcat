<?php

//UCenter API

$post = file_get_contents('php://input'); 
print_r(@$post);

print_r(@$_GET);
print_r(@$_POST);
print_r(@$_SERVER);

?>
UC_CLIENT_VERSION