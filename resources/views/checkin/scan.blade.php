@extends('layouts.app')

@section('title', 'Scan QR Check-In')

@section('content')
    <section class="container-fluid py-3 py-lg-4">
        <div class="ems-checkin-shell ems-checkin-shell--wide">
            <div class="ems-checkin-frame ems-fade-up">
                <div class="ems-checkin-toolbar">
                    <div class="d-flex flex-column gap-2">
                        <span class="ems-badge ems-badge--info">
                            <i class="ti ti-scan"></i>
                            Camera and scanner mode
                        </span>
                        <div>
                            <h1 class="ems-checkin-title mb-1">Scan a QR code, enter the event password, and check the
                                attendee in.</h1>
                            <p class="ems-checkin-subtitle mb-0">
                                This screen is designed for mobile cameras, hardware scanners, and manual fallback entry
                                when needed.
                            </p>
                        </div>
                    </div>

                            <div class="d-flex flex-wrap gap-2 ems-toolbar-actions">
                        <a href="{{ url('/') }}" class="btn btn-outline-light">
                            <i class="ti ti-home-2 me-2"></i>
                            Home
                        </a>
                        <a href="{{ route('attendee.qr.scan') }}" class="btn btn-primary">
                            <i class="ti ti-refresh me-2"></i>
                            Reset scanner
                        </a>
                    </div>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-xl-6">
                        <div class="ems-checkin-hero">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-4">
                                <span class="ems-kicker mb-0 bg-white bg-opacity-10 text-white">
                                    <i class="ti ti-qrcode"></i>
                                    Direct QR workflow
                                </span>
                                <span class="ems-badge ems-badge--success">Ready to scan</span>
                            </div>

                            <h2 class="ems-checkin-hero__title">Use the camera or a handheld reader without leaving the
                                theme.</h2>
                            <p class="ems-checkin-hero__text">
                                If the camera is supported, press start and point it at the QR code. Otherwise, scan into
                                the reference field and enter the password.
                            </p>

                            <div class="ems-checkin-summary">
                                <div class="ems-checkin-summary__item">
                                    <span>Mode</span>
                                    <strong>QR camera / external scanner</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Fallback</span>
                                    <strong>Manual reference entry</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Security</span>
                                    <strong>Event password required</strong>
                                </div>
                                <div class="ems-checkin-summary__item">
                                    <span>Status</span>
                                    <strong>Checked in immediately on confirmation</strong>
                                </div>
                            </div>

                            <div class="ems-support-list mt-4">
                                <li>
                                    <i class="ti ti-camera"></i>
                                    <div>
                                        <div class="fw-semibold">Camera assisted scan</div>
                                        <div class="text-white-50">Modern browsers can detect QR values directly from the
                                            camera feed.</div>
                                    </div>
                                </li>
                                <li>
                                    <i class="ti ti-barcode"></i>
                                    <div>
                                        <div class="fw-semibold">Hardware scanner friendly</div>
                                        <div class="text-white-50">Click the QR reference field and let the scanner type the
                                            code for you.</div>
                                    </div>
                                </li>
                                <li>
                                    <i class="ti ti-lock-check"></i>
                                    <div>
                                        <div class="fw-semibold">Password gate</div>
                                        <div class="text-white-50">The attendee is only marked present after the event
                                            password matches.</div>
                                    </div>
                                </li>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="ems-checkin-card-panel">
                            <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                                <div>
                                    <div class="ems-badge ems-badge--info mb-2">Scanner input</div>
                                    <h2 class="ems-section-title h4 mb-1 text-white">Scan or type the booking reference</h2>
                                    <p class="ems-section-subtitle mb-0">Then enter the event password and confirm
                                        attendance.</p>
                                </div>
                                <span class="ems-badge ems-badge--muted">Theme matched</span>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger border-0 rounded-4">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <div class="ems-scan-stage mb-4">
                                <div id="qr-reader" class="ems-scan-video"></div>
                                <div class="ems-scan-overlay">
                                    <i class="ti ti-qrcode"></i>
                                    <div class="fw-semibold">Camera preview</div>
                                    <div class="small">Tap start scan to activate camera detection.</div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3 ems-toolbar-actions">
                                <button type="button" class="btn btn-primary" data-start-scan>
                                    <i class="ti ti-camera me-2"></i>
                                    Start camera scan
                                </button>
                                <button type="button" class="btn btn-outline-light" data-stop-scan disabled>
                                    <i class="ti ti-player-pause me-2"></i>
                                    Stop scan
                                </button>
                            </div>

                            <div class="ems-scan-status mb-4" data-scan-status>
                                Camera is idle. You can start scanning or type the code manually.
                            </div>

                            <form action="{{ route('attendee.qr.scan.verify') }}" method="POST" class="d-grid gap-3">
                                @csrf
                                <div>
                                    <label for="qr_id" class="form-label text-white">Booking reference / QR code</label>
                                    <input type="text" name="qr_id" id="qr_id"
                                        class="form-control form-control-lg text-uppercase @error('qr_id') is-invalid @enderror"
                                        placeholder="Scan or enter the QR reference" autocomplete="off"
                                        autocapitalize="characters" spellcheck="false" required>
                                    @error('qr_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="checkin_password_scan" class="form-label text-white">Event password</label>
                                    <input type="password" name="checkin_password" id="checkin_password_scan"
                                        class="form-control form-control-lg @error('checkin_password') is-invalid @enderror"
                                        placeholder="Enter the check-in password" required>
                                    @error('checkin_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="ti ti-circle-check me-2"></i>
                                    Mark attendee checked in
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const startButton = document.querySelector('[data-start-scan]');
            const stopButton = document.querySelector('[data-stop-scan]');
            const status = document.querySelector('[data-scan-status]');
            const qrInput = document.getElementById('qr_id');
            const scannerElementId = 'qr-reader';

            if (!startButton || !stopButton || !status || !qrInput) {
                return;
            }

            if (!window.Html5Qrcode || !navigator.mediaDevices?.getUserMedia) {
                status.textContent = 'Camera scan is not supported in this browser. Use the booking reference field instead.';
                startButton.disabled = true;
                stopButton.disabled = true;
                return;
            }

            let scanner = null;
            let scanning = false;
            let scanLocked = false;
            let autoStartAttempted = false;

            const extractBookingRef = (value) => {
                try {
                    const url = new URL(value);
                    const segments = url.pathname.split('/').filter(Boolean);
                    return (segments.length ? segments[segments.length - 1] : value).toUpperCase();
                } catch (error) {
                    return String(value || '').trim().toUpperCase();
                }
            };

            const setStatus = (message) => {
                status.textContent = message;
            };

            const stopScan = async () => {
                scanning = false;
                scanLocked = false;

                if (scanner) {
                    try {
                        await scanner.stop();
                    } catch (error) {
                        // Ignore stop errors when the scanner is already idle.
                    }

                    try {
                        await scanner.clear();
                    } catch (error) {
                        // Ignore clear errors when the scanner is already cleared.
                    }
                }

                scanner = null;
                startButton.disabled = false;
                stopButton.disabled = true;
                setStatus('Camera is idle. You can start scanning or type the code manually.');
            };

            const startScan = async (auto = false) => {
                if (scanning) {
                    return;
                }

                try {
                    const qrScanner = getScanner();
                    startButton.disabled = true;
                    stopButton.disabled = false;
                    setStatus(auto ? 'Requesting camera permission...' : 'Starting camera...');

                    await qrScanner.start(
                        {
                            facingMode: { ideal: 'environment' }
                        },
                        {
                            fps: 10,
                            qrbox: {
                                width: 240,
                                height: 240
                            },
                            aspectRatio: 1.0,
                            disableFlip: false
                        },
                        async (decodedText) => {
                            if (scanLocked) {
                                return;
                            }

                            scanLocked = true;
                            const bookingRef = extractBookingRef(decodedText);
                            qrInput.value = bookingRef;
                            setStatus(`QR detected: ${bookingRef}`);

                            if (window.toastr) {
                                window.toastr.success(`QR detected: ${bookingRef}`, 'Scan complete');
                            }

                            qrInput.focus();
                            await stopScan();
                        }
                    );

                    scanning = true;
                    setStatus('Camera active. Point it at the QR code.');
                } catch (error) {
                    scanning = false;
                    startButton.disabled = false;
                    stopButton.disabled = true;
                    setStatus('Camera access is required. Allow permission or use manual entry / hardware scanner.');

                    if (!autoStartAttempted && window.toastr) {
                        window.toastr.warning('Allow camera access to scan QR codes on this device.', 'Permission needed');
                    }

                    if (window.toastr) {
                        window.toastr.error('Camera access failed. Use manual entry or a hardware scanner.', 'Scanner unavailable');
                    }
                }
            };

            const getScanner = () => {
                if (!scanner) {
                    scanner = new window.Html5Qrcode(scannerElementId);
                }

                return scanner;
            };

            startButton.addEventListener('click', () => {
                autoStartAttempted = true;
                startScan(false);
            });

            qrInput.addEventListener('input', () => {
                qrInput.value = qrInput.value.toUpperCase();
            });

            stopButton.addEventListener('click', () => {
                stopScan();
            });

            autoStartAttempted = true;
            setTimeout(() => {
                startScan(true);
            }, 350);
        });
    </script>
@endsection
