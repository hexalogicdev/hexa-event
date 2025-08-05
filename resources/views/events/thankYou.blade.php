<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Thank You</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #4f4f50, #6610f2);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .thank-you-box {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 2rem 3rem;
            text-align: center;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .thank-you-box h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .thank-you-box p {
            font-size: 1.2rem;
        }

        .btn-home {
            margin-top: 2rem;
        }
    </style>
</head>

<body>

    <div class="thank-you-box">
        <img src="{{ asset('assets/img/event-logo.png') }}" alt="Event Logo" style="max-height: 80px;" class="mb-4">
        <h1>Thank You for Registering!</h1>
        <p>You have successfully registered</strong>.</p>
        <p>We look forward to seeing you at the event.</p>

        {!! QrCode::size(250)->generate(route('attendee.qr', $attendee->qr_id)) !!}

        <a href="data:image/png;base64,{{ base64_encode(QrCode::format('png')->size(250)->generate(route('attendee.qr', $attendee->qr_id))) }}"
            download="qr-{{ $attendee->qr_id }}.png" class="btn btn-outline-primary mt-3">
            Download QR Code
        </a>
    </div>

</body>

</html>
