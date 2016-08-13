<?php
class Sikai {
  

	/**
     * 校验彩客易付签名
     */
    public function verifySign($para)
    {
        if(empty($para)) {//判断POST来的数组是否为空
			return false;
        }
        else {
            $postSign = $para['signMsg'];
            // 除去数组中的签名参数
            unset($para['signMsg']);
            $para['key'] = '*&^%^&*()98^%7^89000^&%##';

            // 签名的字符串
            $signStr = makeLinkstring($para, false);

            $sign = strtoupper(md5($signStr));
			//echo $postSign."***".$sign;
            return $postSign === $sign;
        }
    }




}