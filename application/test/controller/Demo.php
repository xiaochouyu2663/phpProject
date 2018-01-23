<?php
namespace app\test\controller;
use think\Db;
class Demo
{
    public function index()
    {
       echo 1;
    }
    public function linkdb()
    {
        // 利用配置文件连接数据库的操作
        // 1.查询数据
        if(!isset($_GET['UserId'])){
            $result=array(
                'code'    => '400',
                'message' => 'error',
                'data'    => '缺少参数'
            );
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $UserId=$_GET['UserId'];
        $result=[];
        $data=Db::table('kzdd_address')->where('UserId',$UserId)->select();
        $result['code'] = 200;
        $result['message'] = 'success';
        $result['data'] = $data;
        return json_encode($result);
    }
}