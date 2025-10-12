<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Mass Production Tracker</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <form action="{{ route('login.post') }}" method="POST" class="bg-white p-6 rounded-xl shadow-md w-80">
        @csrf
        <h1 class="text-lg font-semibold mb-4 text-center">Login</h1>

        <label>Email</label>
        <input type="email" name="email" class="border rounded w-full px-3 py-2 mb-2" required>

        <label>Password</label>
        <input type="password" name="password" class="border rounded w-full px-3 py-2 mb-3" required>

        @error('email')
            <p class="text-red-500 text-sm mb-2">{{ $message }}</p>
        @enderror

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
            Login
        </button>
    </form>
</body>
</html>
