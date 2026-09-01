<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXUS</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0b3d91">
    @vite(['resources/css/app.css'])
</head>
<body>
    @auth
    <nav class="navbar">
        <a href="{{ route('dashboard') }}" class="brand">NEXUS</a>
        <div class="nav-links">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('value-declarations.index') }}">My Declarations</a>
            <a href="{{ route('matches') }}">Matches</a>
            <a href="{{ route('exchanges.index') }}">Exchanges</a>
            <a href="{{ route('trust-profile') }}">Trust Profile</a>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="link-btn">Logout</button>
            </form>
        </div>
    </nav>
    @endauth

    <main class="container">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js');
        }
    </script>
</body>
</html>