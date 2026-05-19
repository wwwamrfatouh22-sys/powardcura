<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5" style="max-width: 520px;">
    <h1 class="h4 mb-3">Reset {{ ucfirst($role) }} Password</h1>
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    <form method="POST" action="{{ route('password.email', $role) }}" class="card card-body shadow-sm">
        @csrf
        <label class="form-label" for="email">Email address</label>
        <input id="email" class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" required autofocus>
        <button class="btn btn-primary" type="submit">Send reset link</button>
    </form>
</main>
</body>
</html>
