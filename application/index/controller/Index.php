<?php
namespace app\index\controller;
use org\wechat\Jssdk;
$appid = 'wxbf43be700bc7184a';
$appsceret = '8b4c3edd3f70ed75015501cbc4029e7e';
$jssdk = new Jssdk($appid,$appsceret);
// $jssdk -> test();
Jssdk::test();
// var_dump($jssdk);
class Index
{
    public function index()
    {
        //    var_dump($jssdk);
    }
    public function test()
    {
       echo 1;
    }
}
