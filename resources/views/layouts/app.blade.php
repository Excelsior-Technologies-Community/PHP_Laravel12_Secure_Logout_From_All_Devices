<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                {{ config('app.name', 'Laravel') }}
            </a>

            <div class="ms-auto">
                @auth
                    <span class="text-white me-3">
                        {{ auth()->user()->name }}
                    </span>

                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-light">
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Page Content --}}
    <main class="py-4">
        @yield('content')
    </main>

</body>
</html>
