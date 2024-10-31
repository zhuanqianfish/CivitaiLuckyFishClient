<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Max-Age: 3600");
// 设置 HTTP 头信息，指定 Content-Type 为 application/json
header('Content-Type: application/json');

require_once('filehelper.php');

function request($url, $data, $cookie,$isPost=true, $needPorxy=false){
    // 初始化cURL会话
    $ch = curl_init();
    // 设置cURL选项
    //// 允许cURL函数执行时使用代理
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if($needPorxy || true){
        // //设置代理类型
        //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        // //设置SOCKS5代理服务器地址和端口
        //curl_setopt($ch, CURLOPT_PROXY, "socks5://127.0.0.1:10808");
        // curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXY, "http://127.0.0.1:10809");
    }
   // curl_setopt($ch, CURLOPT_CAINFO, __DIR__."/ssl/cacert.pem");
   // 或者跳过证书验证（不推荐在生产环境中使用）
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
   
    // die;
    if($isPost){
        $postData = json_encode($data);
        // var_dump( $postData);
        curl_setopt($ch ,CURLOPT_POST,true); // 发起POST请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // POST数据
    }else{
        // 将关联数组转换为查询字符串
        $queryString = http_build_query($data);
        // 组合完整的 URL
        $fullUrl = $url . (strpos($url, '?') === false ? '?' : '&') . $queryString;
        $url =  $fullUrl ;
    }
    curl_setopt($ch, CURLOPT_URL, $url); // 目标URL
  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回响应而不是输出
    // curl_setopt($ch, CURLOPT_HEADER, true); // 需要头部信息
    
    // 设置cookie
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    
    // 执行cURL会话
    $response = curl_exec($ch);
    
    // 检查是否有错误发生
    if(curl_errno($ch)){
        echo json_encode(['code'=>555, 'msg'=>'cURL error: ' . curl_error($ch)]); 
    }
    // 关闭cURL会话
    curl_close($ch);
    // 输出响应内容
    echo $response;
}
$json = file_get_contents('php://input');
$recvData = json_decode($json, true);

$url = $recvData['url'];
$data = $recvData['data'];
$cookieName = $recvData['cookieName'] ?? 0;
$cookie =  getCookieByName($cookieName);
// var_dump($cookie); echo "\r\n";
// var_dump($url); echo "\r\n";
// var_dump($data); echo "\r\n";

$debug = false;
if(!$cookie || $debug){
    echo json_encode(['code'=>554, 'msg'=>'cookkie missing! check cookieName','cookie:'=>$cookie ]); 
    die;
}

$isPost = $recvData['method'] == 'POST' ? true : false; 
request($url, $data, $cookie, $isPost);




////////////////////////////////////////////////
function getCookieByName($cookieName){
    try {
        return $filesContent = FileHelper::readFile('data/cookie/'.$cookieName);
    } catch (Exception $e) {
        echo 'Error: ',  $e->getMessage(), "\n";
        exit;
    }    
}


function getFileContent(){
    $filePath = $recvData['filepath'];
    try {
        return $filesContent = FileHelper::readFile('data/'.$filePath);
    } catch (Exception $e) {
        echo 'Error: ',  $e->getMessage(), "\n";
        exit;
    }   

}

?>