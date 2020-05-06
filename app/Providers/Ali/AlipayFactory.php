<?php
namespace App\Providers\Ali;

use App\Providers\aop\AlipayTradeAppPayRequest\AlipayTradeAppPayRequest;
use App\Providers\aop\AopClient\AopClient;

class AlipayFactory{
    protected $aop;
    //公共参数
    public function __construct(){
        require_once 'aop/AopClient.php';
        require_once 'aop/request/AlipayTradeAppPayRequest.php';
        $this->aop    =    new AopClient();
        $this->aop->gatewayUrl             = "https://openapi.alipay.com/gateway.do";
        $this->aop->appId                 = "2021001143638010";
        $this->aop->rsaPrivateKey         = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCHwj4wAFChrkuvSGdv35R4CAcK1mXAnzj5zXwqH8lY74IBsqoaSZnobGtajZf++djZB/rXvZ2sROEfBp82tSp2X/MwymDXoiXBmoN5C4KBIz/hv/T3T0r5FG1Srjz+9Ezp16fKG9Hjh+z53iNn7hrS/SbQqTmZowK/b925Z6OsSRTv4EbVX5hco5vT76fxZ79kisb86KJd51WLBJbeKX39DnFrbUqVwM+PKRbKg+iVHYwgWq7w8uHbPy658I9KUW4AEzIv52OjzL1EzNNYf6M3ZbFyoxIMwhzAYWTd6ZkrOjLTprmTgRrz+Xe4eryxtt6m+mYL91lnT4Jl1eA+dVHLAgMBAAECggEAF7OwC81iRngZuqaXeI6+ax5rrFa2OSUTW3mmvewCyUY3mnhI/oHvHwcezxZ9fQS9+VZNUzFFv/fenf4X+gMzQKuL1+7dbYhfsyd44L9Dr6wp0Q4khLERU99fHtpQx+p7KzB9mOm2gVUP6KgB4nlNsZyFFFUXO12cczPisCYKC2ANuBLBRMf5Coe/gJOeayJzIEyMc1YgY7Mm+8YBh6wmdojzijxpWfxXf7AC/LFvTP5utzARRXuqDdYvAGKox+HrW6UGDDM4I+IEz7cLPHo3OXxl5r10Shhqg6LUgJphuWIXIZIHwAS2lyht/03YvIYeUnmMiz7uzpvqkycZ0fjXcQKBgQDKf3ltU29r7subL3KXOySB7lmxfZ+Twn36sTfqua90q1CDSWy7Ozkq6pSpfhR0yj0lOXmRcMKvzxIANjbZ9+2TZ5D3td334KZesdealm7s7yj2T0DWoKsWZc1gqvCGkEXLnbWPQuuDfVp5xQa+iMuhzX2k1aiz/KuFJ5kGyKeQJQKBgQCroKxyVJTNpI1c8v7ECvM50gVN4awLY58rtgYwPntrqKeOCJY0X4xl3JHceusiYyhzqdOL5xYePFHELpQjnLMpzvUxAfN8smkpa4nlZ4I+L9BG51jbXA344jjoVYr8mEYlK82bNiDU/nOPp5ChWGvpj7OjFE+VrPwde2ooJZn/LwKBgAbPWqoOkES09yv04Imtd4DXHzUU4HT7qXjw1MyUCg6GZGLF6V4yk62Zzf4VO2LiGkRSNoBppmJ7OEPBjv6tk9PNO9bYyuo3J+EvYezU+k4FjP8bkzakmJxcOBf0J7qex3odh6mVuo3lfVFzCtMFjrOWUY9lkBLdgHct6buMq/XxAoGAN0ZZI71P62cqzBvKp3LrYNzeLnYXBdgA5IAMQC9van338MudLKL1Qb0nEP5ZikqrNY0lf7JeMkC1CN0DgvCt1zI7T5xUQf3n1po24DZVARg/GQbbXFKqgVTChTk/uRiFxuTzpCBrtI16xHJwJzamEqPgdA5Pj2IWFJyx1No/XasCgYEApUidbp6LOzwadwmcyA6jmmxiFK8RVHV2PApu9fYoaPBfZT5Vw4HvU4miKCy4PjxglXHSxOG8PK0kg+1T3Jvtv2o5BRXhF0bS5tYQAqMEhkwcTA9i76AEGV9JEtg7lOQFwPwlOhBuBCNeT+Dixct0T8EP/EYn1kPB51V6Cels4m4=";//私有密钥（步骤二中生成的商户私有秘钥）
        $this->aop->format                 = "JSON";
        $this->aop->charset                = "utf-8";
        $this->aop->signType            = "RSA2";
        $this->aop->apiVersion = '1.0';
        $this->aop->alipayrsaPublicKey     = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAh8I+MABQoa5Lr0hnb9+UeAgHCtZlwJ84+c18Kh/JWO+CAbKqGkmZ6GxrWo2X/vnY2Qf6172drEThHwafNrUqdl/zMMpg16IlwZqDeQuCgSM/4b/0909K+RRtUq48/vRM6denyhvR44fs+d4jZ+4a0v0m0Kk5maMCv2/duWejrEkU7+BG1V+YXKOb0++n8We/ZIrG/OiiXedViwSW3il9/Q5xa21KlcDPjykWyoPolR2MIFqu8PLh2z8uufCPSlFuABMyL+djo8y9RMzTWH+jN2WxcqMSDMIcwGFk3emZKzoy06a5k4Ea8/l3uHq8sbbepvpmC/dZZ0+CZdXgPnVRywIDAQAB";//商户公钥（步骤二中生成的商户公钥）
//        $this->aop->alipayPublicKey        = "sibxq1odqrg5bdp3fn43pcjhvic272bq";//支付宝公钥
    }

    public function text(){
        return 123;
    }
    public function createAppPay($order_sn, $subject, $total_amount){
        require_once 'aop/request/AlipayTradeAppPayRequest.php';
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent    =    [
            'body'                =>    "商品购买",
            'subject'            =>    $subject,
            'out_trade_no'        =>    $order_sn,
            'timeout_express'    =>    '1d',//失效时间为 1天
            'total_amount'        =>    0.01,//价格
            'product_code'        =>    'QUICK_MSECURITY_PAY',
        ];
        //商户外网可以访问的异步地址 (异步回掉地址，根据自己需求写)
        $request->setNotifyUrl("http://".$_SERVER['HTTP_HOST'].'/apply.php/Api/alinotify_url');
        $request->setBizContent(json_encode($bizcontent));
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $this->aop->sdkExecute($request);
        return $response;
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
    }
}
