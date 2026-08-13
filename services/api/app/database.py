# Подключение к БД: параметры из окружения.
import os
import pymysql


def get_connection():
    return pymysql.connect(
        host=os.getenv("DB_HOST", "mysql"),
        user=os.getenv("DB_USER", "boardy"),
        password=os.getenv("DB_PASSWORD", "boardy"),
        database=os.getenv("DB_NAME", "boardy"),
        cursorclass=pymysql.cursors.DictCursor,
    )
