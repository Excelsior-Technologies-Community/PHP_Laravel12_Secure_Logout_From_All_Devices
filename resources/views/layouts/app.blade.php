<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            min-height: 100vh;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            box-shadow: 0 5px 20px rgba(0,0,0,.15);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 22px;
        }

        .user-badge {
            background: rgba(255,255,255,.15);
            padding: 8px 14px;
            border-radius: 10px;
            color: white;
            margin-right: 10px;
        }

        .logout-btn {
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">

        <div class="container">

            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-shield-lock-fill"></i>
                Security Portal
            </a>

            <div class="ms-auto d-flex align-items-center">

                @auth

                    <span class="user-badge">
                        <i class="bi bi-person-circle"></i>
                        {{ auth()->user()->name }}
                    </span>

                    <form method="POST"
                        action="{{ route('logout') }}"
                        class="d-inline">

                        @csrf

                        <button class="btn btn-outline-light logout-btn">

                            <i class="bi bi-box-arrow-right"></i>
                            Logout

                        </button>

                    </form>

                @endauth

            </div>

        </div>

    </nav>

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>