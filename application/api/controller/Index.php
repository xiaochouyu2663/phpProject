<?php
namespace app\api\controller;
use think\Validate;
header('Access-Control-Allow-Origin:http://localhost:8080');
// header("Access-Control-Allow-Methods:GET,POST,PATCH,PUT,OPTIONS");
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With,content-type;charset=utf-8');
header("Content-type: text/html; charset=utf-8");
class Index extends \think\Controller
{
    /**function：登录 */
    /**author:sherry@2018/1/23 */
    public function login(){
        // 接收到的参数
        $username = input('username','');
        $password = input('password','');
        // 验证参数
        $result = $this->validate(
            [
                'username' => $username,
                'password' => $password
            ],[
                'username' => 'require|regex:^[1][0-9]{10}$',	// 验证1开头且11位数字即可
                'password' => 'require'
            ],[
                'username.require' => '请输入手机号',
                'username.regex'   => '手机号格式不正确',
                'password.require' => '请输入密码'
            ]);
        
        if($result!==true){
            return json_encode(['code'=>1,'msg'=>$result,'data'=>null],JSON_UNESCAPED_UNICODE);
        }

        $row=db('user')
             ->where('username',$username)
             ->find();

        /** 判断此账号是否存在，不存在则返回提示信息 **/
        if(!$row){
            return json_encode(['code'=>400,'msg'=>'用户不存在','data'=>null],JSON_UNESCAPED_UNICODE);
        }
        
        if(md5($password)==$row['password']){
            db('user')->where('userId',$row['userId'])->setField('loginTime',date('Y-m-d H:i:s'));
            return json_encode(['code'=>200,'msg'=>'登录成功','data'=>$row],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(['code'=>400,'msg'=>'用户名或密码错误','data'=>null],JSON_UNESCAPED_UNICODE);
        }
    }

    /**function:注册 */
    /**author:sherry@2018/1/24 */
    public function register()
    {
        $username=input('username','');
        $password=input('password','');
        $vcode   =input('vcode','');
        $alias = input('alias','');
        $data=[
            'username'=>$username,
            'password'=>md5($password),
            'passwords'=>$password,
            'alias'=>$alias,
            'registTime'=>date('Y-m-d H:i:s'),
        ];
        $data['userId']=db('user')->insertGetId($data);
        echo $data['userId'];
    }

    /**function:短信发送 */
    /**author:sherry@2018/1/24 */
    public function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }

    /**function:发送短信 */
    /**author:sherry@2018/1/24 */
    public function sendMessage(){
        $mobile = input('mobile','');

        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
        
        $vcode = rand(100000,999999);

        $smsConf = array(
            'key'   => '81c4d6cd32d97df295642c8ee1991e91', //您申请的APPKEY
            'mobile'    => $mobile, //接受短信的用户手机号码
            'tpl_id'    => '61708', //您申请的短信模板ID，根据实际情况修改
            'tpl_value' =>'#code#='.$vcode //您设置的模板变量，根据实际情况修改
        );
       
        $content =$this->juhecurl($sendUrl,$smsConf,1); //请求发送短信
       
        if($content){
            $result = json_decode($content,true);
            $error_code = $result['error_code'];
            if($error_code == 0){
                //状态为0，说明短信发送成功
                return json_encode(['code'=>200,'msg'=>'短信发送成功','data'=>$result],JSON_UNESCAPED_UNICODE);
            }else{
                //状态非0，说明失败
                return json_encode(['code'=>1,'msg'=>'短信发送失败，'.$result['reason']],JSON_UNESCAPED_UNICODE);
            }
        }else{
            //返回内容异常，以下可根据业务逻辑自行修改
            return json_encode(['code'=>1,'msg'=>'请求发送短信失败'],JSON_UNESCOPED_UNICODE);
        }
    }

    public function updatePwd(){
        
    }    
}