import threading
import time
from queue import Queue
from flask import Flask, request
from websocket import WebSocketApp
# 创建 Flask 应用程序 , 用于中转websocket信息， PHP不支持多线程用python实现
app = Flask(__name__)
# 创建一个队列，用于在不同的线程中传递消息
message_queue = Queue()
# HTTP 请求处理程序
@app.route('/', methods=['POST'])
def handle_request():
    data = request.form.get('data')
    print('收到消息：', data)
    message_queue.put(data)
    return 'OK'
# WebSocket 长连接处理程序
heartbeat_message = 'heartbeat'
heartbeat_interval = 10
def websocket_thread():
    while True :
        if ws.keep_running:
            ws.send(heartbeat_message)
        time.sleep(heartbeat_interval)
def on_message(ws, message):
    print('收到websocket消息：', message)
def on_error(ws, error):
    print('WebSocket 出错：', error)
def on_close(ws):
    print('WebSocket 已关闭 后重新连接')
    ws.run_forever()
def on_open(ws):
    print('WebSocket 已连接')
    while True:
        message = message_queue.get()
        ws.send(message)
# 创建 WebSocket 客户端
ws = WebSocketApp('ws://crm.zhaoyuhao.com/api/app/ws')
ws.on_open = on_open
ws.on_error = on_error
ws.on_close = on_close
ws.op_message = on_message
# 创建 HTTP 服务线程
http_thread = threading.Thread(target=app.run, kwargs={'port': 8990})
websocket_thread = threading.Thread(target=websocket_thread)
http_thread.start()
websocket_thread.start()
# 运行 WebSocket 客户端
ws.run_forever()
# 等待两个线程结束
http_thread.join()
websocket_thread.join()