<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome Guest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-warning-subtle">
    <div class="container py-5 text-center">
        <h2 class="mb-4">👋 Hello, {{ $name }}!</h2>
        <p class="lead">Here are your guest login details:</p>

        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <p><strong>Email:</strong> {{ $email }}</p>
                <p><strong>Password:</strong> {{ $password }}</p>
                <form action="{{ route('guest.enter') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 mt-3">Enter Dashboard</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
