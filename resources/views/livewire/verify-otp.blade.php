<div class="su-card narrow">

    {{-- Shield icon --}}
    <div class="su-vhdr-icon">
        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
    </div>
    <h1 class="su-card-title" style="text-align:center; margin-bottom:4px;">Verify your identity</h1>
    <p class="su-card-sub" style="text-align:center; margin-bottom:24px;">
        We've sent verification codes to your email and WhatsApp
    </p>

    {{-- ══════════════════════════════ EMAIL PANEL ══════════════════════════════ --}}
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
                <span class="su-vp-badge">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="display:inline;vertical-align:-1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Verified
                </span>
            @endif
        </div>

        @if(! $emailVerified)
            <div class="su-vp-body"
                 wire:key="email-otp"
                 x-data="otpBox('verifyEmail')">

                <p style="font-size:12px; color:#6b7280; margin:0 0 10px; line-height:1.5;">
                    Enter the 6-digit code &nbsp;
                    <code style="background:#f3f4f6; padding:2px 7px; border-radius:5px; font-size:11px; font-weight:700; color:#374151; letter-spacing:2px;">123456</code>
                    &nbsp;for demo
                </p>

                {{-- Error messages --}}
                <div x-show="localError" x-text="localError" class="su-otp-error" style="margin-bottom:8px;"></div>
                @if($emailError)
                    <div class="su-otp-error" style="margin-bottom:8px;">{{ $emailError }}</div>
                @endif

                {{-- 6-digit OTP boxes --}}
                <div class="su-otp-row">
                    @for($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            x-ref="b{{ $i }}"
                            @keydown="onKeydown($event, {{ $i }})"
                            @paste.prevent="onPaste($event)"
                            @focus="$el.select()"
                            class="su-otp-box"
                            maxlength="1">
                    @endfor
                </div>

                <div class="su-otp-footer" style="margin-top:14px;">
                    <button
                        type="button"
                        @click="restartTimer()"
                        :disabled="resendSec > 0"
                        class="su-resend-btn"
                        :class="resendSec > 0 ? 'su-resend-wait' : 'su-resend-active'">
                        <span x-text="resendSec > 0 ? 'Resend in ' + resendSec + 's' : '↺ Resend Code'"></span>
                    </button>
                    <button
                        type="button"
                        class="su-otp-btn"
                        @click="verify()"
                        :disabled="verifying || !complete"
                        :class="{ 'su-otp-btn-loading': verifying }">
                        <span x-show="!verifying">Verify Email</span>
                        <span x-show="verifying" style="display:none;">
                            <svg class="su-spin" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════ WHATSAPP PANEL ═══════════════════════════ --}}
    <div class="su-vp @if($phoneVerified) verified @elseif(!$emailVerified) su-vp-muted @endif">
        <div class="su-vp-hd">
            <div class="su-vp-icon @if($phoneVerified) green @else gray @endif">
                @if($phoneVerified)
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                @else
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="color:#25D366;">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.532 5.85L.057 23.7a.75.75 0 00.918.928l5.974-1.56A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.75a9.716 9.716 0 01-4.95-1.355l-.355-.21-3.684.963.985-3.594-.232-.371A9.718 9.718 0 012.25 12C2.25 6.615 6.615 2.25 12 2.25S21.75 6.615 21.75 12 17.385 21.75 12 21.75z"/>
                    </svg>
                @endif
            </div>
            <div class="su-vp-lbl">
                <strong>WhatsApp Verification</strong>
                <span>{{ $phone }}</span>
            </div>
            @if($phoneVerified)
                <span class="su-vp-badge">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" style="display:inline;vertical-align:-1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Verified
                </span>
            @elseif(! $emailVerified)
                <span style="font-size:11px; color:#9ca3af;">Verify email first</span>
            @endif
        </div>

        @if(! $phoneVerified && $emailVerified)
            <div class="su-vp-body"
                 wire:key="phone-otp-active"
                 x-data="otpBox('verifyPhone')">

                <p style="font-size:12px; color:#6b7280; margin:0 0 10px; line-height:1.5;">
                    Enter the 6-digit code &nbsp;
                    <code style="background:#f3f4f6; padding:2px 7px; border-radius:5px; font-size:11px; font-weight:700; color:#374151; letter-spacing:2px;">123456</code>
                    &nbsp;for demo
                </p>

                <div x-show="localError" x-text="localError" class="su-otp-error" style="margin-bottom:8px;"></div>
                @if($phoneError)
                    <div class="su-otp-error" style="margin-bottom:8px;">{{ $phoneError }}</div>
                @endif

                <div class="su-otp-row">
                    @for($i = 0; $i < 6; $i++)
                        <input
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            x-ref="b{{ $i }}"
                            @keydown="onKeydown($event, {{ $i }})"
                            @paste.prevent="onPaste($event)"
                            @focus="$el.select()"
                            class="su-otp-box"
                            maxlength="1">
                    @endfor
                </div>

                <div class="su-otp-footer" style="margin-top:14px;">
                    <button
                        type="button"
                        @click="restartTimer()"
                        :disabled="resendSec > 0"
                        class="su-resend-btn"
                        :class="resendSec > 0 ? 'su-resend-wait' : 'su-resend-active'">
                        <span x-text="resendSec > 0 ? 'Resend in ' + resendSec + 's' : '↺ Resend Code'"></span>
                    </button>
                    <button
                        type="button"
                        class="su-otp-btn"
                        @click="verify()"
                        :disabled="verifying || !complete"
                        :class="{ 'su-otp-btn-loading': verifying }">
                        <span x-show="!verifying">Verify WhatsApp</span>
                        <span x-show="verifying" style="display:none;">
                            <svg class="su-spin" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        @elseif(! $phoneVerified && ! $emailVerified)
            <div style="padding:10px 0 2px; display:flex; align-items:center; gap:6px; font-size:12px; color:#9ca3af;">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                Verify your email above to unlock this step
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════ BUTTONS ══════════════════════════════════ --}}
    <div class="su-row-btns" style="margin-top:20px;">
        <a href="{{ route('signup.step1') }}" class="su-btn-ghost">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back
        </a>

        @if($phoneVerified)
            <a href="{{ route('signup.step2') }}" class="su-btn su-btn-primary">
                Continue
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </a>
        @else
            <span class="su-btn su-btn-primary su-btn-inactive" aria-disabled="true">
                Continue
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
            </span>
        @endif
    </div>

</div>
