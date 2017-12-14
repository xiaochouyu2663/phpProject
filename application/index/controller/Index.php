<?php
namespace app\index\controller;
use wechat\Jssdk;
use think\Db;

$appid = 'wxbf43be700bc7184a';
$appsceret = '8b4c3edd3f70ed75015501cbc4029e7e';
$jssdk=new Jssdk($appid,$appsceret);
// $jssdk ->getAccessToken();
class Index
{
    public function index()
    {
        $access_token=json_decode(file_get_contents(EXTEND_PATH.'wechat/access_token.json'));
        var_dump($access_token);
    }
    public function getData()
    {
        $data=Db::table('user')->select();
        return view();
    }
}
