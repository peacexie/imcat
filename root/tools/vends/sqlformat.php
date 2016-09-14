<?php
require(dirname(__FILE__).'/_config.php'); 

$query = "SELECT count(*),`Column1`,`Testing`, `Testing Three` FROM `Table1`
    WHERE Column1 = 'testing' AND ( (`Column2` = `Column3` OR Column4 >= NOW()) )
    GROUP BY Column1 ORDER BY Column3 DESC LIMIT 5,10";

dump(basSql::fmtShow($query));

// basDebug
echo basDebug::runInfo();

?>
