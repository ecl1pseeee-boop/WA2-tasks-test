# Boardy

Доска объявлений.

## Предусловия
- Установлен Docker и Docker Compose.
- Порт `8080` на хосте свободен.

## Запуск с нуля

```bash
git clone -b p01 https://github.com/ecl1pseeee-boop/WA2-tasks-test.git WA2-tasks
cd WA2-tasks
cp services/php/.env.example services/php/.env
```

Открыть `services/php/.env` и поменять одну строку:

```
DB_CONNECTION=mysql
```

Если нужен вход через GitHub — дописать в `services/php/.env`:

```
GITHUB_CLIENT_ID=...
GITHUB_CLIENT_SECRET=...
GITHUB_REDIRECT_URI=http://localhost:8080/auth/github/callback
```

```bash
docker compose up -d --build
```

## Как проверить, что получилось

```bash
docker compose ps
./smoke.sh; echo $?
```
