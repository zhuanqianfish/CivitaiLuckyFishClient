//var ws;
var ws = new WebSocket("ws://127.0.0.1:8484"); // 替换为你的WebSocket服务器地址
function test_msg(params){
    alert('test_msg()~!');
    console.log("收到了参数", params);
}
function connect() {
    var messages = document.getElementById('messages');
    messages.innerHTML += '<div>尝试连接...</div>';
    var ws_serverAddress = document.getElementById('websocketServer').value;
    ws = new WebSocket(ws_serverAddress); // 替换为你的WebSocket服务器地址
    console.log(111111, ws);
}

ws.onopen = function() {
    messages.innerHTML += '<div>连接成功</div>';
    document.getElementById('messageInput').disabled = false;
    document.querySelector('button:nth-child(1)').disabled = true;
    document.querySelector('button:nth-child(3)').disabled = false;
};

ws.onmessage = function(event) {
    messages.innerHTML += '<div>收到消息: ' + event.data + '</div>';
    try { 
        let tempStr = event.data.replace('\n','\\n');
        var receiveData = JSON.parse(tempStr) ;
        if(__parameStr){
            __parameStr = undefined;
        }
        
        var __parameStr = "var tempParam = " + JSON.stringify( receiveData.data) +";";
        var execStr = receiveData.action +"(tempParam)";
        console.log(receiveData.action, receiveData.data, execStr);

        eval(__parameStr)  //声明变量
        eval(execStr)  //执行函数
    } catch (error) {
        console.log('收到了其他消息',error)
    }
};

ws.onclose = function() {
    messages.innerHTML += '<div>连接断开</div>';
    document.getElementById('messageInput').disabled = true;
    document.querySelector('button:nth-child(1)').disabled = false;
    document.querySelector('button:nth-child(3)').disabled = true;
};

ws.onerror = function(error) {
    messages.innerHTML += '<div>错误: ' + error.message + '</div>';
};


function disconnect() {
    if (ws) {
        ws.close();
    }
    var messages = document.getElementById('messages');
    messages.innerHTML += '<div>已断开连接</div>';
}

function sendMessage() {
    var messageInput = document.getElementById('messageInput');
    console.log('发送', messageInput.value )
    if (ws.readyState === WebSocket.OPEN) {
        ws.send(messageInput.value);
        var messages = document.getElementById('messages');
        messages.innerHTML += '<div>发送: ' + messageInput.value + '</div>';
        messageInput.value = '';
    } else {
        alert('连接未开启或已关闭');
    }
}

function getCookie(){

}