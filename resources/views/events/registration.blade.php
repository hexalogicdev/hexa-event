<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register for {{ $event->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-image: url('{{ asset('assets/img/event.png') }}');
            /* You can replace this */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }

        .register-card {
            position: relative;
            z-index: 1;
            background: rgba(219, 216, 216, 0.95);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }
    </style>
</head>

<body>

    <div class="overlay"></div>

    <div class="register-card text-dark">
        <h2 class="text-center mb-4">Register for {{ $event->name }}</h2>
        <h5 class="text-center mb-4">Location {{ $event->location }}</h5>
        <h5 class="text-center mb-4">Event Date {{ $event->event_start_date?->format('d/m/Y h:i A') }}</h5>

        <form action="{{ route('attendee.store') }}" method="POST">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="mb-3">
                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" id="name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" id="email" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" id="phone">
            </div>

            <!-- Add more fields here if needed -->

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Register Now</button>
            </div>
        </form>
    </div>

</body>

</html>
