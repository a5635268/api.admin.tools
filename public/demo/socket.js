// 双向
ws = new WebSocket("ws://172.28.3.199:2348?token=kkkk");
ws.onopen = function() {
    ws.send('hello,thinkphp');
    console.log("给服务端发送一个字符串：hello,thinkphp");
};
ws.onmessage = function(e) {
    console.log("收到服务端的消息：" + e.data);
};
ws.send({type:'jj',message:'hahahaha'});



// 单向
/**
 * 与GatewayWorker建立websocket连接，域名和端口改为你实际的域名端口，
 * 其中端口为Gateway端口，即start_gateway.php指定的端口。
 * start_gateway.php 中需要指定websocket协议，像这样
 * $gateway = new Gateway(websocket://0.0.0.0:7272);
 */
ws = new WebSocket("ws://172.28.3.199:2348");
// 服务端主动推送消息时会触发这里的onmessage
ws.onmessage = function(e){
    // json数据转换成js对象
    var data = eval("("+e.data+")");
    var type = data.type || '';
    switch(type){
        // Events.php中返回的init类型的消息，将client_id发给后台进行uid绑定
        case 'init':
            // 利用jquery发起ajax请求，将client_id发给后端进行uid绑定
            $.post('http://xgservice.com/Chat/init', {client_id: data.client_id,token: jjjjjjjjj}, function(data){}, 'json');
            break;
        // 当mvc框架调用GatewayClient发消息时直接alert出来
        default :
            console.log("收到服务端的消息：" + e.data);
    }
};

$.post('http://xgservice.com/Chat', {client_id: data.client_id,mesagee: mesageemesageemesageemesagee}, function(data){}, 'json');