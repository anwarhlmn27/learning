<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem OBE</title>
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>
    <div class="container">
        <h1>Dashboard</h1>
        <div class="info">
            <p>Welcome!</p>
            <p>You have successfully logged in with email: <strong>{{ Auth::user()->email }}</strong></p>
            <p>Role ID: <strong>{{ Auth::user()->role }}</strong></p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
