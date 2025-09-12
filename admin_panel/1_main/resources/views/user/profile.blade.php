<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  @include('components.navbar')

  <div class="container py-5 mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
      <div class="card-body text-center">
        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}" class="rounded-circle mb-3" height="80" alt="Avatar">
        <h4>{{ $user->name }}</h4>
        <p class="text-muted">{{ $user->email }}</p>
        <p><strong>Account Type:</strong> {{ $user->is_guest ? 'Guest' : 'Registered User' }}</p>
        @if($user->is_guest)
          <p><strong>Expires:</strong> {{ $user->expires_at->format('d M Y') }}</p>
        @endif
      </div>
    </div>
  </div>

</body>
</html>
