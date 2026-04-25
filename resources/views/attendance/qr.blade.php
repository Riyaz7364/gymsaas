<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily QR Code - {{ config('app.name') }}</title>
    @vite('resources/js/qrcode.js')
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Daily Attendance QR Code</h1>

        <div class="text-center">
            <p class="text-gray-600 mb-4">Today's QR Code for Member Attendance</p>
            <div id="qrcode" class="inline-block"></div>
            <p class="text-sm text-gray-500 mt-4">Date: {{ today()->format('F j, Y') }}</p>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('attendance.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Back to Attendance
            </a>
        </div>
    </div>
</div>

<script>
    
    document.addEventListener("DOMContentLoaded", function () {
        const qrData = @json($qrData);
        generateQR("qrcode", qrData);
    });
</script>

</body>
</html>