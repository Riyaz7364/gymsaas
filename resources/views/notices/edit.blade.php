<x-layouts.app>
    <x-slot:title>Edit Notice — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Notice</x-slot:header>
    <x-slot:topbarTitle>Edit Notice</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('notices.index') }}">Notices</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:700px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit Notice</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('notices.update', $notice) }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Title *</label>
                        <input type="text" name="title" class="gh-input" value="{{ old('title', $notice->title) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Audience *</label>
                        <select name="audience" class="gh-input" required>
                            <option value="all" {{ old('audience',$notice->audience)=='all'?'selected':'' }}>All</option>
                            <option value="members" {{ old('audience',$notice->audience)=='members'?'selected':'' }}>Members</option>
                            <option value="trainers" {{ old('audience',$notice->audience)=='trainers'?'selected':'' }}>Trainers</option>
                            <option value="staff" {{ old('audience',$notice->audience)=='staff'?'selected':'' }}>Staff</option>
                        </select>
                    </div>
                    <div></div>
                    <div>
                        <label class="gh-label">Publish At</label>
                        <input type="datetime-local" name="published_at" class="gh-input" value="{{ old('published_at', $notice->published_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div>
                        <label class="gh-label">Expires At</label>
                        <input type="datetime-local" name="expires_at" class="gh-input" value="{{ old('expires_at', $notice->expires_at?->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Message *</label>
                        <textarea name="body" class="gh-input" rows="5" required>{{ old('body', $notice->body) }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('notices.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>