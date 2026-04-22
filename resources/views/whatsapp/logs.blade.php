<x-layouts.app>
    <x-slot:title>WhatsApp Logs — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>WhatsApp Logs</x-slot:header>
    <x-slot:topbarTitle>WhatsApp</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / WhatsApp / Logs</x-slot:breadcrumb>

    <div class="gh-card">
        <div class="gh-card-body" style="text-align:center; padding:60px 20px;">
            <div style="font-size:48px; margin-bottom:16px;">📋</div>
            <h3 style="font-size:18px; font-weight:600; margin-bottom:8px;">WhatsApp Logs</h3>
            <p style="color:#9ca3af; font-size:14px;">Message logs will appear here once the WhatsApp integration is configured.</p>
        </div>
    </div>
</x-layouts.app>
