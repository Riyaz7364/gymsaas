<x-layouts.app>
    <x-slot:title>Edit Contact — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Contact</x-slot:header>
    <x-slot:topbarTitle>Edit Contact</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('contact-diary.index') }}">Contact Diary</a> / Edit</x-slot:breadcrumb>

    <div class="gh-card" style="max-width:650px;">
        <div class="gh-card-header"><h3 class="gh-card-title">Edit: {{ $contact->contact_name }}</h3></div>
        <div class="gh-card-body">
            @if($errors->any())
            <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('contact-diary.update', $contact) }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Contact Name *</label>
                        <input type="text" name="contact_name" class="gh-input" value="{{ old('contact_name', $contact->contact_name) }}" required>
                    </div>
                    <div>
                        <label class="gh-label">Phone</label>
                        <input type="text" name="phone" class="gh-input" value="{{ old('phone', $contact->phone) }}">
                    </div>
                    <div>
                        <label class="gh-label">Email</label>
                        <input type="email" name="email" class="gh-input" value="{{ old('email', $contact->email) }}">
                    </div>
                    <div>
                        <label class="gh-label">Type</label>
                        <input type="text" name="type" class="gh-input" value="{{ old('type', $contact->type) }}" placeholder="prospect, vendor, partner...">
                    </div>
                    <div>
                        <label class="gh-label">Status *</label>
                        <select name="status" class="gh-input" required>
                            <option value="open" {{ old('status',$contact->status)=='open'?'selected':'' }}>Open</option>
                            <option value="done" {{ old('status',$contact->status)=='done'?'selected':'' }}>Done</option>
                            <option value="cancelled" {{ old('status',$contact->status)=='cancelled'?'selected':'' }}>Cancelled</option>
                        </select>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Follow-up Date</label>
                        <input type="date" name="follow_up_date" class="gh-input" value="{{ old('follow_up_date', $contact->follow_up_date?->format('Y-m-d')) }}">
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="gh-label">Notes</label>
                        <textarea name="notes" class="gh-input" rows="3">{{ old('notes', $contact->notes) }}</textarea>
                    </div>
                </div>
                <div style="margin-top:20px;display:flex;gap:10px;">
                    <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                    <a href="{{ route('contact-diary.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>