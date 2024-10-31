<?php
require_once('filehelper.php');
try {
    $config = include_once('../config.php');
} catch (Exception $e) {
    echo 'Error: ',  $e->getMessage(), "\n";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>摸鱼仙人的C站客户端</title>
    <style>
        #messages {
            width: 300px;
            height: 300px;
            border: 1px solid #ccc;
            padding: 5px;
            overflow-y: scroll;
            margin-bottom: 10px;
        }
        input, button {
            margin-bottom: 10px;
        }
        .content{display:flex;}
    </style>
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/ace.min.css">
</head>
<body>
<div class="content">
<div id="leftPanel" style="width:50%">
    <div class="button-group">
        <!-- <button  type="button" onclick="sendMessage()">发送消息</button> -->
        <!-- <button  type="button" onclick="createImg()">生图测试！</button> -->
        <button  type="button" onclick="getUserId()">获取当前用户ID</button>
        <button  type="button" onclick="checkBuzz()">获取用户剩余Buzz</button>
    </div>
    
    <div class="button-group">
        <button  type="button" class="success" onclick="claimDailyBoostReward()">收取生图器每日Buzz</button>
        <button  type="button" class="success" onclick="getImageList()">给全站最新图片点赞</button>
        <button  type="button" class="success" onclick="queryGeneratedImagesAndLiked()">检查生图队列并点赞</button>
        <!-- <button  type="button" class="success" onclick="_likedOneGenImg()">【测试】给一条生图记录点赞</button> -->
    </div>
    <div class="button-group">
        ID：<span id="nowUserId">-</span>
        Buzz: <span id="buzzLeft">-</span> （黄：<span id="buzzLeft1">-</span> | 蓝：<span id="buzzLeft2">-</span>  ）
    </div>


    <div class="submenu"  id="creater">
        <h2 style="text-align:center;">在线生图</h2>
       
        <div class="button-group">
            <button type="button"  class="info" onclick="test2()">按钮2</button>
            <button type="button"  class="info" onclick="test3()">按钮3</button>
        </div>
        <div class="button-group">
            <button type="button" class="danger" onclick="getImageWhatIf()">预估Buzz消耗</button>
            <button type="button" class="success" style="background:#" onclick="createImg()">生成图片</button>
            <button type="button" class="warning" onclick="queryGeneratedImages()">查询生图结果</button>
        </div>
        <!--ACE 代码编辑器-->
        <div class="button-group">
            <div id="aceEditor" style="width:440px; height:180px;">Loading...</div>
            <button type="button" id="aceEditorToggleButton" onclick="toggleEditor()" >打开编辑器</button>
        </div>
        <div class="form-container" style="display:none;">
            <label for="name">当前任务ID:</label>
            <input type="text" id="nowJobId" name="nowJobId" onchange="setNowJobId" />
        </div>
    </div>
       
    <div class="button-group">
        <button id="btnShowCreater" type="button" class="info" onclick="showCreater()">展开表单生图器</button>
    </div>
    <!-- 表单生图器 -->
    <div id="formCreater" class="form-container" style="display:none;">
        <h2 style="text-align:center;">表单生图器</h2>
        <form >
            <div class="button-group">
                <button type="button" class="info" onclick="test1()">选择主模型??</button>
                <button type="button" class="info" onclick="test2()">添加资源??</button>
                <button type="button" class="add-btn info" onclick="addComponent()">Add Component??</button>
            </div>
            <div id="components-wrapper">
            <!-- 动态生成的组件将会添加到这里 -->
            </div>

            <label for="name">prompt:</label>
            <textarea rows="8" id="fish_prompt" name="fish_prompt"></textarea>

            <label for="name">negativePrompt:</label>
            <textarea rows="8" id="fish_negativePrompt" name="fish_negativePrompt"></textarea>

            <label for="name">cfgScale:</label>
            <input type="text" id="fish_cfgScale" name="fish_cfgScale" />

            <label for="name">sampler:</label>
            <select id="fish_sampler" name="fish_sampler">
                <option value="Euler a">Euler a</option>
                <option value="ddim">DDIM</option>
            </select>

            <label for="name">seed:</label>
            <input type="text" id="fish_seed" name="fish_seed" value="-1"/>

            <label for="name">clipSkip:</label>
            <input type="text" id="fish_clipSkip" name="fish_clipSkip" value="2" />

            <label for="name">steps:</label>
            <input type="text" id="fish_steps" name="fish_steps" value="20" />

            <label for="name">quantity:</label>
            <input type="text" id="fish_quantity" name="fish_quantity" value="2" />

            <label for="name">nsfw:</label>
            <input type="text" id="fish_nsfw" name="fish_nsfw" value="" />

            <label for="name">draft mod:</label>
            <input type="text" id="fish_draft" name="fish_draft" value="false" />

            <label for="name">baseModel:</label>
            <input type="text" id="fish_baseModel" name="fish_baseModel" />

            <label for="name">workflow:</label>
            <input type="text" id="fish_workflow" name="fish_workflow" />

            <label for="name">fluxMode:</label>
            <input type="text" id="fish_fluxMode" name="fish_fluxMode" falue="" />
            <label for="name">width:</label>
            <input type="text" id="fish_width" name="fish_width" value="832" />
            <label for="name">height:</label>
            <input type="text" id="fish_height" name="fish_height" value="1216" />
        </form>
    </div>
</div>


<div id="rightPanel"  style="width:50%">
     <!--生图结果-->
     <div class="form-container">
        <div id="luckfish_imageresult"></div>
    </div>
</div>
<script src="static/js/jquery.min.js"></script>
<script src="static/js/layer.min.js"></script>
<script src="static/js/ace.min.js"></script>

<script>
    var now_cookie = null;
    var ace_editor = null;
    $(function(){
        //Loading...
        console.log("init...")
        ace_editor = ace.edit("aceEditor");
        ace_editor.setTheme("ace/theme/monokai");
        ace_editor.session.setMode("ace/mode/javascript");
        // use setOptions method to set several options at once
        ace_editor.setOptions({
            autoScrollEditorIntoView: true,
            copyWithEmptySelection: true,
        });
       // ace_editor.setValue(dataStr);
    })

    function createImg(){
        var url = "https://civitai.com/api/trpc/orchestrator.generateImage";
        var tempStr =  ace_editor.getValue();
        var dataStr = JSON.parse(tempStr);
        var cookieName = document.getElementById('cookieSelector').value
        var postdata =   JSON.stringify({"data":dataStr, "url":url,"cookieName": cookieName, "method":"POST"});
        $.post("action.php", postdata, function(res){
            console.log(res)
        })
    }


    //保存文件
    function _saveFile(filePath , content){
        var strContent = JSON.stringify(content);
        // console.log(strContent);
        $.post("actionFile.php", JSON.stringify({"action":"saveFile", "filepath":filePath, "content":  strContent}),function(res){
            console.log(filePath,strContent);
        })
    }


    //一张图片点赞
    function dianzan1(entityId, i, cookieName=null){
        var url = "https://civitai.com/api/trpc/reaction.toggle";
        if(!cookieName){
            cookieName = document.getElementById('cookieSelector').value
        }
        var data = {
                "json": {
                    "entityId": entityId,
                    "entityType": "image",
                    "reaction": "Like",
                    "authed": true
                }
        };
        var postdata =   JSON.stringify({"data":data, "url":url,"cookieName": cookieName,"method":"POST"});
        var requestData = {
            method: "POST",
            url: "action.php",
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata,
            success: function(res) {
                //console.log('dianzan', entityId);
            }
        };
        const minDelay = i; // 最小延迟时间（毫秒）
        const maxDelay = i + 3; // 最大延迟时间（毫秒）
        const randomDelayTime = Math.random() * (maxDelay - minDelay) + minDelay; // 生成随机延迟时间

        setTimeout(() => {
           // console.log('延迟执行');
            $.ajax(requestData);
        }, Math.floor(randomDelayTime * 100)); // 将毫秒转换为微秒
    }; 

    //获取每日生图器Buzz
    function claimDailyBoostReward(cookieName=null){
        var url = "https://civitai.com/api/trpc/buzz.claimDailyBoostReward";
        if(!cookieName){
            cookieName = document.getElementById('cookieSelector').value
        }
        var data =  {
                "json": null,
                "meta": {
                    "values": [
                        "undefined"
                    ]
                }
            };
        var postdata =   JSON.stringify({"data":data, "url":url,"cookieName": cookieName,"method":"POST"});
        var requestData = {
            method: "POST",
            url:"action.php",
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata,
            success: function(res) {
                console.log('claimDailyBoostReward', res);
            }
        };
        $.ajax(requestData);
    }
 

    var userId = null;  //当前用户的ID
    //查询用户id
    function getUserId(callback){
        var url = 'https://civitai.com/api/trpc/buzz.getUserMultipliers?input={"json":null,"meta":{"values":["undefined"]}}';
        var cookieName = document.getElementById('cookieSelector').value
        var data = [];
        var postdata =   JSON.stringify({"data":data, "url":url,"cookieName": cookieName,"method":"GET"});
        var requestData = {
            method: "POST",
            url:"action.php" ,
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata,
            success: function(res) {
                console.log('getUserId', res);
                if(callback){
                    callback(res.result.data.json.userId);
                }
                userId = res.result.data.json.userId;
                $("#nowUserId").html(res.result.data.json.userId);

            }
        };
        $.ajax(requestData);
    }

     //检查剩余 总buzz
     function checkBuzz(){
        var url = 'https://civitai.com/api/trpc/buzz.getUserMultipliers?input={"json":null,"meta":{"values":["undefined"]}}';
        var cookieName = document.getElementById('cookieSelector').value
        var tempData = {"json":null,"meta":{"values":["undefined"]}};
        var postdata =  JSON.stringify({"data":tempData, "url":url,"cookieName": cookieName,"method":"GET"});
        var requestData = {
            method: "POST",
            url:"action.php" ,
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata,
            success: function(res) {
                console.log('getUserId', res);
                userId = res.result.data.json.userId
                var url2 = "https://civitai.com/api/trpc/buzz.getBuzzAccount";
                var tempData2 = {"input":JSON.stringify({"json":{"accountId":userId,"accountType":null,"authed":true},"meta":{"values":{"accountType":["undefined"]}} }) };
                var postdata2 =   JSON.stringify({"data":tempData2, "url":url2,"cookieName": cookieName,"method":"GET"});

                var requestData2 = {
                    method: "POST",
                    url:"action.php",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    data:postdata2,
                    success: function(res2) {
                        console.log('checkBuzz', res2);
                        $("#buzzLeft").html(res2.result.data.json.balance);
                        checkBuzz1()
                        checkBuzz2()
                    }
                };
                $.ajax(requestData2);

            }
        };
        $.ajax(requestData);
    }

    //检查剩余 黄buzz
    function checkBuzz1(){
        var url2 = "https://civitai.com/api/trpc/buzz.getBuzzAccount";
        var cookieName = document.getElementById('cookieSelector').value
        var tempData2 = {"input":JSON.stringify({"json":{"accountId":userId,"accountType":"user","authed":true}}) };
        var postdata2 =   JSON.stringify({"data":tempData2, "url":url2,"cookieName": cookieName,"method":"GET"});

        var requestData2 = {
            method: "POST",
            url:"action.php",
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata2,
            success: function(res2) {
                console.log('checkBuzz', res2);
                $("#buzzLeft1").html(res2.result.data.json.balance);
            }
        };
        $.ajax(requestData2);
    }
    //检查剩余 蓝buzz
    function checkBuzz2(){
        var url2 = "https://civitai.com/api/trpc/buzz.getBuzzAccount";
        var cookieName = document.getElementById('cookieSelector').value
        var tempData2 = {"input":JSON.stringify({"json":{"accountId":userId,"accountType":"generation","authed":true}}) };
        var postdata2 =   JSON.stringify({"data":tempData2, "url":url2,"cookieName": cookieName,"method":"GET"});

        var requestData2 = {
            method: "POST",
            url:"action.php",
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata2,
            success: function(res2) {
                console.log('checkBuzz', res2);
                $("#buzzLeft2").html(res2.result.data.json.balance);
            }
        };
        $.ajax(requestData2);
    }

    var nowJobId = null;
    //检查生图队列
    function queryGeneratedImages(cursor=null){
        if(nowJobId){
            cursor = nowJobId;
        }
        if(cursor){
            tempData = {"json":{"cursor":null,"authed":true},"meta":{"values":{"tags":["undefined"]}}};
        }
        var url = "https://civitai.com/api/trpc/orchestrator.queryGeneratedImages";
        var tempData = 
            {"input":
                JSON.stringify(
                    {"json":{"cursor":cursor,"authed":true},"meta":{"values":{"cursor":["undefined"]}}}
                )
            };
        var cookieName = document.getElementById('cookieSelector').value
        var postdata =  JSON.stringify({"data":tempData, "url":url,"cookieName": cookieName,"method":"GET"});

        var requestData = {
            method: "POST",
            url:"action.php" ,
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata,
            success: function(res) {
                console.log('queryGeneratedImages', res);
                var tempstatus = res.result.data.json.items[0].status;
                if(tempstatus == "succeeded"){
                    var imgRes = res.result.data.json.items[0].steps[0].images ?? null;
                // displayImages(imgRes);
                    displayImagesMore(res.result.data.json.items);
                }
            }
        };
        $.ajax(requestData);
    }


    //设置当前任务id
    function setNowJobId(jobId){
        if(!jobId){
            nowJobId = $("#nowJobId").val();
        }else{
            nowJobId = jobId;
            $("#nowJobId").val(jobId);
        }
    }

    //显示图片结果(多个任务)
    function displayImagesMore(items) {
        const gallery = document.getElementById('luckfish_imageresult');
        items.forEach((item) => {
            const panel = document.createElement('div');
            panel.className = 'panel';

            const header = document.createElement('div');
            header.className = 'panel-header';
            header.textContent = "id:" +  item.id + "        " +  item.status;
            const arrow = document.createElement('div');
            arrow.className = 'arrow';
            header.appendChild(arrow);
            header.onclick = function() {
                panel.classList.toggle('active');
                const content = panel.querySelector('.panel-content');
                content.style.display = content.style.display === 'none' ? 'flex' : 'none';
            };

            const content = document.createElement('div');
            content.className = 'panel-content';

            item.steps[0].images.forEach((image) => {
                if (image.status === 'succeeded') {
                    const imgElement = document.createElement('img');
                    imgElement.src = image.url;
                    imgElement.alt = `Image from ${item.id}`;
                    imgElement.className = 'image-item';
                    imgElement.onclick = function() {
                       layer.open({
                           type: 2,
                           title: false,
                           closeBtn: 1,
                           area: ['90%', '90%'], // 全屏显示
                           shade: [0.3, '#000'], // 背景透明
                           shadeClose: true,
                           content: `<img src="${image.url}" alt="Full size image" style="max-width: 100%; max-height: 100%; height: auto; width: auto; object-fit: contain; display: block; margin: 0 auto;">`
                       });
                    };
                    content.appendChild(imgElement);
                } else {
                    const statusDiv = document.createElement('div');
                    statusDiv.className = 'status-div';
                    statusDiv.textContent = `Status: ${image.status}`;
                    content.appendChild(statusDiv);
                }
            });

            panel.appendChild(header);
            panel.appendChild(content);
            gallery.appendChild(panel);
        });
    }

    //检查生图队列并点赞 
    function queryGeneratedImagesAndLiked(pubcount=3){
        var tempData = {"json":{"cursor":null,"authed":true},"meta":{"values":{"tags":["undefined"]}}};
        var url = "https://civitai.com/api/trpc/orchestrator.queryGeneratedImages";
        var tempData = 
            {"input":
                JSON.stringify(
                    {"json":{"cursor":null,"authed":true},"meta":{"values":{"cursor":["undefined"]}}}
                )
            };
        var cookieName = document.getElementById('cookieSelector').value
        var postdata =  JSON.stringify({"data":tempData, "url":url,"cookieName": cookieName,"method":"GET"});
        var requestData = {
            method: "POST",
            url:"action.php" ,
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata,
            success: function(res) {
                // console.log('queryGeneratedImages', res);
                // console.log(`这里应该是发布最新${pubcount}条`,imgRes);
                for(let i = 0;i < pubcount ;i++){
                    var tempwork = res.result.data.json.items[i];
                    for(let j=0;j < tempwork.steps[0].images.length ;j++){
                        var tempImage = tempwork.steps[0].images[j];
                        _likedOneGenImg(tempImage.workflowId, tempImage.id);    //点赞
                    }
                }
            }
        };
        $.ajax(requestData);
    }

    //给一条生图记录点赞
    function _likedOneGenImg(workflowId, imgId){
        // var workflowId = '5476734-20240920010509702'
        var imgPath = '/images/'+ imgId
        var tempData = {
            "json": {
                "workflows": null,
                "steps": [
                {
                    "workflowId": workflowId,
                    "stepName": "$0",
                    "patches": [
                    {
                        "op": "add",
                        "path": "/images",
                        "from": null,
                        "value": {}
                    },
                    {
                        "op": "add",
                        "path": imgPath,
                        "from": null,
                        "value": {}
                    },
                    {
                        "op": "add",
                        "path": imgPath + "/feedback",
                        "from": null,
                        "value": "liked"
                    }
                    ]
                }
                ],
                "remove": null,
                "tags": [
                {
                    "workflowId": workflowId,
                    "tag": "feedback:liked",
                    "op": "add"
                }
                ],
                "authed": true
            },
            "meta": {
                "values": {
                "workflows": [
                    "undefined"
                ],
                "steps.0.patches.0.from": [
                    "undefined"
                ],
                "steps.0.patches.1.from": [
                    "undefined"
                ],
                "steps.0.patches.2.from": [
                    "undefined"
                ],
                "remove": [
                    "undefined"
                ]
                }
            }
        };

        var url = "https://civitai.com/api/trpc/orchestrator.patch";
        var cookieName = document.getElementById('cookieSelector').value
        var postdata =  JSON.stringify({"data":tempData, "url":url,"cookieName": cookieName,"method":"POST"});
        var requestData = {
            method: "POST",
            url:"action.php" ,
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata,
            success: function(res) {
                console.log('_likedOneGenImg', res);
            }
        };
        $.ajax(requestData);

    }

    //预估生图消耗 - error
    function getImageWhatIf(){
        var url = "https://civitai.com/api/trpc/orchestrator.getImageWhatIf";
        var editor = ace.edit("aceEditor");
        var dataStr = editor.getValue();
        var tempData = {"input": JSON.stringify({"json":JSON.parse(dataStr)}) } ;//;
        var cookieName = document.getElementById('cookieSelector').value
        var postdata =  JSON.stringify({"data":tempData, "url":url,"cookieName": cookieName,"method":"GET"});

        var requestData = {
            method: "POST",
            url:"action.php" ,
            headers: {
                "Content-Type": "application/json"
            },
            data:postdata,
            success: function(res) {
                console.log('getImageWhatIf', res);
            }
        };
        $.ajax(requestData);
    }

    //弹出编辑器
    function toggleEditor(){
        var newEditor = null;
        var layer_ACEeditor =  layer.open({
            type: 1,
            area: ['90%', '90%'],
            anim: -1,
            shadeClose: true,
            content: '<div id="layerAceEditor" style="height: 100%; width: 100%;"></div>', // 新容器
            success: function(layero, index){
                // 在弹层中重新实例化Ace Editor
                newEditor = ace.edit('layerAceEditor');
                newEditor.setTheme("ace/theme/monokai");
                newEditor.getSession().setMode("ace/mode/javascript");
                newEditor.getSession().setValue(ace_editor.getValue());
                // 设置Ace Editor的高度和宽度
                newEditor.resize();
            },
            end: function(){
                // 弹层关闭时
                ace_editor.getSession().setValue(newEditor.getValue());
                //销毁Ace Editor实例
                newEditor.destroy();
            }
        });
    }

    //展开折叠表单生图器
    var isShowCreater = false;
    function showCreater(){
        if(isShowCreater){
            isShowCreater = false
            $("#formCreater").slideUp(500);
        }else{
            isShowCreater = true
            $("#formCreater").slideDown(500);
        }
    }

  
/////////////////////////////////////////////////////////
    //测试
    function test2(){
       layer.msg('一段提示信息', {icon: 5});
    }

    
    //获取随机数
    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
    
</script>

</body>
</html>



