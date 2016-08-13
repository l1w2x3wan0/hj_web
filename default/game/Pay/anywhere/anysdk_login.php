<?php
header("Content-type: text/html; charset=utf-8");
/**
 * Start  测试执行代码 
 * 1、游戏客户端向AnySDK框架请求登录验证，AnySDK框架会往登录回调地址（游戏服务端提供并填写到AnySDK打包工具配置中）请求登录验证；
 * 2、游戏服务端在AnySDK框架请求的参数转发到AnySDK统一登录验证服务http://oauth.anysdk.com/api/User/LoginOauth/；
 * 3、由AnySDK统一登录验证服务处理各个渠道的登录验证并返回渠道服务器返回的原始信息及通用信息如channel标识、用户标识uid等；
 * 4、游戏服务端在接收到AnySDK返回的信息后就可以后续游戏逻辑处理，并将渠道返回的原始信息返回给客户端；
 * 5、详细参见http://docs.anysdk.com/index.php?title=%E7%BB%9F%E4%B8%80%E7%99%BB%E5%BD%95%E9%AA%8C%E8%AF%81
 */
error_reporting(E_ALL);
$login = new Login();
$login->check();
/**
 * End 测试执行代码
 */
class Login {
        /**
         * debug 模式
         */
        const DEBUG_MODE = TRUE;
        /**
         * anysdk统一登录地址
         * @var string
         */
        private $_loginCheckUrl = 'http://oauth.anysdk.com/api/User/LoginOauth/';
        /**
         * check login
         * 检查登录合法性及返回sdk返回的用户id或部分用户信息
         */
        public function check() {
                //http请求中所有请求参数数组
                require("../connect_reg.php");
				
				$params = $_REQUEST;

				$mulu = "../work/Logs/".date("Ymd");
				if (!file_exists($mulu)) mkdir($mulu,0777); 
				$logFile = $mulu.'/gamepost'.time().'.txt';
				//echo $logFile."<br>";
				file_put_contents($logFile, json_encode($_REQUEST));

                //检测必要参数
                if (!$this->parametersIsset($params)) {
                        echo 'parameter not complete';
                        exit;
                }
                //模拟http请求
                include_once 'classes/HttpHelper.php';
                $http = new HttpHelper();
                //这里建议使用post方式提交请求，避免客户端提交的参数再次被urlencode导致部分渠道token带有特殊符号验证失败
                $result = $http->post($this->_loginCheckUrl, $params);
                //@todo在这里处理游戏逻辑，在服务器注册用户信息等
                echo $result;
				//$result = '{"status":"ok","data":{"accountId":"363a277c99e5dc5c8395d8dc926d888c","nickName":"\u4e5d\u6e38\u73a9\u5bb6656259299","creator":"JY"},"common":{"channel":"000255","user_sdk":"uc","uid":"363a277c99e5dc5c8395d8dc926d888c","server_id":"1","plugin_id":"6"},"ext":""}';
                //$result如： {"status":"ok","data":{--渠道服务器返回的信息--},"common":{"channel":"渠道标识","uid":"用户标识"}}
                //输出anysdk统一登录返回
                if (self::DEBUG_MODE) {
                        
						$mulu = "../../work/Logs/".date("Ymd");
						if (!file_exists($mulu)) mkdir($mulu,0777); 
						
						$logFile = $mulu.'/game.server.login.check.log';
						//echo "<br>".$logFile;
                        file_put_contents($logFile, "#" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
                        file_put_contents($logFile, "#URL:\n" . print_r($http->getUrl(), TRUE) . "\n", FILE_APPEND);
                        file_put_contents($logFile, "#RESULT:\n" . print_r($result, TRUE) . "\n", FILE_APPEND);
                        file_put_contents($logFile, "#DECODE:\n" . print_r(json_decode($result, true), TRUE) . "\n", FILE_APPEND);
						
						$arr = json_decode($result, true);
						if (!empty($arr['common']['uid']) and $arr['status']=="ok"){
							$sql = "INSERT INTO user_reg_log (showdata, channel, user_sdk, uid, server_id, plugin_id, addtime) values ('".json_encode($arr['data'], JSON_UNESCAPED_UNICODE)."', '".$arr['common']['channel']."', '".$arr['common']['user_sdk']."', '".$arr['common']['uid']."', '".$arr['common']['server_id']."', '".$arr['common']['plugin_id']."', '".time()."')";
							//echo $sql;
							mysql_query($sql);
						}
						
                }
        }
        /**
         * check needed parameters isset 
         * 检查必须的参数 channel uapi_key：渠道提供给应用的app_id或app_key（标识应用的id） uapi_secret：渠道提供给应用的app_key或app_secret（支付签名使用的密钥）
         * @param type $params
         * @return boolean
         */
        private function parametersIsset($params) {
                if (!(isset($params['channel']) && isset($params['uapi_key']) && isset($params['uapi_secret']))) {
                        return false;
                }
                return TRUE;
        }
}
?>