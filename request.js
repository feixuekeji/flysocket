// function ws() {
//
//     this.ws = new WebSocket("ws://localhost:8282");
//     this.ws.onopen = function () {
//         // Web Socket 已连接上，使用 send() 方法发送数据
//         //ws.send("发送数据");
//
//
//     };
//
//         this.messageList = [];
//         this.send = function(data, id, callback) {
//
//             this.messageList['aa'] = callback;
//             this.ws.send(data);
//     };
//
//     this.ws.onmessage = function (e) {
//         //处理各种推送消息
//         //执行回调
//         this.messageList['aa'](e);
//     };
//
// }
//
// var ws = new ws();

var websock = null
var globalCallback = null

// 初始化weosocket
function initWebSocket () {
    // ws地址 -->这里是你的请求路径
    var ws= 'ws://localhost:8282';
    websock = new WebSocket(ws)
    websock.onmessage = function (e) {
        websocketonmessage(e)
    }
    websock.onclose = function (e) {
        websocketclose(e)
    }
    websock.onopen = function () {
        websocketOpen()
    }

    // 连接发生错误的回调方法
    websock.onerror = function () {
        console.log('WebSocket连接发生错误')
    }
}

// 实际调用的方法
function sendSock (agentData, callback) {
    globalCallback = callback
    if (websock.readyState === websock.OPEN) {
        // 若是ws开启状态
        websocketsend(agentData)
    } else if (websock.readyState === websock.CONNECTING) {
        // 若是 正在开启状态，则等待1s后重新调用
        setTimeout(function () {
            sendSock(agentData, callback)
        }, 1000)
    } else {
        // 若未开启 ，则等待1s后重新调用
        setTimeout(function () {
            sendSock(agentData, callback)
        }, 1000)
    }
}

// 数据接收
function websocketonmessage (e) {
    globalCallback(JSON.parse(e.data))
}

// 数据发送
function websocketsend (agentData) {
    websock.send(JSON.stringify(agentData))
}

// 关闭
function websocketclose (e) {
    console.log('connection closed (' + e.code + ')')
}

// 创建 websocket 连接
function websocketOpen (e) {
    console.log('连接成功')
}

initWebSocket()
