<!DOCTYPE html>
<html>
<head>
    <title>Email Confirmation</title>
</head>
<body>
    <p>Hello, {{ $user->name }}!</p>
    <p>Please confirm your email by clicking the link below:</p>
    <p><a href="{{ $confirmationUrl }}">Confirm Email</a></p>
</body>
</html>
