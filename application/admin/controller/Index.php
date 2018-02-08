<?php
namespace app\admin\controller;

use think\Controller;
use think\View;

class Index extends Controller{

    /**引入的公共资源文件 */
    public function lib(){
        return $this->fetch();
    }

    /**公共的头部文件 */
    public function top(){
        return $this->fetch();
    }
    
    /**加载页面 */
    public function index(){

        $username = '小丑鱼';

        $this -> assign('username',$username);
        
        return $this -> fetch();
    }
    
    public function wap(){
        
        return $this -> fetch();
    }
}
