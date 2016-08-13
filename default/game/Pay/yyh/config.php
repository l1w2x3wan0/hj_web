<?php
header("Content-type: text/html; charset=utf-8");
/**
 *功能：配置文件
 *版本：1.0
 *修改日期：2014-06-26
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究爱贝云计费接口使用，只是提供一个参考。
 */
 
//爱贝商户后台接入url
// $coolyunCpUrl="http://pay.coolyun.com:6988";
$iapppayCpUrl="http://ipay.iapppay.com:9999";
//登录令牌认证接口 url
$tokenCheckUrl=$iapppayCpUrl . "/openid/openidcheck";

//下单接口 url
// $orderUrl=$coolyunCpUrl . "/payapi/order";
$orderUrl=$iapppayCpUrl . "/payapi/order";

//支付结果查询接口 url
$queryResultUrl=$iapppayCpUrl ."/payapi/queryresult";

//契约查询接口url
$querysubsUrl=$iapppayCpUrl."/payapi/subsquery";

//契约鉴权接口Url
$ContractAuthenticationUrl=$iapppayCpUrl."/payapi/subsauth";

//取消契约接口Url
$subcancel=$iapppayCpUrl."/payapi/subcancel";
//H5和PC跳转版支付接口Url
$h5url="https://web.iapppay.com/h5/exbegpay?";
$pcurl="https://web.iapppay.com/pc/exbegpay?";

//应用编号
$appid="5001012799";
//应用私钥
$appkey="MIICWwIBAAKBgQCGYRK9BpeJDD/rKRtrqa8gvq97mANLW75tnPf6XZG1bumj+87PCqbhKYBn3xoRnVMOM0o/edkDGrF44JSZJqBomNdOT6uJhEa4Gnuo34e6ku09vt35od18SHNSDA/tMLVXNdggfRa6zJojprZaN4puJ1dEvjrnO9vgzLBpOeWaLwIDAQABAoGAMr1wv+M/UMZJVFg/PFXkBK87v6VUiR3pN6ZvlB/EpwdsPTkVi+Pvw1FSZOY/B6up8tC3FmTB5oQrwwK52WJWatwBDXcETKnAs76Pgmy0ftG14/Q38euAvGeTR39MhtguSafrg6AmRpef4EFNZJ2lw2siKql6k9lUarrHoVHmxsECQQDFq+G2xcUhCGlzkhSwXEmPWJyZhvkGOVj7o9EPKRgXWFMcnItukZ/Td+Crryrv6DlPY9/vz6jXDZD1NuYjyu8RAkEArggXP9lrG95kgiygKWM6KX+XS9MfHG5S18o8yXcvwZq8kgKV3ftSUg42zXN/sSEMDcp9yHfPRIF3wuzxFJJ1PwJAG3WVFV7D06LngzZ3yUgIr/EPUAR/821j/xDyqbh7sEKEySS1+dYg7a9pdnAO9uS7kVu+cAHY4obv7CCEN9SpAQJAVr2voOJkMXLKU9ucRKSxg6eVqHRlKE2Quv+RBrWCNxRB8uCZBBhU8pMlG4f62DVphaIsyXN1+mYxZBWnH6LUDwJAMyciiadSYLv7Ps3QN/5eg4j/jz3nQ4IJ1KjYOwv8CKk0w1H3slaCYAcVdDY9XA4K2KnWUmcv7zxdwjP+8Mlesg==";
//平台公钥
$platpkey="MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCkJDp/5+tzEFb56XIgKHMgQMKvjKIfesMPK1RC3hjttnk7YFNEQfOC2lbZm32x05IppC7VL05r9/BFycV4X5tPViBqE4eQQjMFqpxkqF/3GSXY4o5qsMdZPgl09hf+4rvcAGw1ah4oc9WZiG62V/ZyFIwebKQhGN0QfWFULt2+iwIDAQAB";

?>