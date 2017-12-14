<?php
namespace app\index\controller;
<<<<<<< HEAD
use wechat\Jssdk;
use think\Db;

$appid = 'wxbf43be700bc7184a';
$appsceret = '8b4c3edd3f70ed75015501cbc4029e7e';
$jssdk=new Jssdk($appid,$appsceret);
// $jssdk ->getAccessToken();
=======
use org\wechat\Jssdk;
$appid = 'wxbf43be700bc7184a';
$appsceret = '8b4c3edd3f70ed75015501cbc4029e7e';
$jssdk = new Jssdk($appid,$appsceret);
// $jssdk -> test();
Jssdk::test();
// var_dump($jssdk);
>>>>>>> 6351a6c6822a101280121a53a9eeb87a677b6717
class Index
{
    public function index()
    {
<<<<<<< HEAD
        $access_token=json_decode(file_get_contents(EXTEND_PATH.'wechat/access_token.json'));
        var_dump($access_token);
    }
    public function getData()
    {
        $data=Db::table('user')->select();
        return view();
=======
        //    var_dump($jssdk);
    }
    public function test()
    {
       echo 1;
>>>>>>> 6351a6c6822a101280121a53a9eeb87a677b6717
    }
}
