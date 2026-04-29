<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sign Up - ' . config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --su-bg: #f4efe7;
            --su-surface: rgba(255, 255, 255, 0.9);
            --su-surface-strong: #ffffff;
            --su-border: rgba(24, 33, 27, 0.1);
            --su-text: #18211b;
            --su-muted: #69756c;
            --su-accent: #0d9a73;
            --su-accent-strong: #087455;
            --su-accent-soft: #daf5eb;
            --su-shadow: 0 24px 60px rgba(53, 44, 27, 0.12);
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { background: var(--su-bg); }
        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--su-text);
            background: linear-gradient(135deg, #0abf8e 0%, #0891b2 100%);

        }

        .su-page { min-height: 100vh; display: flex; flex-direction: column; }
        .su-header {
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 28px;
            background: rgba(250, 246, 239, 0.82);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(24, 33, 27, 0.06);
        }
        .su-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            min-width: 160px;
        }
        .su-logo-icon { display: flex; align-items: center; }
        .su-logo-text {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--su-text);
        }
        .su-logo-text em { font-style: normal; color: var(--su-accent); }
        .su-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid rgba(24, 33, 27, 0.08);
            color: var(--su-muted);
            font-size: 12px;
            font-weight: 700;
        }
        .su-header-badge strong { color: var(--su-text); }

        .su-body {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 28px 20px 42px;
        }
        .su-shell {
            width: 100%;
            max-width: 1280px;
            display: grid;
            grid-template-columns: minmax(320px, 0.92fr) minmax(540px, 1.08fr);
            gap: 32px;
            align-items: start;
        }
        .su-sidebar {
            position: sticky;
            top: 92px;
            align-self: start;
        }
        .su-sidebar-card {
            overflow: hidden;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.42);
            background:
                linear-gradient(160deg, rgba(16, 39, 30, 0.97), rgba(18, 31, 27, 0.94) 62%, rgba(91, 68, 33, 0.92));
            box-shadow: var(--su-shadow);
            color: #f8fbf8;
            padding: 28px 24px;
        }
        .su-side-kicker {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.09);
            color: #c7f3e6;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .su-side-title {
            margin: 18px 0 8px;
            font-size: 30px;
            line-height: 1.06;
            letter-spacing: -0.04em;
            font-weight: 800;
        }
        .su-side-copy {
            margin: 0 0 24px;
            color: rgba(239, 247, 241, 0.78);
            font-size: 14px;
            line-height: 1.65;
        }
        .su-side-list { display: flex; flex-direction: column; gap: 14px; }
        .su-side-step {
            position: relative;
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
            padding: 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid transparent;
        }
        .su-side-step.active {
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(199, 243, 230, 0.34);
        }
        .su-side-step.done { background: rgba(7, 116, 85, 0.24); }
        .su-side-step::after {
            content: '';
            position: absolute;
            left: 35px;
            top: calc(100% + 2px);
            width: 2px;
            height: 14px;
            background: rgba(255, 255, 255, 0.12);
        }
        .su-side-step:last-child::after { display: none; }

        .su-step-num {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            transition: all 0.2s;
            background: rgba(255, 255, 255, 0.08);
            color: rgba(248, 251, 248, 0.75);
        }
        .su-step-num.active { background: #ffffff; color: #0a6b50; }
        .su-step-num.done { background: var(--su-accent); color: #ffffff; }
        .su-step-num.pending { background: rgba(255, 255, 255, 0.08); color: rgba(248, 251, 248, 0.75); }
        .su-side-step-meta { display: flex; flex-direction: column; gap: 4px; padding-top: 3px; }
        .su-step-index {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(239, 247, 241, 0.56);
        }
        .su-step-lbl { font-size: 15px; font-weight: 700; line-height: 1.3; }
        .su-step-lbl.active, .su-step-lbl.done { color: #ffffff; }
        .su-step-lbl.pending { color: rgba(239, 247, 241, 0.74); }
        .su-step-note {
            font-size: 12px;
            line-height: 1.55;
            color: rgba(239, 247, 241, 0.58);
        }
        .su-side-footer {
            margin-top: 24px;
            padding: 16px 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.07);
            display: grid;
            gap: 8px;
        }
        .su-side-footer strong { font-size: 14px; }
        .su-side-footer span {
            font-size: 13px;
            line-height: 1.55;
            color: rgba(239, 247, 241, 0.72);
        }

        .su-main {
            min-width: 0;
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        .su-mobile-steps {
            display: none;
            margin-bottom: 16px;
            padding: 16px;
            border-radius: 22px;
            border: 1px solid rgba(24, 33, 27, 0.08);
            background: rgba(255, 255, 255, 0.78);
            box-shadow: 0 12px 30px rgba(53, 44, 27, 0.08);
        }
        .su-mobile-steps-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }
        .su-mobile-title {
            font-size: 16px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }
        .su-mobile-copy { font-size: 12px; color: var(--su-muted); }
        .su-mobile-count {
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--su-accent-soft);
            color: var(--su-accent-strong);
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }
        .su-mobile-strip {
            display: grid;
            grid-template-columns: repeat(6, minmax(64px, 1fr));
            gap: 8px;
            overflow-x: auto;
        }
        .su-mobile-step {
            min-width: 64px;
            padding: 10px 8px;
            border-radius: 16px;
            border: 1px solid rgba(24, 33, 27, 0.08);
            background: #fff;
            text-align: center;
        }
        .su-mobile-step.active {
            background: linear-gradient(180deg, #f1fbf7, #e0f6ed);
            border-color: rgba(13, 154, 115, 0.34);
        }
        .su-mobile-step.done {
            background: rgba(218, 245, 235, 0.72);
            border-color: rgba(13, 154, 115, 0.24);
        }
        .su-mobile-step-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            margin-bottom: 7px;
            border-radius: 10px;
            background: #f2f4f1;
            color: var(--su-muted);
            font-size: 12px;
            font-weight: 800;
        }
        .su-mobile-step.active .su-mobile-step-num,
        .su-mobile-step.done .su-mobile-step-num {
            background: var(--su-accent);
            color: #fff;
        }
        .su-mobile-step-label {
            display: block;
            font-size: 11px;
            line-height: 1.25;
            color: var(--su-muted);
            font-weight: 700;
        }
        .su-mobile-step.active .su-mobile-step-label,
        .su-mobile-step.done .su-mobile-step-label { color: var(--su-text); }

        .su-card,
        .signup-card {
            width: 100%;
            max-width: none;
            padding: 34px 32px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            background: var(--su-surface);
            box-shadow: var(--su-shadow);
            backdrop-filter: blur(12px);
        }
        .su-card.wide,
        .signup-card.wide,
        .su-card.narrow { max-width: none; }
        .su-card-title,
        .signup-card-title {
            font-size: 28px;
            font-weight: 800;
            color: var(--su-text);
            margin: 0 0 6px;
            letter-spacing: -0.04em;
            line-height: 1.1;
        }
        .su-card-sub,
        .signup-card-subtitle {
            font-size: 14px;
            color: var(--su-muted);
            margin: 0 0 24px;
            line-height: 1.6;
            margin-bottom: 3rem
        }

        .su-fg,
        .su-form-group { margin-bottom: 16px; }
        .su-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #2c3831;
            margin-bottom: 7px;
        }
        .su-input-wrap { position: relative; }
        .su-input {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid rgba(24, 33, 27, 0.12);
            border-radius: 14px;
            font-size: 14px;
            color: var(--su-text);
            background: rgba(255, 255, 255, 0.92);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
            font-family: 'Manrope', sans-serif;
        }
        .su-input.no-icon { padding-left: 14px; }
        .su-input:focus {
            border-color: rgba(13, 154, 115, 0.72);
            box-shadow: 0 0 0 4px rgba(13, 154, 115, 0.12);
            background: #fff;
        }
        .su-icon,
        .su-input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #8a958d;
            pointer-events: none;
            display: flex;
            align-items: center;
        }
        .su-eye,
        .su-input-right {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #8a958d;
            display: flex;
            align-items: center;
            padding: 0;
        }
        .su-phone-wrap,
        .phone-wrap {
            display: flex;
            align-items: stretch;
            border: 1.5px solid rgba(24, 33, 27, 0.12);
            border-radius: 14px;
            overflow: hidden;
            transition: border-color 0.15s, box-shadow 0.15s;
            background: rgba(255, 255, 255, 0.92);
        }
        .su-phone-wrap:focus-within,
        .phone-wrap:focus-within {
            border-color: rgba(13, 154, 115, 0.72);
            box-shadow: 0 0 0 4px rgba(13, 154, 115, 0.12);
            background: #fff;
        }
        .su-phone-pfx,
        .phone-prefix {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 0 12px;
            border-right: 1.5px solid rgba(24, 33, 27, 0.09);
            font-size: 13px;
            font-weight: 700;
            color: #324138;
            background: #f7faf6;
            white-space: nowrap;
            user-select: none;
        }
        .su-phone-in,
        .phone-input {
            flex: 1;
            border: none;
            outline: none;
            padding: 11px 14px;
            font-size: 14px;
            color: var(--su-text);
            background: transparent;
            font-family: 'Manrope', sans-serif;
        }
        .su-error { font-size: 12px; color: #ef4444; margin-top: 4px; }
        .su-alert,
        .su-alert-error {
            border-radius: 16px;
            padding: 13px 15px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .su-alert.danger,
        .su-alert-error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .su-alert.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .su-alert.info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }
        .su-alert.warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }

        .su-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: all 0.15s;
            font-family: 'Manrope', sans-serif;
            text-decoration: none;
        }
        .su-btn-primary {
            background: linear-gradient(135deg, var(--su-accent), var(--su-accent-strong));
            color: #fff;
            width: 100%;
            box-shadow: 0 14px 26px rgba(8, 116, 85, 0.22);
        }
        .su-btn-primary:hover { filter: brightness(0.97); transform: translateY(-1px); }
        .su-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .su-btn-ghost {
            background: transparent;
            color: var(--su-muted);
            border: none;
            padding: 13px 4px;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-family: 'Manrope', sans-serif;
        }
        .su-btn-ghost:hover { color: var(--su-text); }
        .su-row-btns { display: flex; align-items: center; gap: 12px; margin-top: 24px; }
        .su-row-btns .su-btn-primary { flex: 1; }

        .su-chk-wrap,
        .su-checkbox-wrap {
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .su-chk,
        .su-checkbox {
            width: 16px;
            height: 16px;
            accent-color: var(--su-accent);
            cursor: pointer;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .su-chk-lbl,
        .su-checkbox-label {
            font-size: 13px;
            color: #374151;
            line-height: 1.55;
        }
        .su-link { color: var(--su-accent-strong); text-decoration: none; font-weight: 700; }
        .su-link:hover { text-decoration: underline; }

        .su-footer-txt,
        .su-footer-text {
            text-align: center;
            font-size: 13px;
            color: #7a857c;
            margin-top: 20px;
            line-height: 1.6;
        }
        .su-badges,
        .su-trust-badges {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(24, 33, 27, 0.08);
        }
        .su-badge,
        .su-trust-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(250, 251, 249, 0.92);
            border: 1px solid rgba(24, 33, 27, 0.06);
            font-size: 12px;
            line-height: 1.5;
            color: #5f6b62;
        }

        .su-otp-row { 
            display: flex; 
            gap: 8px; 
            justify-content: center; 
            margin: 18px 0 0; 
        }

        @media (max-width: 640px) {
            .su-otp-row { gap: 3px; }   
        }
        .su-otp-box {
            width: 48px;
            height: 52px;
            border: 1.5px solid #e5e7eb;
            border-radius: 14px;
            font-size: 20px;
            font-weight: 700;
            text-align: center;
            color: #111827;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            font-family: 'Manrope', sans-serif;
        }
        .su-otp-box:focus {
            border-color: rgba(13, 154, 115, 0.72);
            box-shadow: 0 0 0 4px rgba(13, 154, 115, 0.12);
        }

        .su-vp {
            border: 1.5px solid #e5e7eb;
            border-radius: 18px;
            padding: 18px 20px;
            margin-bottom: 14px;
            background: rgba(255, 255, 255, 0.82);
        }
        .su-vp.verified { border-color: #86efac; background: #f0fdf4; }
        .su-vp-hd { display: flex; align-items: center; gap: 12px; }
        .su-vp-icon { width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .su-vp-icon.green { background: #dcfce7; color: #16a34a; }
        .su-vp-icon.gray { background: #f3f4f6; color: #6b7280; }
        .su-vp-lbl { flex: 1; }
        .su-vp-lbl strong { font-size: 14px; font-weight: 600; color: #111827; display: block; }
        .su-vp-lbl span { font-size: 12px; color: #6b7280; }
        .su-vp-badge { font-size: 12px; font-weight: 600; color: #16a34a; }
        .su-vp-body { padding-top: 14px; }
        .su-otp-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 10px; }
        .su-otp-btn {
            padding: 9px 22px;
            background: var(--su-accent);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
        }
        .su-otp-btn:disabled { opacity: 0.55; cursor: not-allowed; }
        .su-otp-error { font-size: 12px; color: #ef4444; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
        .su-vhdr-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #d1fdf0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: var(--su-accent);
        }
        .su-vp-muted { opacity: 0.55; pointer-events: none; }
        .su-resend-btn {
            background: none;
            border: none;
            font-family: 'Manrope', sans-serif;
            cursor: pointer;
            font-size: 12px;
            padding: 4px 0;
            transition: color 0.15s;
        }
        .su-resend-wait { color: #9ca3af; cursor: not-allowed; }
        .su-resend-active { color: var(--su-accent); font-weight: 700; }
        .su-btn-inactive { opacity: 0.38; cursor: not-allowed; pointer-events: none; }

        .su-type {
            border: 1.5px solid #e5e7eb;
            border-radius: 18px;
            padding: 20px;
            margin-bottom: 14px;
            cursor: pointer;
            transition: border-color 0.15s;
            position: relative;
            background: rgba(255, 255, 255, 0.82);
        }
        .su-type:hover { border-color: rgba(13, 154, 115, 0.34); }
        .su-type:has(input[name="start_type"]:checked) { border-color: var(--su-accent); }
        .su-type-hd { display: flex; align-items: flex-start; gap: 14px; }
        .su-type-ico { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .su-type-ico.green { background: #dcfce7; }
        .su-type-ico.gray { background: #f1f5f9; }
        .su-type-body { flex: 1; }
        .su-type-title { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .su-type-desc { font-size: 13px; color: #6b7280; margin-bottom: 4px; }
        .su-type-green { font-size: 13px; font-weight: 600; color: #16a34a; margin-bottom: 10px; }
        .su-type-radio { flex-shrink: 0; margin-top: 2px; accent-color: var(--su-accent); width: 18px; height: 18px; cursor: pointer; }
        .su-badge-g { position: absolute; top: -10px; left: 16px; background: #16a34a; color: #fff; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; }
        .su-badge-p { position: absolute; top: -10px; left: 16px; background: var(--su-accent-strong); color: #fff; font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 20px; }
        .su-billing-toggle { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 14px; }
        .su-billing-opt { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #374151; cursor: pointer; user-select: none; }
        .su-billing-save { font-size: 11px; background: #dcfce7; color: #166534; padding: 1px 7px; border-radius: 20px; font-weight: 700; }

        .su-plan {
            position: relative;
            border-left: solid 1px transparent;
        }
        .su-plan:hover { border-color: rgba(13, 154, 115, 0.34); }
        .su-plan:has(input[type="radio"]:checked) {
            border-color: var(--su-accent);
            border-width: 2px;
            box-shadow: 0 8px 18px rgba(13, 154, 115, 0.08);
        }
        .su-plan-hd {     
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            border-left: solid 2px #29ab59;
            border-radius: 20px;
            padding: 5px 15px;
            line-height: 2;
        }
        .su-plan-info { flex: 1; }
        .su-plan-name-price { display: flex; align-items: baseline; gap: 8px; margin-bottom: 2px; }
        .su-plan-name { font-size: 16px; font-weight: 700; color: #111827; }
        .su-plan-price { font-size: 16px; font-weight: 700; color: #111827; }
        .su-plan-unit { font-size: 13px; color: #6b7280; font-weight: 400; }
        .su-plan-free { font-size: 13px; font-weight: 600; color: #16a34a; margin-bottom: 2px; }
        .su-plan-desc { font-size: 13px; color: #6b7280; margin-bottom: 10px; }
        .su-plan-pop { 
            position: absolute;
            top: 15px;
            right: 16px;
            left: 20px;
            background: var(--su-accent-strong);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            letter-spacing: 0.04em;
            width: fit-content;
         }

        .su-addon-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin: 16px 0; }
        .su-addon {
            border: 1.5px solid #e5e7eb;
            border-radius: 16px;
            padding: 14px 12px 12px;
            cursor: pointer;
            transition: all 0.15s;
            position: relative;
            background: rgba(255, 255, 255, 0.8);
        }
        .su-addon:hover { border-color: rgba(13, 154, 115, 0.34); }
        .su-addon.checked { border-color: var(--su-accent); background: #f2fbf7; }
        .su-addon-chk { position: absolute; top: 10px; right: 10px; width: 16px; height: 16px; accent-color: var(--su-accent); cursor: pointer; }
        .su-addon-icon { width: 36px; height: 36px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 8px; }
        .su-addon-name { font-size: 13px; font-weight: 600; color: #111827; margin-bottom: 6px; line-height: 1.3; }
        .su-addon-price { font-size: 13px; font-weight: 700; color: var(--su-accent-strong); }
        .su-addon-cycle { font-size: 11px; color: #9ca3af; }

        .su-sel {
            border: 1.5px solid #e5e7eb;
            border-radius: 18px;
            padding: 16px 18px;
            margin-top: 8px;
            background: linear-gradient(180deg, rgba(249, 251, 249, 0.96), rgba(243, 248, 245, 0.96));
        }
        .su-sel-title { font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 12px; }
        .su-sel-row { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; }
        .su-sel-l { font-size: 13px; color: #374151; }
        .su-sel-v { font-size: 13px; font-weight: 600; color: #111827; }
        .su-sel-total { display: flex; justify-content: space-between; align-items: center; padding: 10px 0 0; border-top: 1px solid #f3f4f6; margin-top: 8px; }
        .su-sel-tl { font-size: 14px; font-weight: 700; color: #111827; }
        .su-sel-tv { font-size: 14px; font-weight: 800; color: #111827; }

        .su-confirm-box {
            border: 1.5px solid #e5e7eb;
            border-radius: 18px;
            overflow: hidden;
            margin: 20px 0;
            background: rgba(255, 255, 255, 0.82);
        }
        .su-confirm-box-hd { background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-bottom: 1px solid #bbf7d0; padding: 14px 20px; display: flex; align-items: center; gap: 8px; }
        .su-confirm-box-hd span { font-size: 14px; font-weight: 700; color: #15803d; }
        .su-confirm-paid-hd { background: linear-gradient(135deg, #eff6ff, #dbeafe); border-bottom: 1px solid #bfdbfe; padding: 14px 20px; display: flex; align-items: center; gap: 8px; }
        .su-confirm-paid-hd span { font-size: 14px; font-weight: 700; color: #1d4ed8; }
        .su-confirm-row { display: flex; align-items: center; justify-content: space-between; padding: 13px 20px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .su-confirm-row:last-child { border-bottom: none; }
        .su-confirm-total-row { background: #f9fafb; border-top: 1.5px solid #e5e7eb !important; }
        .su-confirm-lbl { color: #6b7280; }
        .su-confirm-val { font-weight: 600; color: #111827; }

        @media (max-width: 1024px) {
            .su-shell { grid-template-columns: 1fr; }
            .su-sidebar { display: none; }
            .su-mobile-steps { display: block; }
        }

        @media (max-width: 640px) {
            .su-header { padding: 14px 16px; }
            .su-header-badge { font-size: 11px; padding: 8px 10px; }
            .su-body { padding: 18px 12px 28px; }
            .su-card,
            .signup-card {
                padding: 24px 18px;
                border-radius: 22px;
            }
            .su-card-title,
            .signup-card-title { font-size: 24px; }
            .su-row-btns { flex-direction: column-reverse; align-items: stretch; }
            .su-row-btns .su-btn-ghost { justify-content: center; padding: 10px 4px 2px; }
            .su-addon-grid { grid-template-columns: repeat(2, 1fr); }
            .su-badges,
            .su-trust-badges { grid-template-columns: 1fr; }
            .su-mobile-steps { padding: 14px; border-radius: 18px; }
            .su-mobile-strip { grid-template-columns: repeat(6, minmax(58px, 72px)); }
        }
    </style>
</head>
<body>
<div class="su-page">
    @php
        $steps = [
            1 => ['label' => 'Account', 'note' => 'Create your owner profile and verify contact details.'],
            2 => ['label' => 'Your Gym', 'note' => 'Add the business details we need to configure the workspace.'],
            3 => ['label' => 'Start Type', 'note' => 'Choose whether you want a free trial or paid start.'],
            4 => ['label' => 'Choose Plan', 'note' => 'Pick the package that matches your gym size and goals.'],
            5 => ['label' => 'Enhance', 'note' => 'Add optional modules without changing your main plan.'],
            6 => ['label' => 'Confirm', 'note' => 'Review everything once before launching the account.'],
        ];
        $cur = $currentStep
            ?? match ($step ?? null) {
                'account', 'verify' => 1,
                'gym' => 2,
                'start-type' => 3,
                'plan' => 4,
                'enhance' => 5,
                'confirm' => 6,
                default => 1,
            };
    @endphp

    <div class="su-header">
        <a href="{{ route('login') }}" class="su-logo">
            <div class="su-logo-icon">
                <svg width="34" height="34" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="8" fill="#0abf8e"></rect>
                    <path d="M8 20V14l8-6 8 6v6" stroke="#fff" stroke-width="2" stroke-linejoin="round"></path>
                    <rect x="13" y="18" width="6" height="6" rx="1" fill="#fff"></rect>
                </svg>
            </div>
            <span class="su-logo-text">Gym<em>Hub</em></span>
        </a>

        <div class="su-header-badge">
            <span>Setup Flow</span>
            <strong>Step {{ $cur }} of {{ count($steps) }}</strong>
        </div>
    </div>

    <div class="su-body">
        <div class="su-shell">
            <aside class="su-sidebar">
                <div class="su-sidebar-card">
                    <span class="su-side-kicker">Gym Onboarding</span>
                    <h1 class="su-side-title">Build your gym setup with a clearer flow.</h1>
                    <p class="su-side-copy">
                        The signup steps stay exactly the same, but the journey is easier to scan now: progress on the left for desktop, quick status up top on mobile.
                    </p>

                    <div class="su-side-list">
                        @foreach($steps as $num => $step)
                            @php $s = $num < $cur ? 'done' : ($num === $cur ? 'active' : 'pending'); @endphp
                            <div class="su-side-step {{ $s }}">
                                <div class="su-step-num {{ $s }}">
                                    @if($s === 'done')
                                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        {{ $num }}
                                    @endif
                                </div>
                                <div class="su-side-step-meta">
                                    <span class="su-step-index">Step {{ $num }}</span>
                                    <span class="su-step-lbl {{ $s }}">{{ $step['label'] }}</span>
                                    <span class="su-step-note">{{ $step['note'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="su-side-footer">
                        <strong>Everything important stays intact.</strong>
                        <span>Your forms, routes, and step sequence are preserved. This update only improves how the experience looks and responds.</span>
                    </div>
                </div>
            </aside>

            <main class="su-main">
                <div class="su-mobile-steps">
                    <div class="su-mobile-steps-top">
                        <div>
                            <div class="su-mobile-title">{{ $steps[$cur]['label'] }}</div>
                            <div class="su-mobile-copy">{{ $steps[$cur]['note'] }}</div>
                        </div>
                        <div class="su-mobile-count">Step {{ $cur }}/{{ count($steps) }}</div>
                    </div>

                    <div class="su-mobile-strip">
                        @foreach($steps as $num => $step)
                            @php $s = $num < $cur ? 'done' : ($num === $cur ? 'active' : 'pending'); @endphp
                            <div class="su-mobile-step {{ $s }}">
                                <span class="su-mobile-step-num">
                                    @if($s === 'done')
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        {{ $num }}
                                    @endif
                                </span>
                                <span class="su-mobile-step-label">{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @yield('content')
            </main>
        </div>
    </div>
</div>

<script>
window.otpBox = function(action) {
    return {
        digits: ['', '', '', '', '', ''],
        resendSec: 25,
        localError: '',
        verifying: false,
        _timer: null,

        init() {
            this.startTimer();
            this.$nextTick(() => this.$refs.b0 && this.$refs.b0.focus());
        },

        get code() { return this.digits.join(''); },
        get complete() { return this.code.length === 6 && !this.digits.includes(''); },

        startTimer() {
            clearInterval(this._timer);
            this.resendSec = 25;
            this._timer = setInterval(() => {
                if (this.resendSec > 0) this.resendSec--;
                else clearInterval(this._timer);
            }, 1000);
        },

        restartTimer() {
            if (this.resendSec === 0) this.startTimer();
        },

        onKeydown(e, idx) {
            if (/^[0-9]$/.test(e.key)) {
                e.preventDefault();
                this.digits[idx] = e.key;
                e.target.value = e.key;
                if (idx < 5) {
                    this.$nextTick(() => {
                        var next = this.$refs['b' + (idx + 1)];
                        if (next) {
                            next.focus();
                            next.select();
                        }
                    });
                }
            } else if (e.key === 'Backspace') {
                e.preventDefault();
                if (this.digits[idx]) {
                    this.digits[idx] = '';
                    e.target.value = '';
                } else if (idx > 0) {
                    this.digits[idx - 1] = '';
                    this.$nextTick(() => {
                        var prev = this.$refs['b' + (idx - 1)];
                        if (prev) {
                            prev.value = '';
                            prev.focus();
                        }
                    });
                }
            } else if (e.key === 'ArrowLeft' && idx > 0) {
                e.preventDefault();
                this.$refs['b' + (idx - 1)] && this.$refs['b' + (idx - 1)].focus();
            } else if (e.key === 'ArrowRight' && idx < 5) {
                e.preventDefault();
                this.$refs['b' + (idx + 1)] && this.$refs['b' + (idx + 1)].focus();
            }
        },

        onPaste(e) {
            var text = (e.clipboardData || window.clipboardData)
                .getData('text')
                .replace(/\D/g, '')
                .slice(0, 6);

            this.digits = Array.from({ length: 6 }, function(_, i) {
                return text[i] || '';
            });

            this.$nextTick(() => {
                for (var i = 0; i < 6; i++) {
                    var b = this.$refs['b' + i];
                    if (b) b.value = this.digits[i];
                }
                var focusIdx = Math.min(text.length, 5);
                this.$refs['b' + focusIdx] && this.$refs['b' + focusIdx].focus();
            });
        },

        async verify() {
            if (!this.complete) {
                this.localError = 'Please enter all 6 digits.';
                return;
            }

            this.localError = '';
            this.verifying = true;

            try {
                await this.$wire[action](this.code);
            } catch (err) {
                this.localError = 'Something went wrong. Please try again.';
            } finally {
                this.verifying = false;
            }
        }
    };
};
</script>
</body>
</html>
