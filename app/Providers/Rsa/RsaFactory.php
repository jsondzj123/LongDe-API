<?php

namespace App\Providers\Rsa;
use Illuminate\Http\Request;

class RsaFactory {
    /**
     * 加密方法，对数据进行加密，返回加密后的数据
     *
     * @param string $data 要加密的数据
     *
     * @return string
     *
     */
    public function aesencrypt($data , $key , $iv='sciCuBC7orQtDhTO') {
        return base64_encode(openssl_encrypt($data, "AES-128-CBC", $key , OPENSSL_RAW_DATA, $iv));
    }

    /**
     * 解密方法，对数据进行解密，返回解密后的数据
     *
     * @param string $data 要解密的数据
     *
     * @return string
     *
     */
    public function aesdecrypt($data , $key , $iv='sciCuBC7orQtDhTO') {
        return openssl_decrypt(base64_decode($data), "AES-128-CBC", $key , OPENSSL_RAW_DATA, $iv);
    }

    /**
     * 获取私钥
     * @return bool|resource
     */
    private static function getPrivateKey() {
        $privateKey = file_get_contents(app()->basePath().'/rsa_private_key.pem');
        return openssl_pkey_get_private($privateKey);
    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    private static function getPublicKey() {
        $publicKey = file_get_contents(app()->basePath().'/rsa_public_key.pem');
        return openssl_pkey_get_public($publicKey);
    }

    /**
     * 私钥加密
     * @param string $data
     * @return null|string
     */
    public static function privateEncrypt($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        $EncryptStr = '';
        foreach (str_split($data, 117) as $chunk) {
            openssl_private_encrypt($chunk, $encryptData, self::getPrivateKey());
            $EncryptStr .= $encryptData;
        }

        return base64_encode($EncryptStr);
    }

    /**
     * 公钥加密
     * @param string $data
     * @return null|string
     */
    public static function publicEncrypt($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_public_encrypt($data,$encrypted,self::getPublicKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * 私钥解密
     * @param string $encrypted
     * @return null
     */
    public static function privateDecrypt($encrypted = '')
    {
        $DecryptStr = '';

        foreach (str_split(base64_decode($encrypted), 128) as $chunk) {

            openssl_private_decrypt($chunk, $decryptData, self::getPrivateKey());

            $DecryptStr .= $decryptData;
        }

        return $DecryptStr;
    }


    /**
     * 公钥解密
     * @param string $encrypted
     * @return null
     */
    public static function publicDecrypt($encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, self::getPublicKey())) ? $decrypted : null;
    }

    /**
     * 生成数字签名
     * 使用方法示例
     * openssl_sign('您要签名的数据' , '签名后返回来的数据' , '签名的钥匙/可以是公钥签名也可以是私钥签名,一般是私钥加密,公钥解密')
     * @param  $data  待签数据
     * @return String 返回签名
     */
    public static function sign($data=''){
        //获取私钥
        $pkeyid = self::getPrivateKey();
        if (empty($pkeyid)) {
            return false;
        }

        //生成签名方法
        $verify = openssl_sign($data, $signature, $pkeyid , OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    /*
     * 数字签名验证
     */
    public function verifySign($data , $sign) {
        $pub_id = openssl_get_publickey(self::getPublicKey());
        $res    = openssl_verify($data, base64_decode($sign), $pub_id , OPENSSL_ALGO_SHA256);
        return $res;
    }


    /*
     * @param description 客户端RSA加密方法示例
     * @param $key  aes随机加密的key
     *        $data aes加密后的数据
     * @param author duzhijian
     * @param ctime  2020-04-15
     * return array
     */
    public function rsaencrypt($key = '' , $data = ''){
        //判断key是否为空
        if(!$key || empty($key)){
            return response()->json(['code'=>201,'msg'=>'key不合法或为空']);
        }

        //判断data是否为空
        if(!$data || empty($data)){
            return response()->json(['code'=>201,'msg'=>'data不合法或为空']);
        }

        //判断data是否为数组格式
        if(is_array($data)){
            $result = json_encode($data);
        } else {
            $result = $data;
        }

        //将数据进行AES加密处理
        $body = $this->aesencrypt($result , $key);

        //生成签名
        $sign = self::sign($body);

        //将key进行RSA加密处理
        $token= self::privateEncrypt($key);

        //返回数据数组
        return json_encode(array('token' => $token , 'body' => $body , 'sign' => $sign));
    }

    /*
     * @param description 服务端RSA解密方法示例
     * @param $token  rsa加密的key
     *        $body   aes加密后的数据
     *        $sign   签名
     * @param author duzhijian
     * @param ctime  2020-04-15
     * return array
     */
    public function rsadecrypt($token = '' , $body = '' , $sign = ''){
        //判断key是否为空
        if(!$token || empty($token)){
            echo response()->json(['code'=>201,'msg'=>'token不合法或为空']);
            exit;
        }

        //判断data是否为空
        if(!$body || empty($body)){
            echo response()->json(['code'=>201,'msg'=>'body不合法或为空']);
            exit;
        }

        //数据验签处理
        if($sign && !empty($sign)){
            $sign_st = self::verifySign($body , $sign);
            //判断是否验签成功
            if($sign_st <= 0){
                echo response()->json(['code'=>202,'msg'=>'签名验证失败']);
                exit;
            }
        }

        //将key进行RSA解密处理(最后得到aes的明文key)
        $key = self::publicDecrypt($token);

        //再将aes进行数据解密处理
        $data= $this->aesdecrypt($body , $key);

        //返回数据数组
        return json_decode($data , true);
    }

    /*
     * @param description 服务端RSA+AES示例
     * @param $key   加密的key
     *        $arr   加密的数据
     * @param author duzhijian
     * @param ctime  2020-04-15
     * return array
     */
    public function RsaCryptDemo($key , $arr){
        //对数据进行加密处理(生成加密后的数据字符串)
        $encrypt_data =  $this->rsaencrypt($key , $arr);

        //判断加密的数据信息是否为空
        if(empty($encrypt_data)){
            return response()->json(['code'=>201,'msg'=>'加密后的数据为空']);
        }

        //进行json解码处理转化成数组
        $array_data   = json_decode($encrypt_data , true);

        //判断token是否合法或为空
        if(!isset($array_data['token']) || empty($array_data['token'])){
            return response()->json(['code'=>201,'msg'=>'token值不合法']);
        }

        //判断body是否合法或为空
        if(!isset($array_data['body']) || empty($array_data['body'])){
            return response()->json(['code'=>201,'msg'=>'body值不合法']);
        }

        //对数据进行解密处理
        $data_list = $this->rsadecrypt($array_data['token'] , $array_data['body'] , $array_data['sign']);

        echo "<pre>";
        print_r($data_list);
    }

    /*
     * @param description 服务端数据解密
     * @param $key   加密的key
     *        $arr   加密的数据
     * @param author duzhijian
     * @param ctime  2020-04-15
     * return array
     */
    public function Servicersadecrypt($data){
        //判断token是否合法或为空
        if(!isset($data['token']) || empty($data['token'])){
            echo response()->json(['code'=>201,'msg'=>'token值不存在或为空']);
            exit;
        }

        //判断body是否合法或为空
        if(!isset($data['body']) || empty($data['body'])){
            echo response()->json(['code'=>201,'msg'=>'body值不存在或为空']);
            exit;
        }

        //判断签名是否合法或为空
        if(!isset($data['sign']) || empty($data['sign'])){
            echo response()->json(['code'=>201,'msg'=>'sign值不存在或为空']);
            exit;
        }

        //对数据进行解密处理
        return $this->rsadecrypt($data['token'] , $data['body'] , $data['sign']);
    }



    public function Test(){
        $key = time().rand(1,10000);
        $arr = ['status' => '200', 'info' => 'success', 'data' => [['id' => 1, 'name' => '大房间', '2' => '小房间']]];
        $arr = json_encode($arr);
        $aaa = self::sign($arr);

        $ccc = $this->rsaencrypt($key , $arr);
        $ccc = json_decode($ccc , true);
        echo "<pre>";
        print_r($ccc);
        //$bbb = "SBxzwN05LdOY0vswkWieoNj6KnQCsVbHT5Fi4TLAQe5yfZrod3UbZz90od0DinpiEi1+vGMTlZ+Ck9LcaWjGS4yBa1XYO4BI9JtTvJN+JqxKlvZIyHX/ip9WfzPqPtOwUuRt/YSU7sLslpvAbG0hvVH2jVS1OvZdnDA6nbusocs=";
        $token= "EGzWzR27RuS6bA8Haj3RZAdyEseTGgYd1pYubaMN2I2Z9vykrrohxf1Xf2A2BNQA4VsFPjyv4xnkxqKdQZ6fevgQ3pzKy2+RdsCrd8ap68RnXto5o7G8QCX8HNpTQiPmONJl1tjyWB/IVauq7MN/sLg1kViEOxMRSQuOivQAhLg=";
        $body = "wz7Jk0s++OXxJBWt2l5V6hjr9oOHA0Vf86wzvJXhi6Zs3y/nrdGONqUyAH8wG15L4FmIvs4sLUBcQDNN27Gh8Gtrp2hcqij3cbKF0t/FC8eWCJa2GATJ+w6pZbi9+D89OFUnhSCZFFNo9P8dDFjpFg==";
        $sign = "D9OmydE2zhpcS9Zd12JseO/ayiMRT4PnId4wCcRbvUaU7ehnuzK4hqno+VHVhiAzgVp2lTiRbHOWFTgQUdhCfFOSIo7op999wlT47mC8Xqjv+atKEnPZzC0MfvxZbmw62bpiRwGWmUUvMgVnQDCf9OaOEjN4ldcPW8izDsuLGrc=";
        //$bbb = self::verifySign($body , $sign);

        $dddd = $this->rsadecrypt($token , $body , $sign);

        echo "<pre>";
        print_r($dddd);
        exit;
        $this->RsaCryptDemo($key , $arr);
        exit;
    }
}
