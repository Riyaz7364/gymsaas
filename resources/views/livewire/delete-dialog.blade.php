<div>
    @if($isOpen)
    <div style="position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:9999;">
        <div style="background:#fff; border-radius:8px; padding:24px; width:100%; max-width:400px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);">
            <h3 style="margin-top:0; font-size:18px; font-weight:600; color:#111827;">{{ $title }}</h3>
            
            <p style="margin-top:12px; font-size:14px; color:#4b5563; line-height:1.5;">
                {{ $message }}
                @if($itemName)
                    <br><br><strong style="color:#111827;">{{ $itemName }}</strong>
                @endif
            </p>

            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" wire:click="close" class="gh-btn gh-btn-outline" style="cursor:pointer; border-radius:6px; padding:8px 16px;">{{ $cancelText }}</button>
                <form method="POST" action="{{ $formAction }}" style="margin:0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="gh-btn" style="background:#dc2626; color:#fff; border:none; cursor:pointer; border-radius:6px; padding:8px 16px;">{{ $confirmText }}</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>