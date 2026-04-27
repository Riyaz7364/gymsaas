<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold">{{ $header ?? 'Trainer Panel' }}</h1>
                @if(isset($subheader))
                    <p class="text-sm text-gray-500">{{ $subheader }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('trainer.dashboard') }}" class="text-sm text-blue-600 hover:underline">Dashboard</a>
                <form method="POST" action="{{ route('trainer.logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded-md hover:bg-red-700">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
        {{ $slot }}
    </main>
</body>
</html>
