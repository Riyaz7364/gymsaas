<x-layouts.app>
    <x-slot:title>Contact Diary — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Contact Diary</x-slot:header>
    <x-slot:topbarTitle>Contact Diary</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / Contact Diary</x-slot:breadcrumb>

    @if(session('success'))
    <div class="gh-alert gh-alert-success">{{ session('success') }}</div>
    @endif

    <div class="gh-card">
        <div class="gh-card-header">
            <h3 class="gh-card-title">Contacts <span style="color:#9ca3af;font-weight:400;font-size:13px;">({{ $contacts->total() }})</span></h3>
            <a href="{{ route('contact-diary.create') }}" class="gh-btn gh-btn-primary gh-btn-sm">+ Add Contact</a>
        </div>
        @if($contacts->isEmpty())
        <div class="gh-card-body" style="text-align:center;padding:60px;">
            <div style="font-size:40px;margin-bottom:12px;">📓</div>
            <h4>No contacts yet</h4>
            <p style="color:#9ca3af;font-size:14px;margin-bottom:16px;">Track prospects, vendors, and partners here.</p>
            <a href="{{ route('contact-diary.create') }}" class="gh-btn gh-btn-primary">+ Add Contact</a>
        </div>
        @else
        <div class="gh-card-body" style="padding:0;">
            <table class="gh-table">
                <thead><tr><th>Name</th><th>Phone/Email</th><th>Type</th><th>Status</th><th>Follow-up</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($contacts as $contact)
                <tr>
                    <td style="font-weight:500;">{{ $contact->contact_name }}</td>
                    <td><div style="font-size:13px;">{{ $contact->phone }}</div><div style="font-size:12px;color:#9ca3af;">{{ $contact->email }}</div></td>
                    <td><span style="font-size:12px;background:#f3f4f6;padding:2px 8px;border-radius:4px;text-transform:capitalize;">{{ $contact->type ?? '—' }}</span></td>
                    <td><span class="gh-badge {{ match($contact->status) { 'open'=>'gh-badge-warning','done'=>'gh-badge-success',default=>'gh-badge-muted' } }}">{{ ucfirst($contact->status) }}</span></td>
                    <td style="font-size:13px;color:#6b7280;">{{ $contact->follow_up_date?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <a href="{{ route('contact-diary.edit', $contact) }}" class="gh-btn gh-btn-outline gh-btn-sm">Edit</a>
                        <form method="POST" action="{{ route('contact-diary.destroy', $contact) }}" style="display:inline;" onsubmit="return confirm('Delete?');">
                            @csrf @method('DELETE')
                            <button class="gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            <div style="padding:16px;">{{ $contacts->links() }}</div>
        </div>
        @endif
    </div>
</x-layouts.app>