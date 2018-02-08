<?php
namespace app\wechat\controller;
use wechat\Jssdk;


class Login {
	public  function index(){

		return view();
	}
	public function getCode(){
		$code = input('code','');
		echo $code;
	}
}