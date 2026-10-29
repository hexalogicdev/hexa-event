@extends('layouts.app')

@section('title', 'Team Login')

@section('page-style')
    <style>
        /* =========================================================
       EVENTOPS AUTHENTICATION — PREMIUM LOGIN EXPERIENCE
       ========================================================= */

        .ep-auth-page {
            --ep-ink: #15152f;
            --ep-muted: #777b96;
            --ep-primary: #6254f5;
            --ep-primary-dark: #4b3fe0;
            --ep-border: #e9eaf2;
            --ep-surface: #ffffff;
            --ep-background: #f7f8fc;

            min-height: 100vh;
            min-height: 100dvh;
            padding: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(ellipse at 10% 0%, rgba(98, 84, 245, .07), transparent 35%),
                var(--ep-background);
            color: var(--ep-ink);
            font-family: inherit;
        }

        .ep-auth-page *,
        .ep-auth-page *::before,
        .ep-auth-page *::after {
            box-sizing: border-box;
        }

        .ep-auth-shell {
            width: 100%;
            max-width: 1280px;
            min-height: 760px;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, .95fr);
            overflow: hidden;
            background: var(--ep-surface);
            border: 1px solid rgba(226, 228, 240, .9);
            border-radius: 28px;
            box-shadow:
                0 30px 90px rgba(31, 31, 73, .09),
                0 4px 15px rgba(31, 31, 73, .025);
        }

        /* LEFT BRAND PANEL */

        .ep-auth-visual {
            position: relative;
            isolation: isolate;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 0;
            padding: 40px 46px 30px;
            overflow: hidden;
            color: #fff;
            background:
                radial-gradient(ellipse at 85% 12%, rgba(128, 104, 255, .32), transparent 36%),
                radial-gradient(ellipse at 0% 100%, rgba(49, 75, 210, .26), transparent 40%),
                linear-gradient(145deg, #17172f 0%, #1d1c43 52%, #29205b 100%);
        }

        .ep-auth-visual::before {
            position: absolute;
            z-index: -1;
            top: 18%;
            right: -130px;
            width: 420px;
            height: 420px;
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 50%;
            content: "";
        }

        .ep-auth-visual::after {
            position: absolute;
            z-index: -1;
            top: 23%;
            right: -80px;
            width: 320px;
            height: 320px;
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 50%;
            content: "";
        }

        .ep-auth-visual-grid {
            position: absolute;
            z-index: -1;
            inset: 0;
            pointer-events: none;
            opacity: .15;
            background-image:
                linear-gradient(rgba(255, 255, 255, .07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .07) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: linear-gradient(to bottom, black, transparent 82%);
        }

        .ep-auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--ep-ink);
            text-decoration: none;
        }

        .ep-auth-brand__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 43px;
            height: 43px;
            flex-shrink: 0;
            border-radius: 13px;
            color: #fff;
            background: linear-gradient(145deg, #8174ff, #5949eb);
            box-shadow: 0 7px 18px rgba(98, 84, 245, .25);
            font-size: 23px;
        }

        .ep-auth-brand__name {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -.8px;
        }

        .ep-auth-visual .ep-auth-brand {
            color: #fff;
        }

        .ep-auth-visual .ep-auth-brand__icon {
            color: #fff;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .13);
            box-shadow: none;
        }

        .ep-auth-visual__content {
            position: relative;
            z-index: 1;
            max-width: 490px;
            margin: 55px 0 38px;
        }

        .ep-auth-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 8px 12px;
            margin-bottom: 25px;
            border: 1px solid rgba(180, 172, 255, .22);
            border-radius: 100px;
            color: #d5d0ff;
            background: rgba(255, 255, 255, .055);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .ep-auth-eyebrow i {
            color: #a99cff;
            font-size: 16px;
        }

        .ep-auth-visual h1 {
            max-width: 480px;
            margin: 0 0 19px;
            color: #fff;
            font-size: clamp(35px, 3.6vw, 51px);
            font-weight: 800;
            line-height: 1.13;
            letter-spacing: -2.2px;
        }

        .ep-auth-visual h1 span {
            color: #a99cff;
        }

        .ep-auth-visual__description {
            max-width: 420px;
            margin: 0;
            color: #b8bad3;
            font-size: 15px;
            line-height: 1.9;
        }

        .ep-auth-benefits {
            display: grid;
            gap: 15px;
            margin-top: 30px;
        }

        .ep-auth-benefit {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #e5e4f5;
            font-size: 13px;
            font-weight: 500;
        }

        .ep-auth-benefit__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 25px;
            height: 25px;
            flex-shrink: 0;
            border: 1px solid rgba(151, 255, 214, .2);
            border-radius: 8px;
            color: #83edc4;
            background: rgba(88, 224, 171, .09);
            font-size: 15px;
        }

        /* DECORATIVE EVENT DASHBOARD PREVIEW */

        .ep-auth-preview {
            position: relative;
            z-index: 1;
            max-width: 440px;
            padding: 20px;
            margin: 0 0 30px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 18px;
            background: rgba(255, 255, 255, .075);
            box-shadow: 0 20px 55px rgba(0, 0, 0, .12);
            backdrop-filter: blur(14px);
        }

        .ep-auth-preview__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
        }

        .ep-auth-preview__label {
            color: #e8e6ff;
            font-size: 12px;
            font-weight: 700;
        }

        .ep-auth-preview__status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 9px;
            border-radius: 7px;
            color: #92f1c7;
            background: rgba(89, 226, 163, .1);
            font-size: 10px;
            font-weight: 700;
        }

        .ep-auth-preview__status span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #74e9b0;
        }

        .ep-auth-preview__stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .ep-auth-preview__stat {
            min-width: 0;
            padding: 13px 10px;
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 12px;
            background: rgba(255, 255, 255, .045);
        }

        .ep-auth-preview__stat-label {
            display: block;
            margin-bottom: 8px;
            color: #b9bad6;
            font-size: 10px;
        }

        .ep-auth-preview__stat-value {
            display: block;
            color: #fff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.7px;
        }

        .ep-auth-preview__bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-top: 17px;
            margin-top: 17px;
            border-top: 1px solid rgba(255, 255, 255, .10);
        }

        .ep-auth-preview__bottom p {
            margin: 0;
            color: #c6c4df;
            font-size: 11px;
            line-height: 1.7;
        }

        .ep-auth-preview__avatars {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            padding-left: 5px;
        }

        .ep-auth-preview__avatar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            margin-left: -5px;
            border: 2px solid #29234f;
            border-radius: 50%;
            color: #fff;
            background: #7668e9;
            font-size: 9px;
            font-weight: 800;
        }

        .ep-auth-preview__avatar:nth-child(2) {
            background: #d17caa;
        }

        .ep-auth-preview__avatar:nth-child(3) {
            background: #4caaa1;
        }

        .ep-auth-preview__avatar:last-child {
            background: #edac65;
        }

        .ep-auth-visual__footer {
            position: relative;
            z-index: 1;
            padding-top: 21px;
            border-top: 1px solid rgba(255, 255, 255, .12);
            color: #a7a9c8;
            font-size: 11px;
            line-height: 1.8;
        }

        /* RIGHT LOGIN PANEL */

        .ep-auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
            padding: 54px clamp(28px, 5vw, 74px);
            background: #fff;
        }

        .ep-auth-form {
            width: 100%;
            max-width: 390px;
        }

        .ep-auth-mobile-brand {
            display: none;
            margin-bottom: 42px;
        }

        .ep-auth-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--ep-muted);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: color .2s ease;
        }

        .ep-auth-back:hover {
            color: var(--ep-primary);
        }

        .ep-auth-heading {
            margin-bottom: 33px;
        }

        .ep-auth-heading__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            margin-bottom: 25px;
            border: 1px solid #e9e6ff;
            border-radius: 15px;
            color: var(--ep-primary);
            background: #f4f2ff;
            font-size: 23px;
        }

        .ep-auth-heading h2 {
            margin: 0 0 11px;
            color: var(--ep-ink);
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1.3px;
            line-height: 1.2;
        }

        .ep-auth-heading p {
            margin: 0;
            color: var(--ep-muted);
            font-size: 13px;
            line-height: 1.8;
        }

        .ep-auth-form .alert {
            padding: 13px 15px;
            margin-bottom: 22px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.7;
        }

        .ep-auth-form .alert strong {
            display: block;
            margin-bottom: 2px;
        }

        .ep-auth-form .form-label {
            display: block;
            margin-bottom: 9px;
            color: #292b45;
            font-size: 12px;
            font-weight: 700;
        }

        .ep-auth-form .form-control,
        .ep-auth-form .input-group-text,
        .ep-auth-form .btn-outline-secondary {
            min-height: 51px;
            border-color: var(--ep-border);
            box-shadow: none;
        }

        .ep-auth-form .input-group {
            overflow: hidden;
            border: 1px solid var(--ep-border);
            border-radius: 12px;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .ep-auth-form .input-group:focus-within {
            border-color: var(--ep-primary);
            box-shadow: 0 0 0 4px rgba(98, 84, 245, .09);
        }

        .ep-auth-form .input-group .input-group-text,
        .ep-auth-form .input-group .form-control,
        .ep-auth-form .input-group .btn {
            border: 0;
            border-radius: 0;
        }

        .ep-auth-form .input-group-text {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 48px;
            padding: 0 13px;
            color: #9699b2;
            background: #fff;
            font-size: 18px;
        }

        .ep-auth-form .form-control {
            min-width: 0;
            padding: 12px 10px;
            color: #292b45;
            background: #fff;
            font-size: 12px;
        }

        .ep-auth-form .form-control::placeholder {
            color: #a8abc0;
            opacity: 1;
        }

        .ep-auth-form .form-control:focus {
            box-shadow: none;
        }

        .ep-auth-form .form-control.is-invalid {
            background-image: none;
        }

        .ep-auth-form .input-group .btn-outline-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 47px;
            padding: 0 12px;
            color: #8588a2;
            background: #fff;
            font-size: 18px;
        }

        .ep-auth-form .input-group .btn-outline-secondary:hover {
            color: var(--ep-primary);
            background: #f8f7ff;
        }

        .ep-auth-form .small {
            color: var(--ep-primary);
            font-size: 11px;
            font-weight: 700;
            text-decoration: none;
        }

        .ep-auth-form .small:hover {
            color: var(--ep-primary-dark);
            text-decoration: underline;
        }

        .ep-auth-form .form-check {
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: auto;
            padding-left: 0;
            margin: 0;
            cursor: pointer;
        }

        .ep-auth-form .form-check-input {
            float: none;
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin: 0;
            border-color: #d8daea;
            cursor: pointer;
        }

        .ep-auth-form .form-check-input:checked {
            border-color: var(--ep-primary);
            background-color: var(--ep-primary);
        }

        .ep-auth-form .form-check-label {
            color: #747791;
            font-size: 11px;
            cursor: pointer;
        }

        .ep-auth-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 53px;
            padding: 13px 18px;
            margin-top: 2px;
            border: 0;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(105deg, #6959f5 0%, #5545e9 100%);
            box-shadow: 0 8px 20px rgba(98, 84, 245, .22);
            font-size: 13px;
            font-weight: 700;
            transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
        }

        .ep-auth-submit:hover {
            color: #fff;
            background: linear-gradient(105deg, #5d4de9 0%, #4737d7 100%);
            box-shadow: 0 11px 24px rgba(98, 84, 245, .3);
            transform: translateY(-1px);
        }

        .ep-auth-submit:focus-visible {
            outline: 3px solid rgba(98, 84, 245, .3);
            outline-offset: 3px;
        }

        .ep-auth-submit:disabled {
            cursor: wait;
            opacity: .8;
            transform: none;
        }

        .ep-auth-help {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 15px;
            margin-top: 24px;
            border: 1px solid #eeeafa;
            border-radius: 12px;
            color: #777a95;
            background: #faf9ff;
            font-size: 11px;
            line-height: 1.8;
        }

        .ep-auth-help>i {
            flex-shrink: 0;
            margin-top: 2px;
            color: #8b7df4;
            font-size: 16px;
        }

        .ep-auth-security {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 35px;
            color: #a0a2b7;
            font-size: 10px;
            text-align: center;
        }

        .ep-auth-security i {
            color: #38a980;
            font-size: 15px;
        }

        /* RESPONSIVE LAYOUT */

        @media (max-width: 1050px) {
            .ep-auth-page {
                padding: 16px;
            }

            .ep-auth-shell {
                grid-template-columns: minmax(0, 1fr) minmax(0, .95fr);
                min-height: 700px;
            }

            .ep-auth-visual {
                padding: 34px 30px 25px;
            }

            .ep-auth-visual h1 {
                font-size: 39px;
            }

            .ep-auth-panel {
                padding: 42px 30px;
            }

            .ep-auth-preview {
                padding: 15px;
            }
        }

        @media (max-width: 767px) {
            .ep-auth-page {
                display: block;
                min-height: 100vh;
                min-height: 100dvh;
                padding: 0;
                background: #fff;
            }

            .ep-auth-shell {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
                min-height: 100dvh;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .ep-auth-visual {
                min-height: auto;
                padding: 25px 24px 29px;
                border-radius: 0 0 25px 25px;
            }

            .ep-auth-visual .ep-auth-brand {
                margin-bottom: 0;
            }

            .ep-auth-visual__content {
                margin: 32px 0 0;
            }

            .ep-auth-eyebrow {
                margin-bottom: 17px;
                font-size: 9px;
            }

            .ep-auth-visual h1 {
                max-width: 430px;
                font-size: clamp(32px, 8vw, 43px);
                letter-spacing: -1.5px;
            }

            .ep-auth-visual__description {
                max-width: 440px;
                font-size: 13px;
                line-height: 1.8;
            }

            .ep-auth-benefits {
                grid-template-columns: 1fr;
                gap: 11px;
                margin-top: 21px;
            }

            .ep-auth-benefit {
                font-size: 12px;
            }

            .ep-auth-preview {
                display: none;
            }

            .ep-auth-visual__footer {
                display: none;
            }

            .ep-auth-panel {
                align-items: flex-start;
                padding: 34px 24px 28px;
            }

            .ep-auth-form {
                max-width: 460px;
                margin: 0 auto;
            }

            .ep-auth-mobile-brand {
                display: none;
            }

            .ep-auth-heading {
                margin-bottom: 26px;
            }

            .ep-auth-heading__icon {
                width: 43px;
                height: 43px;
                margin-bottom: 19px;
            }

            .ep-auth-heading h2 {
                font-size: 29px;
            }

            .ep-auth-security {
                margin-top: 27px;
            }
        }

        @media (max-width: 380px) {
            .ep-auth-visual {
                padding-right: 19px;
                padding-left: 19px;
            }

            .ep-auth-panel {
                padding-right: 19px;
                padding-left: 19px;
            }

            .ep-auth-visual h1 {
                font-size: 31px;
            }

            .ep-auth-heading h2 {
                font-size: 27px;
            }

            .ep-auth-form .form-control {
                font-size: 11px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .ep-auth-page *,
            .ep-auth-page *::before,
            .ep-auth-page *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
            }
        }
    </style>
@endsection

@section('content')
    <main class="ep-auth-page">
        <div class="ep-auth-shell">

            {{-- LEFT: BRAND AND PRODUCT EXPERIENCE --}}
            <section class="ep-auth-visual" aria-label="Event operations platform introduction">

                <div class="ep-auth-visual-grid" aria-hidden="true"></div>

                <a href="{{ url('/') }}" class="ep-auth-brand" aria-label="{{ config('app.name', 'EventOps') }} home">
                    <span class="ep-auth-brand__icon">
                        <i class="ti ti-calendar-event"></i>
                    </span>
                    <span class="ep-auth-brand__name">{{ config('app.name', 'EventOps') }}</span>
                </a>

                <div class="ep-auth-visual__content">

                    <div class="ep-auth-eyebrow">
                        <i class="ti ti-sparkles"></i>
                        Built for exceptional events
                    </div>

                    <h1>
                        Every event.<br>
                        Every detail.<br>
                        <span>Under control.</span>
                    </h1>

                    <p class="ep-auth-visual__description">
                        Your dedicated workspace to manage events, coordinate teams,
                        welcome attendees and deliver experiences people remember.
                    </p>

                    <div class="ep-auth-benefits">

                        <div class="ep-auth-benefit">
                            <span class="ep-auth-benefit__icon">
                                <i class="ti ti-circle-check"></i>
                            </span>
                            <span>Everything organized in one workspace</span>
                        </div>

                        <div class="ep-auth-benefit">
                            <span class="ep-auth-benefit__icon">
                                <i class="ti ti-shield-check"></i>
                            </span>
                            <span>Secure, role-based team access</span>
                        </div>

                        <div class="ep-auth-benefit">
                            <span class="ep-auth-benefit__icon">
                                <i class="ti ti-scan"></i>
                            </span>
                            <span>Efficient attendee and ticket management</span>
                        </div>

                    </div>
                </div>

                {{-- Decorative product preview; all figures are illustrative --}}
                <div class="ep-auth-preview" aria-label="Illustrative event dashboard preview">

                    <div class="ep-auth-preview__header">
                        <span class="ep-auth-preview__label">
                            <i class="ti ti-layout-dashboard me-1"></i>
                            Operations overview
                        </span>

                        <span class="ep-auth-preview__status">
                            <span></span>
                            Workspace
                        </span>
                    </div>

                    <div class="ep-auth-preview__stats">

                        <div class="ep-auth-preview__stat">
                            <span class="ep-auth-preview__stat-label">Events</span>
                            <strong class="ep-auth-preview__stat-value">--</strong>
                        </div>

                        <div class="ep-auth-preview__stat">
                            <span class="ep-auth-preview__stat-label">Attendees</span>
                            <strong class="ep-auth-preview__stat-value">--</strong>
                        </div>

                        <div class="ep-auth-preview__stat">
                            <span class="ep-auth-preview__stat-label">Check-ins</span>
                            <strong class="ep-auth-preview__stat-value">--</strong>
                        </div>

                    </div>

                    <div class="ep-auth-preview__bottom">
                        <p>
                            One workspace.<br>
                            Your entire event operation.
                        </p>

                        <div class="ep-auth-preview__avatars" aria-hidden="true">
                            <span class="ep-auth-preview__avatar">EV</span>
                            <span class="ep-auth-preview__avatar">TM</span>
                            <span class="ep-auth-preview__avatar">OP</span>
                            <span class="ep-auth-preview__avatar">
                                <i class="ti ti-users"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="ep-auth-visual__footer">
                    <i class="ti ti-sparkles me-1"></i>
                    The right tools for every stage of your event.
                </div>

            </section>

            {{-- RIGHT: AUTHENTICATION FORM --}}
            <section class="ep-auth-panel" aria-labelledby="ep-login-title">

                <div class="ep-auth-form">

                    <a href="{{ url('/') }}" class="ep-auth-back">
                        <i class="ti ti-arrow-left"></i>
                        Back to public website
                    </a>

                    <div class="ep-auth-heading mt-4">

                        <span class="ep-auth-heading__icon">
                            <i class="ti ti-lock"></i>
                        </span>

                        <h2 id="ep-login-title">Welcome back</h2>

                        <p>
                            Sign in to your team account to continue<br class="d-none d-sm-block">
                            managing your events.
                        </p>

                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0" role="alert">
                            <strong>
                                <i class="ti ti-alert-circle me-1"></i>
                                Unable to sign in
                            </strong>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="ems-login-form d-grid gap-4" data-login-form>
                        @csrf

                        {{-- EMAIL --}}
                        <div>
                            <label for="email" class="form-label">
                                Email address
                            </label>

                            <div class="input-group">
                                <span class="input-group-text" aria-hidden="true">
                                    <i class="ti ti-mail"></i>
                                </span>

                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" placeholder="you@company.com" autocomplete="username"
                                    inputmode="email" autocapitalize="none" spellcheck="false" autofocus required
                                    aria-describedby="email-error">
                            </div>

                            @error('email')
                                <div id="email-error" class="text-danger small mt-2" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- PASSWORD --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-2">
                                <label for="password" class="form-label mb-0">
                                    Password
                                </label>

                                @if (\Illuminate\Support\Facades\Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>

                            <div class="input-group">
                                <span class="input-group-text" aria-hidden="true">
                                    <i class="ti ti-lock"></i>
                                </span>

                                <input id="password" type="password" class="form-control" name="password"
                                    placeholder="Enter your password" autocomplete="current-password" required
                                    aria-describedby="password-error">

                                <button class="btn btn-outline-secondary" type="button" data-toggle-password
                                    aria-label="Show password" aria-pressed="false">
                                    <i class="ti ti-eye" aria-hidden="true"></i>
                                </button>
                            </div>

                            @error('password')
                                <div id="password-error" class="text-danger small mt-2" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- REMEMBER ME --}}
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" value="1"
                                {{ old('remember') ? 'checked' : '' }}>

                            <span class="form-check-label">
                                Keep me signed in on this device
                            </span>
                        </label>

                        {{-- SUBMIT --}}
                        <button class="btn btn-primary ep-auth-submit" type="submit" data-login-submit>
                            <span data-login-submit-label>Sign in to your account</span>

                            <i class="ti ti-arrow-up-right ms-2" data-login-submit-arrow aria-hidden="true"></i>

                            <span class="spinner-border spinner-border-sm d-none ms-2" aria-hidden="true"
                                data-login-submit-spinner></span>
                        </button>

                    </form>

                    {{-- ACCESS INFORMATION --}}
                    <div class="ep-auth-help">
                        <i class="ti ti-info-circle" aria-hidden="true"></i>

                        <span>
                            <strong>Team access only.</strong>
                            Accounts are provided by your administrator.
                            Contact your team administrator if you need access.
                        </span>
                    </div>

                    <div class="ep-auth-security">
                        <i class="ti ti-shield-check" aria-hidden="true"></i>
                        <span>Secure authentication · Protected team workspace</span>
                    </div>

                </div>
            </section>

        </div>
    </main>
@endsection

@section('page-script')
    <script>
        (function() {
            'use strict';

            // Toggle password visibility.
            const passwordToggle = document.querySelector('[data-toggle-password]');
            const passwordInput = document.getElementById('password');

            passwordToggle?.addEventListener('click', function() {
                if (!passwordInput) {
                    return;
                }

                const showPassword = passwordInput.type === 'password';
                const icon = this.querySelector('i');

                passwordInput.type = showPassword ? 'text' : 'password';

                if (icon) {
                    icon.className = showPassword ? 'ti ti-eye-off' : 'ti ti-eye';
                }

                this.setAttribute(
                    'aria-label',
                    showPassword ? 'Hide password' : 'Show password'
                );

                this.setAttribute('aria-pressed', String(showPassword));
            });

            // Prevent accidental duplicate form submissions.
            const loginForm = document.querySelector('[data-login-form]');

            loginForm?.addEventListener('submit', function(event) {
                if (!this.checkValidity()) {
                    return;
                }

                const button = this.querySelector('[data-login-submit]');

                if (!button || button.disabled) {
                    event.preventDefault();
                    return;
                }

                button.disabled = true;
                button.setAttribute('aria-busy', 'true');

                const label = button.querySelector('[data-login-submit-label]');
                const arrow = button.querySelector('[data-login-submit-arrow]');
                const spinner = button.querySelector('[data-login-submit-spinner]');

                if (label) {
                    label.textContent = 'Signing in...';
                }

                arrow?.classList.add('d-none');
                spinner?.classList.remove('d-none');
            });

        })();
    </script>
@endsection
