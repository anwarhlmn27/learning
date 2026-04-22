<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem OBE</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f3f4f6; 
            padding: 3rem 1rem; 
            margin: 0;
            color: #1f2937;
        }
        .container { 
            background: white; 
            padding: 2.5rem; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            max-width: 600px; 
            margin: 0 auto; 
        }
        h1 {
            margin-top: 0;
            color: #111827;
            font-size: 1.75rem;
        }
        .info {
            background-color: #f8fafc;
            border-left: 4px solid #4f46e5;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }
        .info p {
            margin: 0.5rem 0;
        }
        .info strong {
            color: #4f46e5;
        }
        button { 
            padding: 0.75rem 1.5rem; 
            background: #ef4444; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            cursor: pointer; 
            font-weight: 600;
            transition: background-color 0.15s ease-in-out;
        }
        button:hover {
            background-color: #dc2626;
        }
    </style>
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
