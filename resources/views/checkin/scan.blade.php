@extends('layouts.layoutMaster')

@section('title', 'Event Check-in Scanner')

@section('content')
    <style>
        .scan-page {
            --scan-bg: #ecedf0;
            --scan-panel: #dcdee4;
            --scan-line: #f1f4fa;
            --scan-ink: #2b3c81;
            --scan-muted: #080808;
            color: var(--scan-ink);
            max-width: 1440px;
            margin: auto;
        }

        .scan-header {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .scan-eyebrow {
            color: #9eb5fb;
            font-weight: 700;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .13em;
        }

        .scan-heading {
            font-size: clamp(1.5rem, 2.4vw, 2.25rem);
            font-weight: 750;
            margin: .3rem 0;
            color: var(--scan-ink);
        }

        .scan-subtitle {
            color: var(--scan-muted);
            margin: 0;
        }

        .scan-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(310px, .7fr);
            gap: 1.25rem;
            align-items: start;
        }

        .scan-card {
            background: var(--scan-panel);
            border: 1px solid var(--scan-line);
            border-radius: 20px;
            padding: clamp(1rem, 2.5vw, 1.5rem);
            box-shadow: 0 12px 30px #080f2126;
            min-width: 0;
        }

        .scan-card+.scan-card {
            margin-top: 1.25rem;
        }

        .scan-card h2 {
            color: var(--scan-ink);
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: .4rem;
        }

        .scan-help {
            color: var(--scan-muted);
            font-size: .9rem;
        }

        .scan-camera {
            background: #080e1d;
            border: 1px solid #3e4d70;
            border-radius: 17px;
            overflow: hidden;
            min-height: 310px;
            position: relative;
            display: grid;
            place-items: center;
        }

        #qr-reader {
            width: 100%;
            min-height: 310px;
        }

        #qr-reader video {
            width: 100% !important;
            object-fit: cover;
            max-height: 460px;
        }

        #qr-reader img {
            display: none !important;
        }

        .scan-placeholder {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            gap: .6rem;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1.5rem;
            background: radial-gradient(circle at center, #1b3155, #080e1d 75%);
            pointer-events: none;
        }

        .scan-placeholder i {
            font-size: 3rem;
            color: #9eb5fb;
        }

        .scan-placeholder[hidden] {
            display: none;
        }

        .scan-camera::after {
            content: "";
            position: absolute;
            width: min(62%, 250px);
            aspect-ratio: 1;
            border: 2px solid #77d4ec;
            border-radius: 17px;
            box-shadow: 0 0 0 100vmax #0000;
            pointer-events: none;
            opacity: 0;
        }

        .scan-camera.is-active::after {
            opacity: .85;
        }

        .scan-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            margin: 1rem 0;
        }

        .scan-actions .btn {
            border-radius: 10px;
        }

        .scan-status {
            border: 1px solid #3e4d70;
            background: #111e38;
            border-radius: 12px;
            padding: .9rem 1rem;
            color: #d2def5;
            font-size: .92rem;
            margin-bottom: 1rem;
        }

        .scan-status[data-kind="success"] {
            background: #0b382e;
            border-color: #247d68;
            color: #c2f8df;
        }

        .scan-status[data-kind="error"] {
            background: #4b2028;
            border-color: #af4c5e;
            color: #ffe3e7;
        }

        .scan-status[data-kind="warning"] {
            background: #483515;
            border-color: #9e7730;
            color: #ffedbd;
        }

        .scan-page .form-label {
            color: #e5ecff;
        }

        .scan-page .form-control,
        .scan-page .form-select {
            background: #0e1930;
            border: 1px solid #465774;
            color: #fff;
            border-radius: 10px;
        }

        .scan-page .form-control::placeholder {
            color: #91a1be;
        }

        .scan-page .form-control:focus,
        .scan-page .form-select:focus {
            border-color: #81a6ff;
            box-shadow: 0 0 0 .2rem #567bef40;
        }

        .scan-event {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            border: 1px solid var(--scan-line);
            border-radius: 12px;
            padding: .9rem;
            margin-top: .65rem;
        }

        .scan-event small {
            color: var(--scan-muted);
        }

        .scan-table {
            --bs-table-bg: transparent;
            --bs-table-color: #eaf0ff;
            --bs-table-border-color: #344463;
        }

        .scan-table th {
            color: #a5b7d6;
            font-weight: 600;
            font-size: .8rem;
        }

        .scan-empty {
            color: var(--scan-muted);
            text-align: center;
            padding: 1.5rem;
        }

        @media(max-width:991.98px) {
            .scan-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:575.98px) {

            .scan-camera,
            #qr-reader {
                min-height: 265px;
            }

            .scan-actions .btn {
                flex: 1;
            }
        }
    </style>

    <section class="container-fluid py-3 py-lg-4 scan-page">
        <header class="scan-header">
            <div>
                <div class="scan-eyebrow"><i class="ti ti-shield-check me-1"></i> Staff check-in portal</div>
                <h1 class="scan-heading">Event check-in scanner</h1>
                <p class="scan-subtitle">Scan a ticket securely or enter its booking reference manually.</p>
            </div>
            <a href="{{ route('attendee.qr.scan') }}" class="btn btn-outline-primary"><i class="ti ti-refresh me-1"></i>
                Refresh results</a>
        </header>

        <div class="scan-grid">
            <div>
                <div class="scan-card">
                    <h2><i class="ti ti-camera me-2"></i>Live QR scanner</h2>
                    <p class="scan-help mb-3">Press <strong>Start camera</strong> and allow camera access when your browser
                        asks. Hold the QR code inside the frame.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3" role="alert">{{ $errors->first() }}</div>
                    @endif

                    <div class="scan-camera" id="scan-camera">
                        <div id="qr-reader" aria-label="Live camera preview"></div>
                        <div class="scan-placeholder" id="scan-placeholder">
                            <i class="ti ti-qrcode"></i>
                            <strong>Camera is off</strong>
                            <span class="scan-help">Your browser will request permission when you start scanning.</span>
                        </div>
                    </div>

                    <div class="scan-actions">
                        <button type="button" class="btn btn-primary" id="scan-start"><i class="ti ti-camera me-1"></i>
                            Start camera</button>
                        <button type="button" class="btn btn-outline-light" id="scan-stop" disabled><i
                                class="ti ti-player-stop me-1"></i> Stop camera</button>
                        <button type="button" class="btn btn-outline-info" id="scan-switch" disabled><i
                                class="ti ti-switch-horizontal me-1"></i> Switch camera</button>
                    </div>

                    <div id="scan-status" class="scan-status" data-kind="neutral" role="status" aria-live="polite">Ready.
                        Start the camera or enter a reference below.</div>

                    <form id="scan-form" action="{{ route('attendee.qr.scan.verify') }}" method="POST">
                        @csrf
                        <label class="form-label fw-semibold text-white" for="qr_id">Booking reference / QR code</label>
                        <input id="qr_id" name="qr_id" type="text"
                            class="form-control form-control-lg mb-3 @error('qr_id') is-invalid @enderror"
                            placeholder="Scan or enter reference" value="{{ old('qr_id') }}" autocomplete="off"
                            autocapitalize="characters" spellcheck="false" required>
                        @error('qr_id')
                            <div class="invalid-feedback d-block mb-2">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="scan-submit"><i
                                class="ti ti-check me-1"></i> Verify and check in</button>
                    </form>
                    <p class="scan-help mt-3 mb-0"><i class="ti ti-info-circle me-1"></i> Camera access requires HTTPS (or
                        localhost). Check-in is validated by the server.</p>
                </div>
            </div>

            <aside>
                <div class="scan-card">
                    <h2><i class="ti ti-calendar-event me-2"></i>Assigned events</h2>
                    <p class="scan-help mb-2">Events authorized for your staff account</p>
                    @forelse($events as $event)
                        <div class="scan-event">
                            <div>
                                <div class="fw-semibold" style="color:black">{{ $event->name }}</div>
                                <small>{{ $event->location ?: 'Location not set' }}</small>
                            </div>
                            <small class="text-nowrap">{{ $event->event_start_date?->format('d M') }}</small>
                        </div>
                    @empty
                        <div class="scan-empty">No active events assigned to this account.</div>
                    @endforelse
                </div>
                <div class="scan-card">
                    <h2><i class="ti ti-history me-2"></i>Recent activity</h2>
                    <div class="table-responsive">
                        <table class="table scan-table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Attendee</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttempts as $attempt)
                                    <tr>
                                        <td class="text-nowrap">{{ $attempt->attempted_at?->format('H:i:s') }}</td>
                                        <td>
                                            <div>{{ $attempt->attendee?->name ?? ($attempt->reference ?? 'Unknown') }}</div>
                                            <small class="scan-help">{{ $attempt->event?->name ?? 'Not resolved' }}</small>
                                        </td>
                                        <td><span
                                                class="badge {{ $attempt->outcome === 'accepted' ? 'bg-success' : ($attempt->outcome === 'duplicate' ? 'bg-warning text-dark' : 'bg-danger') }}">{{ str_replace('_', ' ', ucfirst($attempt->outcome)) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="scan-empty">No scans recorded yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection

@section('page-script')
    {{-- For production, preferably bundle/pin this dependency locally using your existing asset pipeline. --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const $ = (id) => document.getElementById(id);
            const startButton = $('scan-start');
            const stopButton = $('scan-stop');
            const switchButton = $('scan-switch');
            const status = $('scan-status');
            const preview = $('scan-camera');
            const placeholder = $('scan-placeholder');
            const input = $('qr_id');
            const form = $('scan-form');
            const submitButton = $('scan-submit');
            let scanner = null;
            let state = 'idle';
            let consumed = false;
            let availableCameras = [];
            let cameraIndex = -1;
            let generation = 0;

            function message(text, kind = 'neutral') {
                status.textContent = text;
                status.dataset.kind = kind;
            }

            function controls() {
                startButton.disabled = state !== 'idle';
                stopButton.disabled = state === 'idle' || state === 'stopping';
                switchButton.disabled = state !== 'active' || availableCameras.length < 2;
            }

            function permissionError(error) {
                const name = error?.name || '';
                if (name === 'NotAllowedError' || name === 'PermissionDeniedError') {
                    return 'Camera permission was denied. Allow camera access in your browser/site settings, then try again.';
                }
                if (name === 'NotFoundError' || name === 'DevicesNotFoundError') {
                    return 'No camera was found on this device. Connect a camera or enter the reference manually.';
                }
                if (name === 'NotReadableError' || name === 'TrackStartError') {
                    return 'The camera is already in use or unavailable. Close other camera apps and retry.';
                }
                if (name === 'OverconstrainedError') {
                    return 'The selected camera is unavailable. Try switching cameras or restarting the scanner.';
                }
                return 'Could not start the camera. Check browser permissions and retry, or use manual entry.';
            }

            function parseReference(raw) {
                const text = String(raw ?? '').trim();
                if (!text) return '';
                try {
                    const url = new URL(text);
                    if (url.protocol !== 'https:' && url.protocol !== 'http:') return text;
                    const parts = url.pathname.split('/').filter(Boolean);
                    return decodeURIComponent(parts[parts.length - 1] || '').trim();
                } catch (_) {
                    return text;
                }
            }

            async function releaseScanner() {
                const old = scanner;
                scanner = null;
                if (!old) return;
                try {
                    if (old.isScanning) await old.stop();
                } catch (_) {
                    /* Device may already have stopped. */
                }
                try {
                    old.clear();
                } catch (_) {
                    /* Nothing left to clear. */
                }
            }

            async function stopScanner(note = 'Camera stopped. You can restart or enter a reference.') {
                ++generation;
                state = 'stopping';
                controls();
                await releaseScanner();
                state = 'idle';
                preview.classList.remove('is-active');
                placeholder.hidden = false;
                controls();
                message(note);
            }

            async function startScanner(cameraId = null) {
                if (state !== 'idle') return;
                if (!window.isSecureContext || !navigator.mediaDevices?.getUserMedia) {
                    message('Camera access requires HTTPS or localhost in a supported browser. Use manual entry if unavailable.',
                        'error');
                    return;
                }
                if (typeof window.Html5Qrcode !== 'function') {
                    message('The QR scanner library did not load. Check your connection or bundled scanner script.',
                        'error');
                    return;
                }

                const currentGeneration = ++generation;
                state = 'starting';
                consumed = false;
                controls();
                message('Requesting camera access. Please choose Allow when your browser prompts you...');
                try {
                    scanner = new Html5Qrcode('qr-reader', {
                        verbose: false
                    });
                    const chosenCamera = cameraId || {
                        facingMode: 'environment'
                    };
                    await scanner.start(chosenCamera, {
                        fps: 10,
                        qrbox: (width, height) => {
                            const side = Math.max(150, Math.min(260, Math.floor(Math.min(width,
                                height) * .75)));
                            return {
                                width: side,
                                height: side
                            };
                        }
                    }, async (decodedText) => {
                        if (consumed || state !== 'active') return;
                        consumed = true;
                        const reference = parseReference(decodedText);
                        if (!reference) {
                            consumed = false;
                            message('This QR code has no usable reference. Please scan another code.',
                                'warning');
                            return;
                        }
                        input.value = reference;
                        submitButton.disabled = true;
                        message('QR detected. Sending ticket to the server for check-in...',
                            'success');
                        await stopScanner('QR detected. Verifying ticket on the server...');
                        form.requestSubmit();
                    }, () => {
                        /* Normal frame with no detected code. */
                    });

                    if (currentGeneration !== generation) {
                        await releaseScanner();
                        return;
                    }
                    state = 'active';
                    preview.classList.add('is-active');
                    placeholder.hidden = true;
                    message('Camera active. Position a ticket QR code inside the frame.', 'success');
                    try {
                        availableCameras = await Html5Qrcode.getCameras();
                        const actualId = scanner.getRunningTrackCameraCapabilities?.()?.deviceId;
                        cameraIndex = actualId ? availableCameras.findIndex(c => c.id === actualId) :
                            cameraIndex;
                    } catch (_) {
                        availableCameras = [];
                    }
                    controls();
                } catch (error) {
                    await releaseScanner();
                    state = 'idle';
                    preview.classList.remove('is-active');
                    placeholder.hidden = false;
                    controls();
                    message(permissionError(error), 'error');
                }
            }

            startButton.addEventListener('click', () => startScanner());
            stopButton.addEventListener('click', () => {
                if (state === 'active') void stopScanner();
                else if (state === 'starting') message(
                    'Waiting for the browser camera permission prompt to complete.', 'warning');
            });
            switchButton.addEventListener('click', async () => {
                if (state !== 'active' || availableCameras.length < 2) return;
                cameraIndex = (cameraIndex + 1) % availableCameras.length;
                const nextCamera = availableCameras[cameraIndex].id;
                await stopScanner('Switching camera...');
                await startScanner(nextCamera);
            });
            input.addEventListener('input', () => {
                input.value = input.value.toUpperCase();
            });
            form.addEventListener('submit', () => {
                submitButton.disabled = true;
            });
            window.addEventListener('pagehide', () => {
                void releaseScanner();
            });
            controls();
        });
    </script>
@endsection
