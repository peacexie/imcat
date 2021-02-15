<?php /*
 * Copyright (C) 2017 All rights reserved.
 *   
 * @File OauthTest.php
 * @Brief 
 * @Author abelzhu, abelzhu@tencent.com
 * @Version 1.0
 * @Date 2017-12-26
 *
 */
 
include_once("../src/CorpAPI.class.php");
include_once("../src/ServiceCorpAPI.class.php");
include_once("../src/ServiceProviderAPI.class.php");

$config = require('./config.php');
// 
$agentId = '1000002';
$api = new CorpAPI($config['CorpId'], $config['AppsConfig']['1000002']['Secret']);
//var_dump($api);

try {
    $UserInfoByCode = $api->GetUserInfoByCode("IPzWnFmIQVf2wJFlJrln9-P-wqu7jeQsKyUKol1TWeU"); 
    var_dump($UserInfoByCode);

    $userDetailByUserTicket = $api->GetUserDetailByUserTicket($UserInfoByCode->user_ticket); 
    var_dump($userDetailByUserTicket);

} catch (Exception $e) { 
    echo $e->getMessage() . "\n";
}
