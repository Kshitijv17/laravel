<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-4 text-center">Admin Registration</h4>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('shopkeeper.register.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                            </div>

                            @if($canCreateSuperAdmin ?? false)
                            <div class="mb-3">
                                <label for="role" class="form-label">Admin Role</label>
                                <select name="role" class="form-control" id="role">
                                    <option value="admin" {{ old('role', 'admin') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="super_admin" {{ old('role', 'super_admin') == 'super_admin' || ($isFirstAdmin ?? false) ? 'selected' : '' }}>Super Admin</option>
                                </select>
                                <div class="form-text">
                                    @if($isFirstAdmin ?? false)
                                        <span class="text-success">First admin will be Super Admin by default</span>
                                    @else
                                        <span class="text-info">Only Super Admins can create other Super Admins</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <button type="submit" class="btn btn-success w-100">Register</button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="{{ route('shopkeeper.login') }}">Already a shopkeeper? Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
