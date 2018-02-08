<?php
namespace app\api\controller;
use think\Validate;
header('Access-Control-Allow-Origin:http://localhost:8080');
// header("Access-Control-Allow-Methods:GET,POST,PATCH,PUT,OPTIONS");
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With,content-type;charset=utf-8');
header("Content-type: text/html; charset=utf-8");
class Account extends \think\Controller
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

        /**对接收的参数进行验证 */
        $result = $this ->validate([
                    'username'  => $username,
                    'password'  => $password,
                    'vcode'     => $vcode,
                    'alias'     => $alias
                ],[
                    'username'  => 'require|regex:^[1][13478][0-9]{9}$|unique:user',
                    'password'  => 'require|min:6|alphaNum',
                    'vcode'     => 'require|length:6',
                    'alias'     => 'require'  
                ],[
                    'username.require'  => '请输入手机号',
                    'username.regex'  => '手机号格式不正确',
                    'username.unique'  => '用户已存在',
                    'password.require'  => '请输入密码',
                    'password.min'  => '密码最低为6位',
                    'password.alphaNum'  => '密码必须为英文和数字组合',
                    'vcode.require'     => '请输入验证码',
                    'vcode.length'     => '请输入6位验证码',
                    'alias.require'     => '请输入姓名'  
                ]);
        if($result!==true){
            return json_encode(['code'=>1,'msg'=>$result,'data'=>null],JSON_UNESCAPED_UNICODE);
        }

        /**查找最近一次并且未使用的验证码 */
        $row=db('system_vcode') -> where(['mobile' => $username,'isUsed' =>0])
                                -> order('addtime desc')
                                -> find();
        if(!$row||$row['vcode']!=$vcode){
            return json_encode(['code'=>1,'msg'=>'验证码错误','data'=>null],JSON_UNESCAPED_UNICODE);
        }

        /**验证码超时验证 */
        if((time() - strtotime($row['addtime'])) > 60 * 10){
            return json_encode(['code'=>1,'msg'=>'验证码过期','data'=>null],JSON_UNESCAPED_UNICODE);
        }

        db('system_vcode') -> where('id',$row['id'])
                           ->setField('isUsed',1);
        $data=[
            'username'=>$username,
            'password'=>md5($password),
            'passwords'=>$password,
            'alias'=>$alias,
            'registTime'=>date('Y-m-d H:i:s'),
            'loginTime'=>date('Y-m-d H:i:s')
        ];
        $data['userId']=db('user')->insertGetId($data);
         return json_encode(['code'=>1,'msg'=>'注册成功','data'=>[
            'username'=>$username,
            'alias'=>$alias,
            'registTime'=>date('Y-m-d H:i:s'),
            'loginTime'=>date('Y-m-d H:i:s')
         ]],JSON_UNESCAPED_UNICODE);
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

        $result = $this -> validate([
                    'mobile' => $mobile
                ],[
                    'mobile' => 'require|regex:^[1][34578][0-9]{9}$'
                ],[
                    'mobile.require' => '请输入手机号',
                    'mobile.regex'   => '手机号格式不正确'
                ]);
        if($result!==true){
            return json_encode(['code'=>1,'msg'=>$result,'data'=>null],JSON_UNESCAPED_UNICODE);
        }

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
                $data = [
                    'mobile' => $mobile,
                    'vcode'  => $vcode,
                    'isUsed' => 0,
                    'addtime'=> date('Y-m-d H:i:s')
                ];
                db('system_vcode') -> insert($data);
                //状态为0，说明短信发送成功 成功示例：{"reason":"操作成功","result":{"sid":"180125224103103011100006","fee":1,"count":1},"error_code":0}
                return json_encode(['code'=>200,'msg'=>'短信发送成功','data'=>null],JSON_UNESCAPED_UNICODE);
            }else{
                //状态非0，说明失败
                return json_encode(['code'=>1,'msg'=>'短信发送失败，'.$result['reason']],JSON_UNESCAPED_UNICODE);
            }
        }else{
            //返回内容异常，以下可根据业务逻辑自行修改
            return json_encode(['code'=>1,'msg'=>'请求发送短信失败'],JSON_UNESCOPED_UNICODE);
        }
    }

    /**function:重置密码 */
    public function updatePwd(){
        $username = input('username','');
        $password = input('password','');
        $vcode    = input('vcode','');
        $result   =$this ->validate([
            'username' => $username,
            'password' => $password,
            'vcode'    => $vcode
        ],[
            'username' => 'require|regex:^[1][0-9]{10}$',
            'password' => 'require',
            'vcode'    => 'require'
        ],[
            'username.require'  => '请输入手机号',
            'username.regex'    => '请输入正确的手机号',
            'password.require'  => '请输入密码',
            'vcode.require'     => '请输入验证码'
        ]);
        if($result!==true){
            return json_encode(['code'=>1,'msg'=>$result,'data'=>null],JSON_UNESCAPED_UNICODE);
        }

        // 查询用户是否存在
        $row=db('user') -> where('username',$username)
                        -> find();
        if($row==null){
            return json_encode(['code'=>1,'msg'=>'用户不存在','data'=>null],JSON_UNESCAPED_UNICODE);
        }

        /**查询发送的并且没有使用的最后一条验证码 */
        $rows = db('system_vcode') -> where('mobile',$username)
                                   -> where('isUsed',0)
                                   ->order('addtime desc')
                                   ->find();
        if(!$rows||$rows['vcode']!=$vcode){
            return json_encode(['code'=>1,'msg'=>'验证码错误','data'=>null],JSON_UNESCAPED_UNICODE);
        } 
        
        /**验证码是否超时 */
        $nowtime = time();
        if(($nowtime - strtotime($rows['addtime']))> 60 * 10){
            return json_encode(['code'=>1,'msg'=>'验证码过期','data'=>null],JSON_UNESCAPED_UNICODE);
        };

        /**将此次所用的验证码的状态改为已用 */
        db('system_vcode') -> where('id',$rows['id'])
                           -> setField('isUsed',1);
        /**修改用户的密码 */
        db('user') -> where('username',$username)
                   -> update(['password'=>md5($password),'passwords'=>$password]);
                   
        return json_encode(['code'=>200,'msg'=>'密码重置成功','data'=>null],JSON_UNESCAPED_UNICODE);
        
    } 

    /**function:获取省 2018-1-27*/
    public function province(){

        $data = db('province') -> field('ProvinceId,ProvinceDes') 
                               -> order('ProvinceId')
                               -> select();
        if(!$data){
            return json_encode(['code'=>1,'msg'=>'查询不成功','data'=>null],JSON_UNESCAPED_UNICODE);
        }
        return json_encode(['code'=>200,'msg'=>'查询成功','data'=>$data],JSON_UNESCAPED_UNICODE);
    }   

    /**function:获取市 2018-1-27*/
    public function cities(){
        $ProvinceId=input('ProvinceId','');
        $data = db('cities') -> where('ProvinceId',$ProvinceId)
                             -> field('CityId,CityDes')
                             -> select();
        if(!$data){
            return json_encode(['code'=>1,'msg'=>'查询不成功','data'=>null],JSON_UNESCAPED_UNICODE);
        }
        return json_encode(['code'=>200,'msg'=>'查询成功','data'=>$data],JSON_UNESCAPED_UNICODE);
    }  

    /**function:获取区 2018-1-27*/
    public function areas(){
        $CityId=input('CityId','');
        $data = db('areas') -> where('CityId',$CityId)
                            -> field('AreaId,AreaDes')
                            -> select();
        if(!$data){
            return json_encode(['code'=>1,'msg'=>'查询不成功','data'=>null],JSON_UNESCAPED_UNICODE);
        }
        return json_encode(['code'=>200,'msg'=>'查询成功','data'=>$data],JSON_UNESCAPED_UNICODE);
    }  

    /**function:新增地址 2018-1-27*/
    public function newAddress(){
        $receiver   = input('receiver','');
        $provinceId = input('provinceId','');
        $cityId     = input('cityId','');
        $areaId     = input('areaId','');
        $address    = input('address','');
        $mobile     = input('mobile','');
        $postcode   = input('postcode','');

        $result = $this -> validate([
            'receiver'   => $receiver,
            'provinceId' => $provinceId,
            'cityId'     => $cityId, 
            'areaId'     => $areaId,
            'address'    => $address,
            'mobile'     => $mobile,
            'postcode'   => $postcode
        ],[
            'receiver'   => 'require',
            'provinceId' => 'require',
            'cityId'     => 'require', 
            'areaId'     => 'require',
            'address'    => 'require',
            'mobile'     => 'require|regex:^[1][13478][0-9]{9}$',
            'postcode'   => 'length:6|number'
        ],[
            'receiver.require'   => '请填写收货人姓名',
            'provinceId.require' => '请选择省',
            'cityId.require'     => '请选择市', 
            'areaId.require'     => '请选择区',
            'address.require'    => '请填写详细地址',
            'mobile.require'     => '请填写手机号',
            'mobile.regex'       => '手机号格式不正确',
            'postcode.length'    => '邮编是六位数字',
            'postcode.number'    => '邮编是六位数字'
        ]);

        if($result!==true){
            return json_encode(['code'=>1,'msg'=>$result,'data'=>null],JSON_UNESCAPED_UNICODE);
        }
    }

}