<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen">

<!-- Header -->
<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-bold text-gray-900">Welcome, {{ $member->name }}</h1>
            <form method="POST" action="{{ route('member.logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Attendance -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Attendance</h3>
            @if($member->attendances->count() > 0)
                <ul class="space-y-2">
                    @foreach($member->attendances as $attendance)
                        <li class="flex justify-between">
                            <span>{{ $attendance->check_in->format('M d, Y') }}</span>
                            <span class="text-green-600">{{ $attendance->check_in->format('H:i') }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No attendance records yet.</p>
            @endif
        </div>

        <!-- Workout Plan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Workout Plan</h3>
            @if($member->workoutPlan)
                <p class="text-gray-700">{{ $member->workoutPlan->name }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ $member->workoutPlan->description }}</p>
            @else
                <p class="text-gray-500">No workout plan assigned.</p>
            @endif
        </div>

        <!-- Diet Plan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Diet Plan</h3>
            @if($member->dietPlan)
                <p class="text-gray-700">{{ $member->dietPlan->name }}</p>
                <p class="text-sm text-gray-500 mt-2">{{ $member->dietPlan->description }}</p>
            @else
                <p class="text-gray-500">No diet plan assigned.</p>
            @endif
        </div>

        <!-- Membership -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Membership</h3>
            @if($member->activePlan)
                <p class="text-gray-700">{{ $member->activePlan->plan->name }}</p>
                <p class="text-sm text-gray-500">Expires: {{ $member->activePlan->end_date->format('M d, Y') }}</p>
            @else
                <p class="text-gray-500">No active membership.</p>
            @endif
        </div>

        <!-- QR Scanner -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mark Attendance</h3>
            <button id="scan-qr" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Scan QR Code
            </button>
            <div id="qr-reader" style="display: none; width: 100%;"></div>
        </div>

    </div>
</main>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.getElementById('scan-qr').addEventListener('click', function() {
    const qrReader = document.getElementById('qr-reader');
    qrReader.style.display = 'block';

    const html5QrCode = new Html5Qrcode("qr-reader");

    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        },
        (decodedText, decodedResult) => {
            // Process the QR code
            fetch('/member/scan-qr', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_data: decodedText })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                html5QrCode.stop();
                qrReader.style.display = 'none';
            })
            .catch(error => {
                alert('Error processing QR code');
                console.error(error);
            });
        },
        (errorMessage) => {
            // Ignore errors
        }
    ).catch((err) => {
        console.error(err);
    });
});
</script>

</body>
</html>