<?php
namespace app\api\controller;
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
            return json_encode(['code'=>400,'msg'=>'登录成功','data'=>null],JSON_UNESCAPED_UNICODE);
        }else{
            return json_encode(['code'=>400,'msg'=>'用户名或密码错误','data'=>null],JSON_UNESCAPED_UNICODE);
        }
    }
}