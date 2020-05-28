<?php
namespace App\Tools;

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
        $this->aop->appId                 =  "2021001105658113";
//        $this->aop->appId                 =  "2021001143638010";
        $this->aop->rsaPrivateKey = "MIIEowIBAAKCAQEAqi3W8s2Pz9oYja5nkVKlCkaX9vsEIrBimVhgH/cPGjLKcKKy98QRgSPTaG3zFS8dxDYzEB1RDKjUS2myaabXyuN8qoMj5UyczDxSKWRKiBpOUZ75N8rIGl8AM+reufu7ga1YnZcz8rscTWG1TAF9rAtQS5cYLQF02lXtLUkFPWwqmLfGvh1q9rW0BgcLnD0r38HsMFxj6ROpa4Z/mk6b3Vf+HZ+a46Z5NpymyIJbdt7xIG+0Uy0ctOKcs+YWXkmRYMHHBse6KHjzbgIx246IN7Paix4C5vkOsd4Hbc5Evx1sxczi7yYLFMv1kev6QJiYraZ38tyZURyWIy0Coi5UXQIDAQABAoIBAEihG7WwaYop6IS/RFBPV0SVcFHmO5Oad9o+T3gU9wsVVjTQG1WHBnl5Esbk9fO6khelkhF0kZy3iTNOPui8XiinAhO7uFwqYFkB/YbQ2MZRg89t66sWDmTC2tFNkhUKDLKBiupnF7KmjKOx6bAwirQcd/5q09SRZI+yUHEdUvEtP2+fx8POWSvkz5cuJKusaD4pSzE7f9s2F0G8gF+557i+8aVZnJQWI0JXh5w6UpnltUusaBfsw7MFixF3CJCXA2HiIJM0ikfVQKPm2m6ASWcur2PWblYcixeGe83E7iuBzIosdIKM+uSL9hNWBGcwL4SElb72HTFfnNrlxhuy4gECgYEA6vCpnqjzQnPlKpv53VE8xOZrolxUV24vdRWZKCteGtxfDHS2gxtswszeFuRs9hmEYZBiHnsNHpDM4IGq15x3wx96KQo795U/mM7Ixx1GTEw6oZiGxeF7MfjSV3yHL+kFBAuDP1cvSJO8r+TdWYr3dUVeO4UOOZ78SevPspkToZ0CgYEAuW8PR17JpMPtQzaiXuCCdl0RmCuUWV/MzlJXa35xB9pqUtcQpr/fJM7fxLQ768wGYcloCLm2Q71hcfV5oSIlY1f7m/XyIM64euuZR2bQD3q2P0bHUyp6ibUh18XHCUNqQHTKoWNEfUJpOAbTW4XK0CX2Bj/TDv4H7mWUZwgpYcECgYAUIetnHTM7TpMkw5j1zjBW7yfqEd9oXpjSf7dQKec2hgvfFWFOetsnFkcxzwFHVYhyk9zUn9bP97iWxIXPVCkvH1NokOfyn2eDwLST235aq22ay2dBLcFQ1vGvbYxoHp+/aP0mQGJc5cwVhpcxRSdPdVJN52kApw8Xho2V0GhOQQKBgQCJ8EqOTb1z+mcRW5/XMez6fWrsJmbZQQFJ7UioZstQCzKSYvc5A3vLlrQwT95PHlsU/MyNyRADPeox6mfK7Gqhhr5dGsw9iWkDzyQbUCivixns4gq+G9hBfeMp7i6L/oEYZ4igGwbEotVAXxt0docS5Voo9etbuK5PsXJ+XjziQQKBgDsSlIOiSqd1GnHiOOAKtfxQ9MNZZIgNbKCSAnj8sZXULXiBiQub3ITv5Sapt0VNQf9c3tpieRLoSZM6DomzyeEuakm5mZ9/2IF0Ngv3pE9fv5MaZBBcG3XaJzwCX9NOZtxHdaW2UdYNNCBKJ+YHHNHbhfOvSMQys/yk9MnfBgtk";
//        $this->aop->rsaPrivateKey         = "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCHwj4wAFChrkuvSGdv35R4CAcK1mXAnzj5zXwqH8lY74IBsqoaSZnobGtajZf++djZB/rXvZ2sROEfBp82tSp2X/MwymDXoiXBmoN5C4KBIz/hv/T3T0r5FG1Srjz+9Ezp16fKG9Hjh+z53iNn7hrS/SbQqTmZowK/b925Z6OsSRTv4EbVX5hco5vT76fxZ79kisb86KJd51WLBJbeKX39DnFrbUqVwM+PKRbKg+iVHYwgWq7w8uHbPy658I9KUW4AEzIv52OjzL1EzNNYf6M3ZbFyoxIMwhzAYWTd6ZkrOjLTprmTgRrz+Xe4eryxtt6m+mYL91lnT4Jl1eA+dVHLAgMBAAECggEAF7OwC81iRngZuqaXeI6+ax5rrFa2OSUTW3mmvewCyUY3mnhI/oHvHwcezxZ9fQS9+VZNUzFFv/fenf4X+gMzQKuL1+7dbYhfsyd44L9Dr6wp0Q4khLERU99fHtpQx+p7KzB9mOm2gVUP6KgB4nlNsZyFFFUXO12cczPisCYKC2ANuBLBRMf5Coe/gJOeayJzIEyMc1YgY7Mm+8YBh6wmdojzijxpWfxXf7AC/LFvTP5utzARRXuqDdYvAGKox+HrW6UGDDM4I+IEz7cLPHo3OXxl5r10Shhqg6LUgJphuWIXIZIHwAS2lyht/03YvIYeUnmMiz7uzpvqkycZ0fjXcQKBgQDKf3ltU29r7subL3KXOySB7lmxfZ+Twn36sTfqua90q1CDSWy7Ozkq6pSpfhR0yj0lOXmRcMKvzxIANjbZ9+2TZ5D3td334KZesdealm7s7yj2T0DWoKsWZc1gqvCGkEXLnbWPQuuDfVp5xQa+iMuhzX2k1aiz/KuFJ5kGyKeQJQKBgQCroKxyVJTNpI1c8v7ECvM50gVN4awLY58rtgYwPntrqKeOCJY0X4xl3JHceusiYyhzqdOL5xYePFHELpQjnLMpzvUxAfN8smkpa4nlZ4I+L9BG51jbXA344jjoVYr8mEYlK82bNiDU/nOPp5ChWGvpj7OjFE+VrPwde2ooJZn/LwKBgAbPWqoOkES09yv04Imtd4DXHzUU4HT7qXjw1MyUCg6GZGLF6V4yk62Zzf4VO2LiGkRSNoBppmJ7OEPBjv6tk9PNO9bYyuo3J+EvYezU+k4FjP8bkzakmJxcOBf0J7qex3odh6mVuo3lfVFzCtMFjrOWUY9lkBLdgHct6buMq/XxAoGAN0ZZI71P62cqzBvKp3LrYNzeLnYXBdgA5IAMQC9van338MudLKL1Qb0nEP5ZikqrNY0lf7JeMkC1CN0DgvCt1zI7T5xUQf3n1po24DZVARg/GQbbXFKqgVTChTk/uRiFxuTzpCBrtI16xHJwJzamEqPgdA5Pj2IWFJyx1No/XasCgYEApUidbp6LOzwadwmcyA6jmmxiFK8RVHV2PApu9fYoaPBfZT5Vw4HvU4miKCy4PjxglXHSxOG8PK0kg+1T3Jvtv2o5BRXhF0bS5tYQAqMEhkwcTA9i76AEGV9JEtg7lOQFwPwlOhBuBCNeT+Dixct0T8EP/EYn1kPB51V6Cels4m4=";//私有密钥（步骤二中生成的商户私有秘钥）
        $this->aop->format                 = "JSON";
        $this->aop->charset                = "utf-8";
        $this->aop->signType            = "RSA2";
        $this->aop->apiVersion = '1.0';
        $this->aop->alipayPublicKey ="MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAlTAdFGs8uzPYG3akYT1qs3gEFtjkuRIjP2i7FHUiF52/FVTSzOiYwy9n4qQYovyP/lKxtFWTlKMZfjy1G8EYJBbcb/5dIdDbgm40yaactPaeGkAvykzw5az0PhYTUFJ7PSewZyTJeqETT8ROpuIY5rxgNVHciASiNvrSOMudHfUtqvS7mUPX/Kcpl9q0ryW6BJUIb5SnFouVmh0x6ZAyb+cXVqPXrBTLlQucT3RKuvR+zMkT9IeFFn9fIsCBGhVg8eHfacKUjOWT00CILyoLk6rIZF+PRDX32kvxLKAlfq1puupT2BZxDpH3+LvcMj0Cpl0jmXylEqAxM6qh5+sdjwIDAQAB";
//        $this->aop->alipayrsaPublicKey     = "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAh8I+MABQoa5Lr0hnb9+UeAgHCtZlwJ84+c18Kh/JWO+CAbKqGkmZ6GxrWo2X/vnY2Qf6172drEThHwafNrUqdl/zMMpg16IlwZqDeQuCgSM/4b/0909K+RRtUq48/vRM6denyhvR44fs+d4jZ+4a0v0m0Kk5maMCv2/duWejrEkU7+BG1V+YXKOb0++n8We/ZIrG/OiiXedViwSW3il9/Q5xa21KlcDPjykWyoPolR2MIFqu8PLh2z8uufCPSlFuABMyL+djo8y9RMzTWH+jN2WxcqMSDMIcwGFk3emZKzoy06a5k4Ea8/l3uHq8sbbepvpmC/dZZ0+CZdXgPnVRywIDAQAB";//商户公钥（步骤二中生成的商户公钥）
//        $this->aop->alipayPublicKey        = "sibxq1odqrg5bdp3fn43pcjhvic272bq";//支付宝公钥
    }

    public function text(){
        return 123;
    }
    public function createAppPay($order_sn, $subject, $total_amount,$pay_type){
        require_once 'aop/request/AlipayTradeAppPayRequest.php';
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent    =    [
            'body'                =>    "商品购买",
            'subject'            =>    $subject,
            'out_trade_no'        =>    $order_sn,
            'timeout_express'    =>    '1d',//失效时间为 1天
            'total_amount'        =>    $total_amount,//价格
            'product_code'        =>    'QUICK_MSECURITY_PAY',
        ];
        //商户外网可以访问的异步地址 (异步回掉地址，根据自己需求写)
        if($pay_type == 1){
            $request->setNotifyUrl("http://".$_SERVER['HTTP_HOST'].'/Api/notify/alinotify');
        }else{
            $request->setNotifyUrl("http://".$_SERVER['HTTP_HOST'].'/Api/notify/aliTopnotify');
        }
        $request->setBizContent(json_encode($bizcontent));
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $this->aop->sdkExecute($request);
        return $response;
        //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
    }
}
