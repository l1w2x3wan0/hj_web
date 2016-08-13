<?php
header("Content-type: text/html; charset=utf-8");
/*
 * Created on 2015-9-1
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once ("base.php");
require_once ("config.php");
require_once("../connect.php");

function testQueryResult() {
	global $queryResultUrl, $platpkey;
	$string = $_POST;//接收post请求数据
	$cporderid;
	if($string ==null){
			echo "请使用post方式提交数据";
		}else{
			
			
			$transdata=$string['transdata'];
			echo "$transdata\n";
			if(stripos("%22",$transdata)){//=%7B%22appid%22%3A%22300123212234%22%2C%22appuserid
				
				$data=urldecode($string);
				$respData = 'transdata='.$string['transdata'].'&sign='.$string['sign'].'&signtype='.$string['signtype'];//把数据组装成验签函数要求的参数格式
				//  验签函数parseResp（） 中 只接受明文数据。数据如：transdata={"appid":"3003686553","appuserid":"10123059","cporderid":"1234qwedfq2as123sdf3f1231234r","cpprivate":"11qwe123r23q232111","currency":"RMB","feetype":0,"money":0.12,"paytype":403,"result":0,"transid":"32011601231456558678","transtime":"2016-01-23 14:57:15","transtype":0,"waresid":1}&sign=jeSp7L6GtZaO/KiP5XSA4vvq5yxBpq4PFqXyEoktkPqkE5b8jS7aeHlgV5zDLIeyqfVJKKuypNUdrpMLbSQhC8G4pDwdpTs/GTbDw/stxFXBGgrt9zugWRcpL56k9XEXM5ao95fTu9PO8jMNfIV9mMMyTRLT3lCAJGrKL17xXv4=&signtype=RSA
				echo "进入了1";
				if(!parseResp($respData, $platpkey, $respJson)) {
					echo 'failed'."\n";
				}else{
					echo 'success'."\n";
					$transdata=$string['transdata'];
					$arr=json_decode($transdata);
					$cporderid=$arr->cporderid;
					echo "$transdata\n";
					echo "$cporderid\n";
				}
				
			}else{
				$respData = 'transdata='.$string['transdata'].'&sign='.$string['sign'].'&signtype='.$string['signtype'];//把数据组装成验签函数要求的参数格式
				//  验签函数parseResp（） 中 只接受明文数据。数据如：transdata={"appid":"3003686553","appuserid":"10123059","cporderid":"1234qwedfq2as123sdf3f1231234r","cpprivate":"11qwe123r23q232111","currency":"RMB","feetype":0,"money":0.12,"paytype":403,"result":0,"transid":"32011601231456558678","transtime":"2016-01-23 14:57:15","transtype":0,"waresid":1}&sign=jeSp7L6GtZaO/KiP5XSA4vvq5yxBpq4PFqXyEoktkPqkE5b8jS7aeHlgV5zDLIeyqfVJKKuypNUdrpMLbSQhC8G4pDwdpTs/GTbDw/stxFXBGgrt9zugWRcpL56k9XEXM5ao95fTu9PO8jMNfIV9mMMyTRLT3lCAJGrKL17xXv4=&signtype=RSA
				echo "进入了2";
				if(!parseResp($respData, $platpkey, $respJson)) {
					echo 'failed'."\n";
				}else{
					echo 'success'."\n";
					$transdata=$string['transdata'];
					$arr=json_decode($transdata);
					$appid=$arr->appid;
					$appuserid=$arr->appuserid;
					$cporderid=$arr->cporderid;
					$cpprivate=$arr->cpprivate;
					$money=$arr->paytype;
					$result=$arr->result;
					$transid=$arr->transid;
					$transtime=$arr->transtime;
					$waresid=$arr->waresid;
					echo "$appid\n";
					echo "$appuserid\n";
					echo "$cporderid\n";
					echo "$cpprivate\n";
					echo "$money\n";
					echo "$result\n";
					echo "$transid\n";
					echo "$transtime\n";
					echo "$waresid\n";
				}
			}
			
			
			
			
			
			
			
		
	 }
	 
}
testQueryResult();
?>
