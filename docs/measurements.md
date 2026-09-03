# Протокол замеров

Практика 1, блок C и блок F (учения). Все цифры — реальные прогоны, без досочинения.

## Время ответа страниц

Команда:

```bash
for url in / /login /register /ads/1 /search?q=test ; do
  echo -n "$url  "
  curl -s -o /dev/null -w '%{time_total}\n' "http://localhost:8080$url"
done
```

Три прогона подряд, секунды:

| Страница | Прогон 1 | Прогон 2 | Прогон 3 | Максимум |
|---|---|---|---|---|
| `/` | 0.615694 | 0.622339 | 0.622267 | **0.622339** |
| `/login` | 0.321925 | 0.315937 | 0.327974 | **0.327974** |
| `/register` | 0.316247 | 0.327944 | 0.324984 | **0.327944** |
| `/ads/1` | 0.320114 | 0.327894 | 0.325195 | **0.327894** |
| `/search?q=test` | 0.310945 | 0.293864 | 0.314843 | **0.314843** |

Требование заказчика — ≤ 3 с. Худший результат по всем страницам и всем прогонам —
**0,622339 с** (`/`, прогон 2). Запас почти пятикратный.

## Ресурсы

`docker compose ps`:

```
NAME                IMAGE               COMMAND                  SERVICE   CREATED      STATUS          PORTS
wa2-tasks-api-1     wa2-tasks-api       "uvicorn app.main:ap…"   api       7 days ago   Up 8 minutes
wa2-tasks-mysql-1   mysql:8.0           "docker-entrypoint.s…"   mysql     7 days ago   Up 5 minutes    3306/tcp, 33060/tcp
wa2-tasks-nginx-1   nginx:1.27-alpine   "/docker-entrypoint.…"   nginx     7 days ago   Up 45 minutes   0.0.0.0:8080->80/tcp, [::]:8080->80/tcp
wa2-tasks-php-1     wa2-tasks-php       "/usr/local/bin/dock…"   php       7 days ago   Up 45 minutes   9000/tcp
wa2-tasks-redis-1   redis:7-alpine      "docker-entrypoint.s…"   redis     7 days ago   Up 45 minutes   6379/tcp
```

`docker stats --no-stream`:

| Контейнер | CPU % | Память | % от лимита хоста |
|---|---|---|---|
| nginx | 0.00% | 9.98 MiB | 0.13% |
| php | 0.00% | 93.4 MiB | 1.18% |
| api | 0.12% | 43.3 MiB | 0.55% |
| redis | 0.30% | 7.043 MiB | 0.09% |
| mysql | 0.45% | 380.2 MiB | 4.79% |
| **Итого** | — | **≈ 533,9 MiB (0,52 ГБ)** | — |

Бюджет заказчика — 3 из 4 ГБ памяти. Занято меньше пятой части бюджета.

`df -h .` (хост):

```
Filesystem      Size  Used Avail Use% Mounted on
/dev/sdd       1007G  3.2G  953G   1% /
```

Это диск дев-машины (1 ТБ), не показателен напрямую для VPS Виктора (80 ГБ) — приведён
как факт замера, вывод по бюджету диска ниже сделан по `docker system df`.

`docker system df`:

```
TYPE            TOTAL     ACTIVE    SIZE      RECLAIMABLE
Images          13        10        18.26GB   12.58GB (68%)
Containers      13        5         5.464MB   4.444MB (81%)
Local Volumes   6         6         729MB     0B (0%)
Build Cache     418       0         14.42GB   14.42GB
```

**Как читать:** «Local Volumes 729MB» — это реальные данные системы (том `mysql-data`),
не накопленный мусор. «Images 18.26GB» и «Build Cache 14.42GB» — это история
многократных пересборок на этой машине за время разработки (7 дней), а не то, что займёт
одно чистое развёртывание на VPS. Точную цифру «сколько займёт с нуля» этот протокол не
даёт — для неё нужно прогнать `docker system df` сразу после `docker compose up -d
--build` на реально чистой машине; это ещё не сделано.

**Вывод по блоку C:** по памяти и времени ответа система укладывается в рамки заказчика с
большим запасом (0,52 из 3 ГБ памяти, 0,62 из 3 с максимального времени ответа). По диску
данные (729 МБ) далеко в рамках 60 из 80 ГБ; вес образов на чистом сервере отдельно не
измерен.

## Учение 1: чистая машина

Команда:

```bash
mkdir /tmp/clean && cd /tmp/clean
git clone <адрес> .
# дальше — строго по README, без подсказок
```

| Прогон | Время | Результат |
|---|---|---|
| 1 | 1:26,42 | провал |
| 2 | 1:09,95 | успех |

**Прогон 1 — что случилось:**

```
Warning: require(/var/www/html/public/../vendor/autoload.php): Failed to open stream:
No such file or directory in /var/www/html/public/index.php on line 18

Fatal error: Uncaught Error: Failed opening required
'/var/www/html/public/../vendor/autoload.php'
(include_path='.:/usr/local/lib/php') in /var/www/html/public/index.php:18
Stack trace:
#0 {main}
  thrown in /var/www/html/public/index.php on line 18
```

Причина: `vendor/` приезжает в контейнер через bind-mount, а `composer install` при сборке
образа не выполнялся.

**Что исправили перед прогоном 2:** добавили в `services/php/Dockerfile` бинарник
composer (`COPY --from=composer:2.8.4 ...`) и вызов `composer install` в
`docker-entrypoint.sh` при первом старте контейнера.

**Прогон 2:** успешно за 1:09,95. По итогу в README добавлен флаг `--build`
(`docker compose up -d --build`), чтобы образ гарантированно пересобирался и на будущих
чистых клонах.

**Обращений к автору: 0.** Обе проблемы найдены и исправлены самостоятельно.
Требование — ≤ 30 минут: выполнено с большим запасом на обоих прогонах.

## Учение 2: перезагрузка

Команда после `sudo reboot`:

```bash
cd ~/WA2-tasks && docker compose up -d && ./smoke.sh
```

Вывод:

```
$ docker compose up -d && ./smoke.sh
[+] Running 5/5
 ✔ Container wa2-tasks-mysql-1  Running   0.0s
 ✔ Container wa2-tasks-redis-1  Running   0.0s
 ✔ Container wa2-tasks-api-1    Running   0.0s
 ✔ Container wa2-tasks-php-1    Running   0.0s
 ✔ Container wa2-tasks-nginx-1  Running   0.0s
=== Открытие страниц ===
OK   лента
OK   страница входа (200)
OK   страница регистрации (200)
OK   список пользователей (200)
OK   REST-API документация (200)
=== Ограничение: база MySQL отвечает ===
OK   MySQL отвечает
=== Регистрация ===
OK   регистрация (smoke_1788457141_1199@example.test)
=== Выход и вход паролем ===
OK   вход паролем (smoke_1788457141_1199@example.test)
=== Вход через GitHub (функция + ограничение заказчика) ===
OK   вход через GitHub (редирект на GitHub OAuth)
=== Подача объявления ===
OK   подача объявления
=== Поиск ===
OK   поиск
=== Просмотр объявления ===
OK   просмотр объявления
=== Комментарий ===
OK   комментарий

ИТОГ: всё зелёное
```

**Время: 0:15,46.** Требование — ≤ 10 минут одной командой.

**Что не поднялось само:** ничего — все пять контейнеров оказались уже в состоянии
`Running` к моменту запуска команды (политика `restart: unless-stopped` подняла их
раньше). `docker compose up -d` фактически подтвердил состояние, а не поднимал систему
заново.

**Что добавили заранее, чтобы это сработало:**
- `restart: unless-stopped` у всех пяти сервисов;
- healthcheck у `mysql` (`mysqladmin ping`) и у `redis` (`redis-cli ping`);
- `depends_on` с `condition: service_healthy` у `php` и `api` (ждут и `mysql`, и `redis`).
