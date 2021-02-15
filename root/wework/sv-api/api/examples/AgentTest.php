<?php /*
 * Copyright (C) 2017 All rights reserved.
 *   
 * @File AgentTest.php
 * @Brief 
 * @Author abelzhu, abelzhu@tencent.com
 * @Version 1.0
 * @Date 2017-12-26
 *
 */
 
include_once("../src/CorpAPI.class.php");
include_once("../src/ServiceCorpAPI.class.php");
include_once("../src/ServiceProviderAPI.class.php");

$config = require __DIR__.'/config.php';

//
$api = new CorpAPI($config['CorpId'], $config['AppsConfig']['1000002']['Secret']);

// ------------------------- 应用管理 --------------------------------------
try {
    //
    $agent = new Agent();
    {
        $agent->agentid = 1000002;
        $agent->description = "I'm description";
    }
    $api->AgentSet($agent);

    /*
    $agent = $api->AgentGet('1000002');
    var_dump($agent);
    */

    //
    $agentList = $api->AgentGetList();
    dump($agentList);

} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}
