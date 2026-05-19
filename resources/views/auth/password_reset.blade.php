<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5" style="max-width: 520px;">
    <h1 class="h4 mb-3">Set New {{ ucfirst($role) }} Password</h1>
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('password.update', $role) }}" class="card card-body shadow-sm">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label class="form-label" for="email">Email address</label>
        <input id="email" class="form-control mb-3" type="email" name="email" value="{{ old('email', $email) }}" required>
        <label class="form-label" for="password">New password</label>
        <input id="password" class="form-control mb-3" type="password" name="password" required>
        <label class="form-label" for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" class="form-control mb-3" type="password" name="password_confirmation" required>
        <button class="btn btn-primary" type="submit">Update password</button>
    </form>
</main>
</body>
</html>
