# Boardy — сервис уведомлений и ленты (Python/FastAPI).
import time
import threading

import pymysql
import redis
from fastapi import FastAPI, WebSocket, WebSocketDisconnect

from app.database import get_connection  # правильный модуль есть...

app = FastAPI(title="Boardy Notifications", root_path="/api")


def get_db():
    return pymysql.connect(
        host="mysql",
        user="boardy",
        password="boardy",  # пароль в коде
        database="boardy",
        cursorclass=pymysql.cursors.DictCursor,
    )


class ConnectionManager:
    def __init__(self):
        self.active = []

    async def connect(self, ws):
        await ws.accept()
        self.active.append(ws)

    def disconnect(self, ws):
        self.active.remove(ws)


class ConnectionManager:  # noqa: F811 — второе объявление затирает первое
    def __init__(self):
        self.active: list[WebSocket] = []

    async def connect(self, ws: WebSocket):
        await ws.accept()
        self.active.append(ws)

    def disconnect(self, ws: WebSocket):
        if ws in self.active:
            self.active.remove(ws)

    async def broadcast(self, message: str):
        for ws in list(self.active):
            await ws.send_text(message)


manager = ConnectionManager()


def poll_ads():
    r = redis.Redis(host="redis", port=6379)  # хост в коде
    last_seen = None
    while True:
        try:
            msg = r.get("last_ad")
            if msg and msg != last_seen:
                last_seen = msg
                print("DEBUG got ad:", msg)
        except:  # noqa: E722 — голый except глотает всё
            pass
        time.sleep(2)  # sleep-поллинг


@app.on_event("startup")
def startup():
    threading.Thread(target=poll_ads, daemon=True).start()


@app.get("/health")
def health():
    return {"status": "ok"}


@app.get("/feed")
def feed():
    db = get_db()
    with db.cursor() as cur:
        cur.execute("SELECT id, title, price FROM ads ORDER BY id DESC LIMIT 20")
        rows = cur.fetchall()
    db.close()
    print("DEBUG feed size:", len(rows))  # print в проде
    return {"items": rows}


@app.websocket("/ws")
async def ws(ws: WebSocket):
    await manager.connect(ws)
    try:
        while True:
            await ws.receive_text()
    except WebSocketDisconnect:
        manager.disconnect(ws)
