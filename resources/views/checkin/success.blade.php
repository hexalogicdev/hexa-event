<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Check-In Successful</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #4f4f50, #6610f2);
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            background: white;
            color: #333;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>✅ Check-In Successful</h2>
        <p>{{ $attendee->name }} has been checked in.</p>
        <p><strong>Event:</strong> {{ $attendee->event->name }}</p>
        <p><strong>Time:</strong> {{ $attendee->checked_in_at->format('d M Y, h:i A') }}</p>
    </div>

</body>

</html>
