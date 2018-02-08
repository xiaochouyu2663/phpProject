<<<<<<< HEAD
<?php
namespace app\index\controller;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');
header("Content-Type: text/html; charset=utf8");
use think\Db;
use think\Session;
class Index
{   public function index()
    {   
        echo "hello\r\n";//unix系统使用\n；windows系统下\r\n 
        echo "world!"; 
    }   
    public function login()
    {   
        $username=isset($_GET['username']) ? $_GET['username'] : 'admin';
        $password=isset($_GET['password']) ? $_GET['password'] : '123456';
        $data=Db::table('w_user')->where('username',$username)->where('password',$password)->find();
        

        if($data){

            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
            $string=time();
            for($len = count($chars);$len>0;$len--)
            {
                $position=rand()%strlen($chars);
                $position2=rand()%strlen($string);
                $string=substr_replace($string,substr($chars,$position,1),$position2,0);
            }
            $data['token']=$string;
            Session::set('uid',$string);
            $uid=Session::get('uid');
            $result=[
                'code' => 200,
                'msg'  => 'success',
                'data' => $data
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }else{
            $result=[
                'code' => 400,
                'msg'  => 'err',
                'data' => '账号或密码错误'
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    public function getBanner(){
        $con=mysqli_connect('localhost','root','root');
        if(!$con){
            echo '连接数据不成功';
        }

        
        mysqli_query($con,'set names utf8');             //写库
        mysqli_query($con,"set character set 'utf8'");  //读库


        mysqli_select_db($con,'kzdd');
        $selectStr='select * from products';
        // $selectStr='INSERT INTO test (name , id) VALUES ("哈哈哈","送达方式")';
        $res=mysqli_query($con,$selectStr);
        if(!$res){
            echo '无法查询数据';
        }
        $result=[];
        while($row=mysqli_fetch_array($res,MYSQL_ASSOC)){
            // var_dump($row);
            Array_push($result,$row);
        }
        echo json_encode($result,JSON_UNESCAPED_UNICODE);  
        //用PHP的json_encode来处理中文的时候, 中文都会被编码, 变成不可读的, 类似”\u***”的格式，如果想汉字不进行转码,
            // JSON_UNESCAPED_UNICODE, 故名思议, 就是说, Json不要编码Unicode.
    }
    public function productList(){
        $con=mysqli_connect('localhost','root','root');
        $showArr=[];
        if(!$con){
            echo '连接数据不成功';
            $showArr['code']=400;
            $showArr['count']=0;
            $showArr['msg']='查询失败';
        }

        
        mysqli_query($con,'set names utf8');             //写库
        mysqli_query($con,"set character set 'utf8'");  //读库


        mysqli_select_db($con,'kzdd');
        $nowPage=isset($_GET['nowPage'])&&$_GET['nowPage']>1 ? $_GET['nowPage'] : 1;
        $pageSize=isset($_GET['pageSize'])&&$_GET['pageSize']>1 ? $_GET['pageSize'] : 2;
        $selectStr='select * from products limit '.($nowPage-1)*$pageSize.','.$pageSize.'';

        $res=mysqli_query($con,$selectStr);
        $result=[];
        while($row=mysqli_fetch_array($res,MYSQL_ASSOC)){
            Array_push($result,$row);
        }
        
        
        $showArr['code']=200;
        $showArr['count']=count($result);
        $showArr['data']=$result;
        echo json_encode($showArr,JSON_UNESCAPED_UNICODE);
          
        //用PHP的json_encode来处理中文的时候, 中文都会被编码, 变成不可读的, 类似”\u***”的格式，如果想汉字不进行转码,
            //JSON_UNESCAPED_UNICODE, 故名思议, 就是说, Json不要编码Unicode.
    }
    public function yreight(){
        if(isset($_GET['type'])){
            $result=[];
            $result['code']=200;
            $result['msg']='success';
            switch ($_GET['type']){
                case 1:
                $result['data']=10;
                break;
                case 2:
                $result['data']=15;
                break;
                default:
                $result['data']=10;
                break;
            }
        }else{
            $result=array(
                'code' => 400,
                'msg'  => 'error'
            );
        } 
        
        echo json_encode($result);
    }
    public function creatOrder(){
        $con=mysqli_connect('localhost','root','root');
        $showArr=[];
        if(!$con){
            echo '连接数据不成功';
            $showArr['code']=400;
            $showArr['count']=0;
            $showArr['msg']='查询失败';
        }

        
        mysqli_query($con,'set names utf8');             //写库
        mysqli_query($con,"set character set 'utf8'");  //读库


        mysqli_select_db($con,'kzdd');
        if(!isset($_GET['UserId'])||!isset($_GET['Freight'])||!isset($_GET['DeliveryType'])) {
            $result=array(
                'code' => 400,
                'msg'  => 'error',
                'data' => '缺少参数'
            );
            return json_encode($result);
        }
        $Id=date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $OrderDate=date('Y-m-d H:i:s');
        $OrderState=1;
        $UserId=$_GET['UserId'];
        $Freight=$_GET['Freight'];
        $DeliveryType=$_GET['DeliveryType'];
        $sqlstr='insert into kzdd_orders (Id, OrderDate,OrderState,UserId,Freight,DeliveryType) values ("'.$Id.'","'.$OrderDate.'","'.$OrderState.'","'.$UserId.'","'.$Freight.'","'.$DeliveryType.'")';
        // $sqlstr='insert into kzdd_orders (Id, OrderDate,OrderState,UserId,Freight,DeliveryType) values ("123","ss","1","1","34","1")';
        $res=mysqli_query($con,$sqlstr); 
        if($res){
            $result=array(
                'code' => 200,
                'msg'  => 'success',
                'data' => '订单号：'.$Id
            );
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }else{
            $result=array(
                'code' => 400,
                'msg'  => 'error',
                'data' => '查询出错'
            );
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    public function address()
    {
        if(!isset($_GET['UserId'])){
            $result=array(
                'code'    => 400,
                'message' => 'error',
                'data'    => '缺少参数'
            );
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $UserId=$_GET['UserId'];
        $data=Db::table('kzdd_address')->where('UserId',$UserId)->order('Time desc')->select();
        $result=array(
            'code'    => 200,
            'message' => 'success',
            'data'    => $data
        );
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }
    public function addAddress()
    {

        if(!isset($_GET['UserId'])||!isset($_GET['AddressList'])){
            $result=[
                'code' => 400,
                'data' => 'err',
                'msg'  => '缺少参数'
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $UserId=$_GET['UserId']; 
        //$AddressList='{"receiver":"爱上房顶上","receiverPhone":"13125454121","provinceId":"110000","cityId":"110100","countId":"110101","address":"舒服撒","isDefault":1}';
        $AddressList=json_decode($_GET['AddressList'],true);
        // 通过省市区id找出省市区对应信息
        $province=Db::table('kzdd_provinces')->where('ProvinceId',$AddressList['provinceId'])->find();
        $city=Db::table('kzdd_cities')->where('CityId',$AddressList['cityId'])->find();
        $count=Db::table('kzdd_areas')->where('AreaId',$AddressList['countId'])->find();
        if($AddressList['isDefault']==1){
            $data=Db::table("kzdd_address")->where('UserId',$UserId)->where('IsDefault',1)->update(['IsDefault'=>0]);
        }
        $data=[
            'UserId' => $UserId,
            'Receiver' => $AddressList['receiver'],
            'ReceiverPhone' => $AddressList['receiverPhone'],
            'ProvinceId' => $AddressList['provinceId'],
            'ProvinceDes' => $province['ProvinceDes'],
            'CityId' => $AddressList['cityId'],
            'CityDes' => $city['CityDes'],
            'CountId' => $AddressList['countId'],
            'CountDes' => $count['AreaDes'],
            'Address' => $AddressList['address'],
            'IsDefault' => $AddressList['isDefault'],
            'Time' => date('Y-m-d H:i:s'),
        ];
        $res=Db::table('kzdd_address')->insert($data);
        if($res==1){
            $result=[
                'code' => 200,
                'data' => 'success'
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }else{
            $result=[
                'code' => 400,
                'data' => 'err'
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

        
    }
    public function setDefault()
    {   
        if(!isset($_GET['UserId'])||!isset($_GET['Id'])){
            $result=[
                'code' => 400,
                'msg' => 'err',
                'data'=>'缺少参数'
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $UserId=$_GET["UserId"];
        $Id=$_GET["Id"];
        // 先将默认的地址改为非默认地址
        Db::table('kzdd_address')->where('UserId',$UserId)->where('IsDefault',1)->update(['IsDefault'=>0]);
        $data=Db::table('kzdd_address')->where('UserId',$UserId)->where('Id',$Id)->update(['IsDefault'=>1]);
        if($data){
            $result=[
                'code' => 200,
                'msg' => 'success',
                'data'=>'修改成功'
            ];
            
        }else{
            $result=[
                'code' => 400,
                'msg' => 'err',
                'data'=>'修改失败'
            ];
        }
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }
    public function upload()
    {
        $fileContent=isset($_POST['fileContent'])?$_POST['fileContent']:'';
        return $fileContent;
    }
}
=======
<?php
namespace app\index\controller;
header('Access-Control-Allow-Origin:*');
header("Content-Type: text/html; charset=utf8");
use think\Db;
use think\Session;

class Index
{   public function index()
    {   
        echo date('Y-m-d H:i:s');
    }   
    public function login()
    {   
        $username=isset($_GET['username']) ? $_GET['username'] : 'admin';
        $password=isset($_GET['password']) ? $_GET['password'] : '123456';
        $db=new Db;
        // dump($db);
        $data=Db::table('user')->where('username',$username)->where('password',$password)->find();
        $char = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string='';
        for($i = count($char); $i > 0; $i--) {
            $string .= $char[mt_rand(0, strlen($char) - 1)];
        }
        

        if($data){
            
            $data['token']=$string;
            $result=[
                'code' => 200,
                'msg'  => 'success',
                'data' => $data
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    public function getBanner(){
        $con=mysqli_connect('localhost:3306','root','root');
        if(!$con){
            echo '连接数据不成功';
        }

        
        mysqli_query($con,'set names utf8');             //写库
        mysqli_query($con,"set character set 'utf8'");  //读库


        mysqli_select_db($con,'kzdd');
        $selectStr='select * from user';
        // $selectStr='INSERT INTO test (name , id) VALUES ("哈哈哈","送达方式")';
        $res=mysqli_query($con,$selectStr);
        if(!$res){
            echo '无法查询数据';
        }
        $result=[];
        while($row=mysqli_fetch_array($res,MYSQL_ASSOC)){
            // var_dump($row);
            Array_push($result,$row);
        }
        echo json_encode($result,JSON_UNESCAPED_UNICODE);  
        //用PHP的json_encode来处理中文的时候, 中文都会被编码, 变成不可读的, 类似”\u***”的格式，如果想汉字不进行转码,
            // JSON_UNESCAPED_UNICODE, 故名思议, 就是说, Json不要编码Unicode.
    }
    public function productList(){
        $con=mysqli_connect('localhost','root','root');
        $showArr=[];
        if(!$con){
            echo '连接数据不成功';
            $showArr['code']=400;
            $showArr['count']=0;
            $showArr['msg']='查询失败';
        }

        
        mysqli_query($con,'set names utf8');             //写库
        mysqli_query($con,"set character set 'utf8'");  //读库


        mysqli_select_db($con,'kzdd');
        $nowPage=isset($_GET['nowPage'])&&$_GET['nowPage']>1 ? $_GET['nowPage'] : 1;
        $pageSize=isset($_GET['pageSize'])&&$_GET['pageSize']>1 ? $_GET['pageSize'] : 2;
        $selectStr='select * from products limit '.($nowPage-1)*$pageSize.','.$pageSize.'';

        $res=mysqli_query($con,$selectStr);
        $result=[];
        while($row=mysqli_fetch_array($res,MYSQL_ASSOC)){
            Array_push($result,$row);
        }
        
        
        $showArr['code']=200;
        $showArr['count']=count($result);
        $showArr['data']=$result;
        echo json_encode($showArr,JSON_UNESCAPED_UNICODE);
          
        //用PHP的json_encode来处理中文的时候, 中文都会被编码, 变成不可读的, 类似”\u***”的格式，如果想汉字不进行转码,
            //JSON_UNESCAPED_UNICODE, 故名思议, 就是说, Json不要编码Unicode.
    }
    public function yreight(){
        if(isset($_GET['type'])){
            $result=[];
            $result['code']=200;
            $result['msg']='success';
            switch ($_GET['type']){
                case 1:
                $result['data']=10;
                break;
                case 2:
                $result['data']=15;
                break;
                default:
                $result['data']=10;
                break;
            }
        }else{
            $result=array(
                'code' => 400,
                'msg'  => 'error'
            );
        } 
        
        echo json_encode($result);
    }
    public function creatOrder(){
        $con=mysqli_connect('localhost','root','root');
        $showArr=[];
        if(!$con){
            echo '连接数据不成功';
            $showArr['code']=400;
            $showArr['count']=0;
            $showArr['msg']='查询失败';
        }

        
        mysqli_query($con,'set names utf8');             //写库
        mysqli_query($con,"set character set 'utf8'");  //读库


        mysqli_select_db($con,'kzdd');
        if(!isset($_GET['UserId'])||!isset($_GET['Freight'])||!isset($_GET['DeliveryType'])) {
            $result=array(
                'code' => 400,
                'msg'  => 'error',
                'data' => '缺少参数'
            );
            return json_encode($result);
        }
        $Id=date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $OrderDate=date('Y-m-d H:i:s');
        $OrderState=1;
        $UserId=$_GET['UserId'];
        $Freight=$_GET['Freight'];
        $DeliveryType=$_GET['DeliveryType'];
        $sqlstr='insert into kzdd_orders (Id, OrderDate,OrderState,UserId,Freight,DeliveryType) values ("'.$Id.'","'.$OrderDate.'","'.$OrderState.'","'.$UserId.'","'.$Freight.'","'.$DeliveryType.'")';
        // $sqlstr='insert into kzdd_orders (Id, OrderDate,OrderState,UserId,Freight,DeliveryType) values ("123","ss","1","1","34","1")';
        $res=mysqli_query($con,$sqlstr); 
        if($res){
            $result=array(
                'code' => 200,
                'msg'  => 'success',
                'data' => '订单号：'.$Id
            );
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }else{
            $result=array(
                'code' => 400,
                'msg'  => 'error',
                'data' => '查询出错'
            );
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    public function address()
    {
        if(!isset($_GET['UserId'])){
            $result=array(
                'code'    => 400,
                'message' => 'error',
                'data'    => '缺少参数'
            );
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $UserId=$_GET['UserId'];
        $data=Db::table('kzdd_address')->where('UserId',$UserId)->select();
        $result=array(
            'code'    => 200,
            'message' => 'success',
            'data'    => $data
        );
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }
    public function addAddress()
    {
        // if(!isset($_GET['UserId'])||!isset($_GET['AddressList'])){
        //     $result=[
        //         'code' => 400,
        //         'data' => 'err',
        //         'msg'  => '缺少参数'
        //     ];
        //     return json_encode($result,JSON_UNESCAPED_UNICODE);
        // }
        $UserId=1;  //$_GET['UserId']
        //$AddressList=$_GET['AddressList'];  //$_GET['AddressList']
        $AddressList='{"receiver":"爱上房顶上","receiverPhone":"13125454121","provinceId":"110000","cityId":"110100","countId":"110101","address":"舒服撒","isDefault":1}';
        $AddressList=json_decode($AddressList,true);
        if($AddressList['isDefault']==1){
            $data=Db::table("kzdd_address")->where('UserId',$UserId)->where('IsDefault',1)->update(['IsDefault'=>0]);
            dump($data);
        }
        $data=[
            'UserId' => $UserId,
            'Receiver' => $AddressList['receiver'],
            'ReceiverPhone' => $AddressList['receiverPhone'],
            'ProvinceId' => $AddressList['provinceId'],
            'CityId' => $AddressList['cityId'],
            'CountId' => $AddressList['countId'],
            'Address' => $AddressList['address'],
            'IsDefault' => $AddressList['isDefault'],
        ];
        dump($data);
        $res=Db::table('kzdd_address')->insert($data);
        if($res==1){
            $result=[
                'code' => 200,
                'data' => 'success'
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }else{
            $result=[
                'code' => 400,
                'data' => 'err'
            ];
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }
    public function upload()
    {
        $photoSrc=$_POST['photoSrc'];
    }
}
>>>>>>> 0d66a1571bb6cb44412c3ee3370a556928034068
