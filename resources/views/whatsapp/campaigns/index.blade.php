<x-layouts.app>
    <x-slot:title>WhatsApp Campaigns — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>WhatsApp Campaigns</x-slot:header>
    <x-slot:topbarTitle>WhatsApp Campaigns</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / WhatsApp / Campaigns</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Campaigns</h3>
            <a href="{{ route('whatsapp.campaigns.create') }}" class="gh-btn gh-btn-primary">+ New Campaign</a>
        </div>
        @if($campaigns->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">📱</div>
            <h4>No campaigns yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Create WhatsApp campaigns to engage your members.</p>
            <a href="{{ route('whatsapp.campaigns.create') }}" class="gh-btn gh-btn-primary">+ New Campaign</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Name</th><th>Status</th><th>Scheduled At</th><th>Sent</th><th>Failed</th><th>Created</th></tr></thead>
                <tbody>
                @foreach($campaigns as $c)
                <tr>
                    <td style="font-weight:500;">{{ $c->name }}</td>
                    <td>
                        @if($c->status==='sent')<span class="gh-badge gh-badge-success">Sent</span>
                        @elseif($c->status==='sending')<span class="gh-badge gh-badge-info">Sending</span>
                        @elseif($c->status==='scheduled')<span class="gh-badge gh-badge-warning">Scheduled</span>
                        @elseif($c->status==='failed')<span class="gh-badge gh-badge-danger">Failed</span>
                        @else<span class="gh-badge gh-badge-muted">Draft</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#6b7280;">{{ $c->scheduled_at?->format('d M Y H:i') ?? '—' }}</td>
                    <td style="color:#22c55e;">{{ $c->sent_count ?? 0 }}</td>
                    <td style="color:#ef4444;">{{ $c->failed_count ?? 0 }}</td>
                    <td style="font-size:13px;color:#6b7280;">{{ $c->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $campaigns->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>