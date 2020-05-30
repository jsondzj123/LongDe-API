<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Tools\AlipayFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class OrderController extends Controller {
    //微信pc支付
    public function wxPcpay(){

    }
    //支付宝支付pc
    public function aliPcpay(){
        $alipay = new AlipayFactory();
        $return = $alipay->createPcPay();
        if($return['alipay_trade_precreate_response']['code'] == 10000){
            echo $return['alipay_trade_precreate_response']['code'];
            //生成二维码
            $qrcode = new QrCode();


            $value = $return['alipay_trade_precreate_response']['qr_code']; //二维码内容
            $errorCorrectionLevel = 'L';//容错级别
            $matrixPointSize = 6; // 生成图片大小
            //生成二维码图片
            $img = $qrcode->png($value);
            echo $img;
        }else{

        }
    }
    //汇聚支付宝支付
    public function hjaliPcpay(){
//        if($pay_type == 1){
//            $notify = "http://".$_SERVER['HTTP_HOST']."/Api/notify/hjAlinotify";
//        }else{
            $notify = "http://".$_SERVER['HTTP_HOST']."/Web/notify/hjAlinotify";
//        }
        $arr=[
            'p0_Version'=>'1.0',
            'p1_MerchantNo'=>'888108900009969',
            'p2_OrderNo'=>'20200528171614556633',
            'p3_Amount'=>0.01,
            'p4_Cur'=>1,
            'p5_ProductName'=>"龙德产品",
            'p9_NotifyUrl'=>$notify,
            'q1_FrpCode'=>'ALIPAY_NATIVE',
            'q4_IsShowPic'=>1,
            'qa_TradeMerchantNo'=>'777167300271170'
        ];
        $str = "15f8014fee1642fbb123fb5684cda48b";
        $token = $this->hjHmac($arr,$str);
        $arr['hmac'] = $token;
        $aaa = $this->hjpost($arr);
        print_r($aaa);die;
    }
    //汇聚微信支付
    public function hjwxPcpay(){
//        if($pay_type == 1){
//            $notify = "http://".$_SERVER['HTTP_HOST']."/Api/notify/hjAlinotify";
//        }else{
        $notify = "http://".$_SERVER['HTTP_HOST']."/Web/notify/hjAlinotify";
//        }
        $arr=[
            'p0_Version'=>'1.0',
            'p1_MerchantNo'=>'888108900009969',
            'p2_OrderNo'=>'20200528171614556633',
            'p3_Amount'=>0.01,
            'p4_Cur'=>1,
            'p5_ProductName'=>"龙德产品",
            'p9_NotifyUrl'=>$notify,
            'q1_FrpCode'=>'WEIXIN_NATIVE',
            'q4_IsShowPic'=>1,
            'qa_TradeMerchantNo'=>'777170100269422'
        ];
        $str = "15f8014fee1642fbb123fb5684cda48b";
        $token = $this->hjHmac($arr,$str);
        $arr['hmac'] = $token;
        $aaa = $this->hjpost($arr);
        print_r($aaa);die;


//    "r7_TrxNo": "100220052803060518",
//    "rb_CodeMsg": "",
//    "r2_OrderNo": "20200528171614556633",
//    "r3_Amount": "0.01",
//    "r6_FrpCode": "WEIXIN_NATIVE",
//    "rc_Result": "http://trade.joinpay.com/wxPay.action?trxNo=100220052803060518",
//    "ra_Code": 100,
//    "hmac": "743CB595BC6729B67978510CD9F61BE9",
//    "rd_Pic": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAANIAAADSAQAAAAAX4qPvAAABvElEQVR42u2YPY6EMAyFjSgoOQI3gYuNBBIXg5vkCJQp0Hjfc9iBndntVnYDRUbko7Dy/PMyon8+i9zsZv/HdsEzJtVtTLmXUfneBTH8bg/J1VZjo927puzFMKkQogxgWcgWeUQyxNm3k9gH0WzUZsWCOOdYRv2oGrafreqHto6MeQy2bsfynvOO7Pvhdhapf+sFXgxxVptIs5RcYtFDxDimc9In2g6Cranf1AWxLlt01SuX1vPMnJmiCbczeo8xqZOy2oJYYqmnPOjE05vQnc84nRlqe2nxARbF9tTh4E79fJmmkkFPVHnD4ckj1CCmzStO6blUF23dmW7CFoii52IZHsSY0jYV1GZV+qGfL9s781mMLlm951M/b6al8eXDStBsyRjFkk0FYb0X/a4578t2KQaC0dm29Jf57swOn8V2fIwGuuIghjSyZjjb9UDKBI1iSCM2w4eYKxaa8xhmD19Y7z1tMB1XDDv8pxQ/wSGx6qxBjL4cN8mBi7AFvt0RXFlRzfzEYCJePF8E401ypJ9QdOdNYpmadIvNqkuNeTO796PeJ5ta7INhzPK6jAbTT/Ut5/3Y/R/ZzULZF7C4BezFfHYqAAAAAElFTkSuQmCC",
//    "r4_Cur": "1",
//    "r0_Version": "1.0",
//    "r1_MerchantNo": "888108900009969"
    }






    //汇聚签名
    public function hjHmac($arr,$str){
        $newarr = '';
        foreach ($arr as $k=>$v){
            $newarr =$newarr.$v;
        }
        return md5($newarr.$str);
    }
    public function hjpost($data){
        //简单的curl
        $ch = curl_init("https://www.joinpay.com/trade/uniPayApi.action");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

