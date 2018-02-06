<?php
namespace app\api\controller;

class Products {
    public function indexBanner(){
    	$data = db('goods') -> where('flag',0)
    						-> select();
    	if(!$data){
    		return json_encode(['code'=>1,'msg'=>'fail','data'=>null],JSON_UNESCAPED_UNICODE);
    	}
    	return json_encode(['code'=>200,'msg'=>'success','data'=>$data],JSON_UNESCAPED_UNICODE);
    }
}