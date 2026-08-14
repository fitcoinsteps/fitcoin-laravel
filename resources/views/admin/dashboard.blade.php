<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <div class="min-h-screen p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Super Admin Dashboard</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-500 px-4 py-2 rounded">Logout</button>
            </form>
        </div>
        <p>Welcome, {{ $user->first_name ?? 'Super Admin' }}!</p>
        <div class="mt-4 grid grid-cols-3 gap-4">
            <div class="bg-gray-800 p-4 rounded">
                <h3 class="font-bold">Total Users</h3>
                <p class="text-2xl">1,234</p>
            </div>
            <div class="bg-gray-800 p-4 rounded">
                <h3 class="font-bold">Admins</h3>
                <p class="text-2xl">3</p>
            </div>
            <div class="bg-gray-800 p-4 rounded">
                <h3 class="font-bold">Revenue</h3>
                <p class="text-2xl">$12,345</p>
            </div>
        </div>
    </div>
</body>
</html>