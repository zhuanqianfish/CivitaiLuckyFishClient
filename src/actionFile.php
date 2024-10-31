<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");
// 设置 HTTP 头信息，指定 Content-Type 为 application/json
header('Content-Type: application/json');

require_once('filehelper.php');


$json = file_get_contents('php://input');
$recvData = json_decode($json, true);
$action = $recvData['action'] ?? null;


if($action == "getFileContent" || !$action ){
    getFileContent();
}else{
    $action();
}



//读取用户信息的内容
function getFileContent(){
    try {
        $json = file_get_contents('php://input');
        $recvData = json_decode($json, true);
        $filePath = $recvData['filepath'] ?? null;
        // $filesContent = FileHelper::readFile('data/'.$filePath);
        // $filesContent = include_once('data/'.$filePath);
        $filesContent = include_once('data/auto/userinfo.php');
        if($filePath != "auto/userinfo.php"){
            $filesContent = FileHelper::readFile('data/'.$filePath);
            $filesContent = json_decode($filesContent, true); 
        }
      
       // var_dump( $filesContent);
        echo  json_encode(['code'=>1, 'data'=>$filesContent]); 
    } catch (Exception $e) {
        echo 'Error: ',  $e->getMessage(), "\n";
        exit;
    }  
}
 


//获取某个文件夹下的文件列表
function getFileList(){
    try {
        $json = file_get_contents('php://input');
        $recvData = json_decode($json, true);
        $filePath = $recvData['filepath'] ?? null;
        $fileList = FileHelper::listFiles('data/'.$filePath);
        echo json_encode(['code'=>1 , 'data'=>$fileList]); 
    } catch (Exception $e) {
        echo   json_encode(['code'=>0 , 'msg'=>'Error: '.$e->getMessage(). "\n"]); 
        exit;
    }  
}

//保存文件
function saveFile(){
    try {
        $json = file_get_contents('php://input');
        $recvData = json_decode($json, true);
        $filePath = $recvData['filepath'] ?? null;
        $filesContent = $recvData['content'];
        // FileHelper::writeFile( 'data/'.$filePath , json_encode( $filesContent ,null,2));
        $tempJsonData = json_decode( $filesContent, true);
        $tempJsonDataStr = json_encode( $tempJsonData ,null,2);
        file_put_contents('data/'.$filePath, $filesContent);
        echo json_encode(['code'=>1 ]); 
    } catch (Exception $e) {
        echo   json_encode(['code'=>0 , 'msg'=>'Error: '.$e->getMessage(). "\n"]); 
        exit;
    }  
}


//读取单个json文件
function readOneFile(){
    try {
        $json = file_get_contents('php://input');
        $recvData = json_decode($json, true);
        $filePath = $recvData['filepath'] ?? null;
        $filesContent = FileHelper::readFile('./data/'.$filePath);
        $filesData = json_decode($filesContent, true); 
        echo  json_encode(['code'=>1, 'data'=>$filesData]); 
    } catch (Exception $e) {
        echo   json_encode(['code'=>0 , 'msg'=>'Error: '.$e->getMessage(). "\n"]); 
        exit;
    }  
}



//读取单个json文件 - 为一键点赞用
function readOneFile2(){
    try {
        $json = file_get_contents('php://input');
        $recvData = json_decode($json, true);
        $filePath = $recvData['filepath'] ?? null;
        $i = $recvData['i'] ?? null;
        $i2 = $recvData['i2'] ?? null;
        $filesContent = FileHelper::readFile('./data/'.$filePath);
        $filesData = json_decode($filesContent, true); 
        echo  json_encode(['code'=>1, 'data'=>$filesData ,'i'=>$i , 'i2'=>$i2]); 
    } catch (Exception $e) {
        echo   json_encode(['code'=>0 , 'msg'=>'Error: '.$e->getMessage(). "\n"]); 
        exit;
    }  
}





?>