<?php
namespace app\index\controller;
header('Access-Control-Allow-Origin:*');
class Index
{
    public function index()
    {
        $username=$_GET['username'];
        $password=$_GET['password'];
        $sqlcon = mysqli_connect('localhost','root','root');
        if(!$sqlcon){
            echo '数据库连接不成功';
        }
        mysqli_query($sqlcon,'set names utf8');
        mysqli_select_db($sqlcon, 'kzdd' );
        $sql='select * from user';
        $res=mysqli_query($sqlcon,$sql);
        if(!$res){
            echo '无法读取数据：'.mysqli_error($sqlcon);
        }
        while($row = mysqli_fetch_array($res, MYSQL_ASSOC)){
            // echo json_encode($row);
            if($username==$row['username']&&$password==$row['password']){
                $result=['result' => 'success'];
                echo json_encode($result);
                return;
            }else{
                $result=['result' => 'fail'];
                echo json_encode($result);
                return;
            }
        }
        mysqli_close($sqlcon);
    }
}
