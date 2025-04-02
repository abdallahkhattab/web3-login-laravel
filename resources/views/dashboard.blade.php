<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Dashboard</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                    Logout
                </button>
            </form>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Welcome, {{ $user->name }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-medium mb-2">Your Wallet</h3>
                    <p class="text-gray-300">
                        <span class="font-mono text-sm break-all">{{ $user->wallet_address }}</span>
                    </p>
                </div>
                
                <div class="bg-gray-700 p-4 rounded-lg">
                    <h3 class="text-lg font-medium mb-2">Account Info</h3>
                    <p class="text-gray-300">Name: {{ $user->name }}</p>
                    <p class="text-gray-300">Email: {{ $user->email }}</p>
                    <p class="text-gray-300">Joined: {{ $user->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>