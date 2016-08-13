<?php

require_once dirname(dirname(__FILE__)).'/service/SDKServerService.php';
require_once dirname(dirname(__FILE__)).'/model/SDKException.php';

//玩家的sid
$sid = $_GET['sid'];
try{
    $sessionInfo = SDKServerService::verifySession($sid);
    echo $sessionInfo->accountId."|";
    echo $sessionInfo->creator."|";
	echo $sessionInfo->nickName;
}
catch (SDKException $e){
    //echo $e->getCode()." ".$e->getMessage();
	echo -1;
}