//websocket地址
let host = "ws://localhost:8282";
let ws = null;
let socketOpen = false;
let callbackArr = [];
let msgArr = [];
//心跳时间
let heartCheckTime = 45000;
//重连尝试次数
let reconnectTime = 0;

function wsConnect() {


    ws = new WebSocket(host);

    // 打开socket连接
    ws.onopen = e => {
        socketOpen = true;
        console.log('连接成功');

        //待发送消息队列
        if (msgArr.length) {
            for (let i in msgArr) {
                sendMsg(msgArr[i].data, msgArr[i].callback)
            }
            msgArr = []
        }
        // 心跳
        heartCheck()
    };


    // socketClose
    ws.onclose = e => {
        console.log('连接已断开', e);
        socketOpen = false;
        reconnect();
    };


    //连接发生错误
    ws.onerror = e => {
        console.log('连接发生错误', e);
        socketOpen = true;
        reconnect();
    };


    //接收消息
    ws.onmessage = e => {
        // 按api保存回调
        let callBackData = JSON.parse(e.data);
        let key = callBackData.key;
        if (!key) return;
        callbackArr[key](callBackData)

    }
}


// 发送消息
function send(data, callback) {
    callbackArr[data.key] = callback
    // console.log('发送消息',data)
    if (socketOpen) {
        ws.send(JSON.stringify(data))
    } else {
        // 未开启，加入队列
        msgArr.push({data, callback});
        wsConnect();
    }
}


// 心跳检测
function heartCheck() {
    if (socketOpen) {
        setInterval(() => {
            ws.send('ping')
            console.log("发送ping", heartCheckTime)
        }, heartCheckTime);
    }
}


// 重连
function reconnect() {
    if (!socketOpen && reconnectTime <= 10) {
        setTimeout(() => {
            wsConnect()
            reconnectTime++
            console.log('重新连接', reconnectTime)
        }, 2000)
    }
}

module.exports.send = send