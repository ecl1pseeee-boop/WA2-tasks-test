#!/usr/bin/env bash
# Воспроизводимая проверка Boardy: восемь функций + два ограничения заказчика
# (вход через GitHub жив, база MySQL отвечает). Ничего руками — одна команда.
set -u

BASE="${BASE:-http://localhost:8080}"
FAILED=0

WORKDIR=$(mktemp -d)
COOKIES="$WORKDIR/cookies.txt"
trap 'rm -rf "$WORKDIR"' EXIT

pass() { echo "OK   $1"; }
fail() { echo "FAIL $1"; FAILED=1; }

# Проверка по коду ответа: check_code "<название>" <ожидаемый код> "<url>"
check_code() {
  local name="$1" want="$2" url="$3" code
  code=$(curl -s -o /dev/null -w '%{http_code}' "$url")
  if [ "$code" = "$want" ]; then pass "$name ($code)"; else fail "$name (ожидали $want, получили $code)"; fi
}

# Проверка по коду + подстроке в теле: check_body "<название>" <код> "<url>" "<подстрока>"
check_body() {
  local name="$1" want="$2" url="$3" needle="$4" out code body
  out=$(curl -s -w '\n%{http_code}' "$url")
  code=$(printf '%s' "$out" | tail -n1)
  body=$(printf '%s' "$out" | sed '$d')
  if [ "$code" = "$want" ] && printf '%s' "$body" | grep -qF "$needle"; then
    pass "$name"
  else
    fail "$name (код $code, ожидалась подстрока «$needle»)"
  fi
}

# Достать CSRF-токен со страницы формы (и обновить куки в COOKIES)
csrf_token() {
  curl -s -c "$COOKIES" -b "$COOKIES" "$1" \
    | sed -n 's/.*name="_token" value="\([^"]*\)".*/\1/p' | head -n1
}

echo "=== Открытие страниц ==="
check_body "лента"                    200 "$BASE/"        "Лента объявлений"
check_code "страница входа"           200 "$BASE/login"
check_code "страница регистрации"     200 "$BASE/register"
check_code "список пользователей"     200 "$BASE/users"
check_code "REST-API документация"    200 "$BASE/api/docs"

echo "=== Ограничение: база MySQL отвечает ==="
if docker compose exec -T mysql mysqladmin ping -h localhost --silent >/dev/null 2>&1; then
  pass "MySQL отвечает"
else
  fail "MySQL не отвечает"
fi

echo "=== Регистрация ==="
STAMP="$(date +%s)_$$"
EMAIL="smoke_${STAMP}@example.test"
PASSWORD="smoke-pass-12345"
TOKEN=$(csrf_token "$BASE/register")
curl -s -o /dev/null -c "$COOKIES" -b "$COOKIES" \
  --data-urlencode "_token=$TOKEN" \
  --data-urlencode "name=Smoke Test" \
  --data-urlencode "email=$EMAIL" \
  --data-urlencode "password=$PASSWORD" \
  "$BASE/register"
CREATE_CODE=$(curl -s -o /dev/null -w '%{http_code}' -b "$COOKIES" "$BASE/ads/create")
if [ "$CREATE_CODE" = "200" ]; then
  pass "регистрация ($EMAIL)"
else
  fail "регистрация: после /register сессия не залогинена ($CREATE_CODE)"
fi

echo "=== Выход и вход паролем ==="
LOGOUT_TOKEN=$(csrf_token "$BASE/")
curl -s -o /dev/null -c "$COOKIES" -b "$COOKIES" \
  --data-urlencode "_token=$LOGOUT_TOKEN" \
  "$BASE/logout"

LOGIN_TOKEN=$(csrf_token "$BASE/login")
curl -s -o /dev/null -c "$COOKIES" -b "$COOKIES" \
  --data-urlencode "_token=$LOGIN_TOKEN" \
  --data-urlencode "email=$EMAIL" \
  --data-urlencode "password=$PASSWORD" \
  "$BASE/login"
LOGIN_CHECK=$(curl -s -o /dev/null -w '%{http_code}' -b "$COOKIES" "$BASE/ads/create")
if [ "$LOGIN_CHECK" = "200" ]; then
  pass "вход паролем ($EMAIL)"
else
  fail "вход паролем: сессия не залогинена ($LOGIN_CHECK)"
fi

echo "=== Вход через GitHub (функция + ограничение заказчика) ==="
GH_HEADERS=$(curl -s -D - -o /dev/null "$BASE/auth/github")
GH_CODE=$(printf '%s' "$GH_HEADERS" | head -n1 | tr -d '\r' | awk '{print $2}')
GH_LOCATION=$(printf '%s' "$GH_HEADERS" | grep -i '^Location:' | tr -d '\r')
if [ "$GH_CODE" = "302" ] && printf '%s' "$GH_LOCATION" | grep -q "github.com"; then
  pass "вход через GitHub (редирект на GitHub OAuth)"
else
  fail "вход через GitHub (код $GH_CODE, Location: $GH_LOCATION)"
fi

echo "=== Подача объявления ==="
AD_TITLE="Smoke Ad ${STAMP}"
AD_TOKEN=$(csrf_token "$BASE/ads/create")
AD_HEADERS=$(curl -s -D - -o /dev/null -c "$COOKIES" -b "$COOKIES" \
  --data-urlencode "_token=$AD_TOKEN" \
  --data-urlencode "title=$AD_TITLE" \
  --data-urlencode "description=Smoke test description ${STAMP}" \
  --data-urlencode "price=100" \
  "$BASE/ads")
AD_LOCATION=$(printf '%s' "$AD_HEADERS" | grep -i '^Location:' | tr -d '\r' | awk '{print $2}')
AD_ID=$(printf '%s' "$AD_LOCATION" | grep -oE '[0-9]+$')
if [ -n "$AD_ID" ]; then
  check_body "подача объявления" 200 "$BASE/ads/$AD_ID" "$AD_TITLE"
else
  fail "подача объявления (не удалось получить id из редиректа: '$AD_LOCATION')"
fi

echo "=== Поиск ==="
if [ -n "${AD_ID:-}" ]; then
  check_body "поиск" 200 "$BASE/?q=$(printf '%s' "$AD_TITLE" | tr ' ' '+')" "$AD_TITLE"
else
  fail "поиск (пропущен: объявление не создано)"
fi

echo "=== Просмотр объявления ==="
if [ -n "${AD_ID:-}" ]; then
  check_body "просмотр объявления" 200 "$BASE/ads/$AD_ID" "Smoke test description ${STAMP}"
else
  fail "просмотр объявления (пропущен: объявление не создано)"
fi

echo "=== Комментарий ==="
if [ -n "${AD_ID:-}" ]; then
  COMMENT_TEXT="Smoke comment ${STAMP}"
  COMMENT_TOKEN=$(csrf_token "$BASE/ads/$AD_ID")
  curl -s -o /dev/null -c "$COOKIES" -b "$COOKIES" \
    --data-urlencode "_token=$COMMENT_TOKEN" \
    --data-urlencode "body=$COMMENT_TEXT" \
    "$BASE/ads/$AD_ID/comments"
  check_body "комментарий" 200 "$BASE/ads/$AD_ID" "$COMMENT_TEXT"
else
  fail "комментарий (пропущен: объявление не создано)"
fi

echo
if [ "$FAILED" = "0" ]; then
  echo "ИТОГ: всё зелёное"
else
  echo "ИТОГ: есть провалы"
fi
exit $FAILED
