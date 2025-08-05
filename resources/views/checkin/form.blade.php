<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Check-In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #4f4f50, #6610f2);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .checkin-card {
            background: #fff;
            color: #333;
            border-radius: 10px;
            padding: 2rem;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-primary {
            background-color: #667eea;
            border: none;
        }

        .btn-primary:hover {
            background-color: #5a67d8;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
</head>

<body>

    <div class="checkin-card">
        <h3 class="mb-4 text-center">Attendee Check-In</h3>

        <div class="mb-3">
            <strong>Name:</strong> {{ $attendee->name }}<br>
            <strong>Email:</strong> {{ $attendee->email }}<br>
            <strong>Event:</strong> {{ $attendee->event->name }}<br>
            <strong>QR ID:</strong> {{ $attendee->qr_id }}
        </div>

        @if ($attendee->checked_in)
            <div class="alert alert-success">
                ✅ Already checked in at {{ $attendee->checked_in_at?->format('d M Y, h:i A') }}
            </div>
        @else
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('attendee.qr.verify', $attendee->qr_id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="checkin_password" class="form-label">Check-In Password</label>
                    <input type="password" name="checkin_password" id="checkin_password"
                        class="form-control @error('checkin_password') is-invalid @enderror" required>
                    @error('checkin_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Confirm Check-In</button>
            </form>
        @endif
    </div>

</body>

</html>
