<?php
class Caikeyi {
  

	/**
     * 生成彩客易付签名
     * 生成机制：Md5(md5(str+密钥)+ 密钥)
     */
    protected function makeCaikeSign($str) {
        $appKey = '55e4151772385d21bf8ffdbf9dd5084e';
        return md5(md5($str.$appKey).$appKey);
    }

    /**
     * 获取彩客易付签名keyValue值
     */
    protected function getCaikeSignKeyValue($para)
    {
        $arg = array();
        while (list ($key, $val) = each ($para)) {
            if($key == "sign" || $val == "")continue;
            else	$arg[$key] = $para[$key];
        }

        $arg = arrSort($arg);

        return $arg;
    }

    /**
     * 校验彩客易付签名
     */
    public function verifyCaikeSing($para)
    {
        $sign = $para['sign'];
        $signArr = $this->getCaikeSignKeyValue($para);
        $signStr = makeLinkstring($signArr);
        $new_sign = $this->makeCaikeSign($signStr);
        return $new_sign === $sign;
    }









}