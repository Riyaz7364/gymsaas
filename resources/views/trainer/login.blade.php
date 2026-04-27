<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Login - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Trainer Login</h1>
            <p class="text-gray-600 mt-2">Access your trainer dashboard</p>
        </div>

        <form method="POST" action="{{ route('trainer.login') }}">
            @csrf
            <div class="space-y-5">
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" />
                    @error('phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500" />
                    @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Login</button>
            </div>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            <a href="{{ route('member.login') }}" class="text-blue-600 hover:underline">Member Login</a> ·
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Admin Login</a>
        </div>
    </div>
</body>
</html>
