<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Boardy — доска объявлений')</title>
    <style>
        :root {
            --color-bg: #f4f5f7;
            --color-surface: #ffffff;
            --color-border: #e2e5ea;
            --color-text: #1f2430;
            --color-muted: #6b7280;
            --color-brand: #2f6fed;
            --color-brand-dark: #234fb8;
            --color-danger: #d9432f;
            --color-danger-bg: #fdecea;
            --color-success: #1f8a4c;
            --color-success-bg: #e9f8ee;
            --radius: 10px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--color-bg);
            color: var(--color-text);
            line-height: 1.5;
        }

        a { color: var(--color-brand); text-decoration: none; }
        a:hover { text-decoration: underline; }

        .container {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header.site-header {
            background: var(--color-surface);
            border-bottom: 1px solid var(--color-border);
        }

        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            color: var(--color-text);
        }
        .brand:hover { text-decoration: none; }
        .brand span { color: var(--color-brand); }

        nav.main-nav {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        nav.main-nav a {
            color: var(--color-text);
            font-weight: 500;
        }

        .nav-cta {
            background: var(--color-brand);
            color: #fff !important;
            padding: 8px 16px;
            border-radius: var(--radius);
            font-weight: 600;
        }
        .nav-cta:hover { background: var(--color-brand-dark); text-decoration: none; }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .nav-user .username {
            font-weight: 600;
            color: var(--color-muted);
        }

        form.inline { display: inline; margin: 0; }

        .btn {
            display: inline-block;
            font: inherit;
            font-weight: 600;
            padding: 9px 16px;
            border-radius: var(--radius);
            border: 1px solid var(--color-border);
            background: var(--color-surface);
            color: var(--color-text);
            cursor: pointer;
        }
        .btn:hover { background: var(--color-bg); text-decoration: none; }

        .btn-primary {
            background: var(--color-brand);
            border-color: var(--color-brand);
            color: #fff;
        }
        .btn-primary:hover { background: var(--color-brand-dark); }

        .btn-link {
            background: none;
            border: none;
            color: var(--color-brand);
            cursor: pointer;
            padding: 0;
            font: inherit;
            font-weight: 600;
        }
        .btn-link:hover { text-decoration: underline; }

        main { padding: 28px 0 60px; }

        h1 { font-size: 26px; margin: 0 0 20px; }
        h2 { font-size: 20px; margin: 0 0 12px; }

        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-success {
            background: var(--color-success-bg);
            color: var(--color-success);
            border: 1px solid #bfe6cc;
        }
        .alert-error {
            background: var(--color-danger-bg);
            color: var(--color-danger);
            border: 1px solid #f3c3bc;
        }
        .alert-error ul { margin: 4px 0 0; padding-left: 18px; }

        .card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            padding: 20px;
        }

        .card + .card { margin-top: 14px; }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 22px;
        }
        .search-bar input[type="text"] {
            flex: 1;
        }

        .ad-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            padding: 18px 20px;
            margin-bottom: 14px;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .ad-card-main { flex: 1; min-width: 200px; }

        .ad-card h3 {
            margin: 0 0 6px;
            font-size: 18px;
        }

        .ad-card .meta {
            color: var(--color-muted);
            font-size: 13px;
        }

        .ad-card .price {
            font-size: 20px;
            font-weight: 700;
            color: var(--color-brand);
            white-space: nowrap;
        }

        .badge {
            display: inline-block;
            background: var(--color-bg);
            border: 1px solid var(--color-border);
            border-radius: 999px;
            padding: 2px 10px;
            font-size: 12px;
            color: var(--color-muted);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--color-muted);
        }

        .pagination-wrap {
            margin-top: 20px;
        }
        /* Разметка идёт из штатного bootstrap-5 view пагинатора Laravel — здесь только
           наша собственная стилизация без подключения Bootstrap CSS. */
        .pagination-wrap nav {
            display: flex;
        }
        .pagination-wrap nav > div:first-child { display: none; }
        .pagination-wrap nav > div:last-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            width: 100%;
        }
        .pagination-wrap ul.pagination {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 4px;
        }
        .pagination-wrap .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            background: var(--color-surface);
            color: var(--color-text);
            text-decoration: none;
            font-size: 14px;
        }
        .pagination-wrap a.page-link:hover { background: var(--color-bg); }
        .pagination-wrap .page-item.active .page-link {
            background: var(--color-brand);
            color: #fff;
            border-color: var(--color-brand);
        }
        .pagination-wrap .page-item.disabled .page-link {
            color: var(--color-muted);
            cursor: not-allowed;
        }
        .pagination-wrap .small.text-muted { color: var(--color-muted); font-size: 13px; }
        .pagination-wrap .fw-semibold { font-weight: 600; }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            font: inherit;
            background: var(--color-surface);
            color: var(--color-text);
        }

        textarea { resize: vertical; min-height: 100px; }

        .field { margin-bottom: 16px; }
        .field-error {
            color: var(--color-danger);
            font-size: 13px;
            margin-top: 4px;
        }

        .form-narrow {
            max-width: 420px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid var(--color-border);
            font-size: 14px;
        }
        table th { color: var(--color-muted); font-weight: 600; }

        .ad-detail-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .ad-photo {
            max-width: 100%;
            border-radius: var(--radius);
            margin: 16px 0;
            display: block;
        }

        .comment {
            border-top: 1px solid var(--color-border);
            padding: 14px 0;
        }
        .comment:first-child { border-top: none; }
        .comment .meta { font-size: 13px; color: var(--color-muted); margin-bottom: 4px; }

        .muted { color: var(--color-muted); }

        .auth-links { text-align: center; margin-top: 16px; font-size: 14px; }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a href="/" class="brand">Board<span>y</span></a>
            <nav class="main-nav">
                <a href="/">Лента</a>
                <a href="/users">Пользователи</a>
                <a href="/ads/create" class="nav-cta">Подать объявление</a>
                @auth
                    <div class="nav-user">
                        <span class="username">{{ auth()->user()->name }}</span>
                        <form action="/logout" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="btn-link">Выйти</button>
                        </form>
                    </div>
                @else
                    <div class="nav-user">
                        <a href="/login">Войти</a>
                        <a href="/register" class="btn">Регистрация</a>
                    </div>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <strong>Проверьте форму:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
