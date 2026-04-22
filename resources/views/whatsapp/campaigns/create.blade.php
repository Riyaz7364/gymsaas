<x-layouts.app>
    <x-slot:title>New Campaign — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>New WhatsApp Campaign</x-slot:header>
    <x-slot:topbarTitle>New Campaign</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('whatsapp.campaigns.index') }}">Campaigns</a> / New</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:600px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Campaign Details</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('whatsapp.campaigns.store') }}">
                @csrf
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <label class="gh-label">Campaign Name *</label>
                        <input type="text" name="name" class="gh-input" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Message Body *</label>
                        <textarea name="body" class="gh-input" rows="5" required placeholder="Hi {name}, ...">{{ old('body') }}</textarea>
                        <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Use {name} to personalize with member name.</div>
                    </div>
                    <div>
                        <label class="gh-label">Schedule At (optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="gh-input" value="{{ old('scheduled_at') }}">
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save as Draft</button>
                    <a href="{{ route('whatsapp.campaigns.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
