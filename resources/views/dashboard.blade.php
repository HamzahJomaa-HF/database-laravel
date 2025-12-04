<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; }
        .user-info { background: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to Dashboard!</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
        
        <div class="user-info">
            <h3>User Information:</h3>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ $user->role }}</p>
            <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
        </div>
        
        <div>
            <h3>What would you like to do?</h3>
            <ul>
                <li><a href="#">View Profile</a></li>
                <li><a href="#">Edit Settings</a></li>
                @if($user->isAdmin())
                    <li><a href="#">Admin Panel</a></li>
                    <li><a href="#">Manage Users</a></li>
                @endif
            </ul>
        </div>
    </div>
</body>
</html>