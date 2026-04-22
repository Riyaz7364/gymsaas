<div class="su-card narrow">
    <div class="su-vhdr-icon">
        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
    </div>
    <h1 class="su-card-title" style="text-align:center; margin-bottom:4px;">Verify your identity</h1>
    <p class="su-card-sub" style="text-align:center; margin-bottom:24px;">
        We've sent verification codes to your email and WhatsApp
    </p>

    <div class="su-vp @if($emailVerified) verified @endif">
        <div class="su-vp-hd">
            <div class="su-vp-icon @if($emailVerified) green @else gray @endif">
                @if($emailVerified)
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                @endif
            </div>
            <div class="su-vp-lbl">
                <strong>Email Verification</strong>
                <span>{{ $email }}</span>
            </div>
            @if($emailVerified)
                <span class="su-vp-badge">Verified</span>
            @endif
        </div>

        @if(! $emailVerified)
            <div class="su-vp-body" wire:key="email-otp" x-data="otpBox('verifyEmail')">
                <p style="font-size:12px; color:#6b7280; margin:0 0 10px; line-height:1.5;">
                    Enter the demo code <code style="background:#f3f4f6; padding:2px 7px; border-radius:5px; font-size:11px; font-weight:700; color:#374151; letter-spacing:2px;">123456</code>
                </p>

                <div x-show="localError" x-text="localError" class="su-otp-error" style="margin-bottom:8px;"></div>
                @if($emailError)
                    <div class="su-otp-error" style="margin-bottom:8px;">{{ $emailError }}</div>
                @endif

                <div class="su-otp-row">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" autocomplete="one-time-code" x-ref="b{{ $i }}" @keydown="onKeydown($event, {{ $i }})" @paste.prevent="onPaste($event)" @focus="$el.select()" class="su-otp-box" maxlength="1">
                    @endfor
                </div>

                <div class="su-otp-footer" style="margin-top:14px;">
                    <button type="button" @click="restartTimer()" :disabled="resendSec > 0" class="su-resend-btn" :class="resendSec > 0 ? 'su-resend-wait' : 'su-resend-active'">
                        <span x-text="resendSec > 0 ? 'Resend in ' + resendSec + 's' : 'Resend Code'"></span>
                    </button>
                    <button type="button" class="su-otp-btn" @click="verify()" :disabled="verifying || !complete" :class="{ 'su-otp-btn-loading': verifying }">
                        <span x-show="!verifying">Verify Email</span>
                        <span x-show="verifying" style="display:none;">...</span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <div class="su-vp @if($phoneVerified) verified @elseif(!$emailVerified) su-vp-muted @endif">
        <div class="su-vp-hd">
            <div class="su-vp-icon @if($phoneVerified) green @else gray @endif">
                @if($phoneVerified)
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5.25h18M3 12h18M3 18.75h18"/></svg>
                @endif
            </div>
            <div class="su-vp-lbl">
                <strong>WhatsApp Verification</strong>
                <span>+91 {{ $phone }}</span>
            </div>
            @if($phoneVerified)
                <span class="su-vp-badge">Verified</span>
            @elseif(! $emailVerified)
                <span style="font-size:11px; color:#9ca3af;">Verify email first</span>
            @endif
        </div>

        @if(! $phoneVerified && $emailVerified)
            <div class="su-vp-body" wire:key="phone-otp" x-data="otpBox('verifyPhone')">
                <p style="font-size:12px; color:#6b7280; margin:0 0 10px; line-height:1.5;">
                    Enter the demo code <code style="background:#f3f4f6; padding:2px 7px; border-radius:5px; font-size:11px; font-weight:700; color:#374151; letter-spacing:2px;">123456</code>
                </p>

                <div x-show="localError" x-text="localError" class="su-otp-error" style="margin-bottom:8px;"></div>
                @if($phoneError)
                    <div class="su-otp-error" style="margin-bottom:8px;">{{ $phoneError }}</div>
                @endif

                <div class="su-otp-row">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text" inputmode="numeric" autocomplete="one-time-code" x-ref="b{{ $i }}" @keydown="onKeydown($event, {{ $i }})" @paste.prevent="onPaste($event)" @focus="$el.select()" class="su-otp-box" maxlength="1">
                    @endfor
                </div>

                <div class="su-otp-footer" style="margin-top:14px;">
                    <button type="button" @click="restartTimer()" :disabled="resendSec > 0" class="su-resend-btn" :class="resendSec > 0 ? 'su-resend-wait' : 'su-resend-active'">
                        <span x-text="resendSec > 0 ? 'Resend in ' + resendSec + 's' : 'Resend Code'"></span>
                    </button>
                    <button type="button" class="su-otp-btn" @click="verify()" :disabled="verifying || !complete" :class="{ 'su-otp-btn-loading': verifying }">
                        <span x-show="!verifying">Verify WhatsApp</span>
                        <span x-show="verifying" style="display:none;">...</span>
                    </button>
                </div>
            </div>
        @elseif(! $phoneVerified && ! $emailVerified)
            <div style="padding:10px 0 2px; display:flex; align-items:center; gap:6px; font-size:12px; color:#9ca3af;">
                Verify your email above to unlock this step
            </div>
        @endif
    </div>

    <div class="su-row-btns" style="margin-top:20px;">
        <button type="button" wire:click="goTo('account')" class="su-btn-ghost">Back</button>
        @if($phoneVerified)
            <button type="button" wire:click="goTo('gym')" class="su-btn su-btn-primary">Continue</button>
        @else
            <span class="su-btn su-btn-primary su-btn-inactive" aria-disabled="true">Continue</span>
        @endif
    </div>
</div>
