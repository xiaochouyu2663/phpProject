<?php
namespace app\api\controller;

class Partner {
	public function index(){
		$content = input('content','');
		return 'ok';
	}
	/**功能：获取一阶合作伙伴 */
    public function partnerFirst(){
    	$nowPage = input('nowPage',1);   //当前页默认为第一页
    	$pageCount = input('pageCount',5);   //每页默认5条数据
    	
        $result = db('partner_first') -> where('userId',213543)
        							  -> limit(($nowPage-1) * $pageCount,$pageCount)
                                      -> select();
        // dump($result);
       	return json_encode(['code'=>200,'msg'=>'success','data'=>$result],JSON_UNESCAPED_UNICODE);
    }
}