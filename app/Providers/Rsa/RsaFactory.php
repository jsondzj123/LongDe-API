<?php

namespace App\Providers\Rsa;
use Illuminate\Http\Request;
 
class RsaFactory {
    private static $PRIVATE_KEY =
        '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQChMx+AdLgn/mvU2uTMvV7L2b/VraaEab3ABvhf8qALEmPg+sPu
LmYxjFt9N7jz0IbXzZq0d5WVTaGXhqnWrigGCxGi9losvIcQ33mms+9GgYbTasFo
NbM3BhjDgqdXVTsU08sfIBNJ+PkfPuwcj+UEUA+MziLaGyxa1IQ456HhDQIDAQAB
AoGBAJzB7Ulqt8bUqCHm94aORQgxaVautnafqZF6dcBAXhhGOvCGi1AsuN5IIpQD
Qw1+ZBKp8165x7HYO2Lx5mlJFMs4W6iM/nXbR2j/WfihsDWxhAmvUevbZ6wz22+b
g9/QMO9sUVM0+W0WymMlKzaj7HX4kiL8ynn5JlkTsoiTZZT1AkEA0eU5Hmdaybg9
aGe/c7BCtrNsNpDPkzfbGzNF2mDYk91qWKMod4F90oynOkn5ZykaC71e6lr5K6cy
xwS1w/TI5wJBAMSbqEqaNVCucNj6QpBqboi2hmO3FK3DXw14oaYXnu0hfpyW8qkU
xY65ZHQh72zU9O+DGkeWH1n5a5OMDUe/Q+sCQHsOsAFCSTkQ2nfWs6lJAqQI533K
QtimG8CDvAV/WBrA6nOTHMuL0M/bhMOo0R8JOur9GKO/uGw+d4e1HDgJ0KsCQEWG
IbnX1DimpwMjZDx7VoEDwnwqdqaHquoxmUAJpEqIiKRJAKBn1wCEcJBcm7TpjX/Q
5Y8g+A8yEyeG4/9WFGcCQQCJSc17ORFF/70FLrQ+u9k5Qx+boH/Go/1sl/ywDu9I
ASEr/L8MpfsLiubluFvN8A2+TezECAfwc9A5MQx0OBPv
-----END RSA PRIVATE KEY-----';
    private static $PUBLIC_KEY = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChMx+AdLgn/mvU2uTMvV7L2b/V
raaEab3ABvhf8qALEmPg+sPuLmYxjFt9N7jz0IbXzZq0d5WVTaGXhqnWrigGCxGi
9losvIcQ33mms+9GgYbTasFoNbM3BhjDgqdXVTsU08sfIBNJ+PkfPuwcj+UEUA+M
ziLaGyxa1IQ456HhDQIDAQAB
-----END PUBLIC KEY-----';
    
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
    private static function getPrivateKey()
    {
        $privateKey = self::$PRIVATE_KEY;
        return openssl_pkey_get_private($privateKey);
    }
 
    /**
     * 获取公钥
     * @return bool|resource
     */
    private static function getPublicKey()
    {
        $publicKey = self::$PUBLIC_KEY;
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
    
        //将key进行RSA加密处理
        $token= self::privateEncrypt($key);

        //返回数据数组
        return json_encode(array('token' => $token , 'body' => $body));
    }
    
    /*
     * @param description 服务端RSA解密方法示例
     * @param $token  rsa加密的key
     *        $body   aes加密后的数据
     * @param author duzhijian
     * @param ctime  2020-04-15
     * return array
     */
    public function rsadecrypt($token = '' , $body = ''){
        //判断key是否为空
        if(!$token || empty($token)){
            return response()->json(['code'=>201,'msg'=>'token不合法或为空']);
        }
        
        //判断data是否为空
        if(!$body || empty($body)){
            return response()->json(['code'=>201,'msg'=>'body不合法或为空']);
        }

        //将key进行RSA解密处理(最后得到aes的明文key)
        $key = self::publicDecrypt($token);
        
        //再将aes进行数据解密处理
        $data= $this->aesdecrypt($body , $key);

        //返回数据数组
        return json_encode(array('key' => $key , 'data' => $data));
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
        $data_list = $this->rsadecrypt($array_data['token'] , $array_data['body']);
        
        echo "<pre>";
        print_r($data_list);
    }
    
    
    
    public function Test(){
        $key = time().rand(1,10000);
        $arr = ['status' => '1', 'info' => 'success', 'data' => [['id' => 1, 'name' => '大房间', '2' => '小房间']]];
        $this->RsaCryptDemo($key , $arr);
        exit;
    }
}
