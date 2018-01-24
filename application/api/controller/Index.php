<?php
namespace app\api\controller;
use think\Validate;
header('Access-Control-Allow-Origin:http://localhost:8080');
// header("Access-Control-Allow-Methods:GET,POST,PATCH,PUT,OPTIONS");
header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With,content-type;charset=utf-8');
header("Content-type: text/html; charset=utf-8");
class Index
{
    /**function：登录 */
    /**author:sherry@2018/1/23 */
    public function login(){
        $username = input('username','');
        $password = input('password','');
        $row=db('user')
             ->where('username',$username)
             ->find();

        /** 判断此账号是否存在，不存在则返回提示信息 **/
        if(!$row){
            return json_encode(['code'=>400,'msg'=>'用户不存在','data'=>null],JSON_UNESCAPED_UNICODE);
        }
        
        if(md5($row['userId'].$password)==$row['password']){
            db('user')->where('userId',$row['userId'])->setField('loginTime',date('Y-m-d H:i:s'));
            return json_encode(['code'=>200,'msg'=>'登录成功','data'=>$row],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(['code'=>400,'msg'=>'用户名或密码错误','data'=>null],JSON_UNESCAPED_UNICODE);
        }
    }
}