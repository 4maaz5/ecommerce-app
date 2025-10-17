<!DOCTYPE html>

<html lang="en">

<head>

    <title>Reset Password Email</title>

</head>

<body style="font-family: Arial, Helvetica, sans-serif;font-size:16px">

<p>Hello, {{ isset($formData['user']) ? $formData['user'] : 'User' }}</p>

<h1>You have requested to change password:</h1>

<p>Please click the link given below to reset the password.</p>

<a href="{{ route('front.resetPassword', isset($formData['token']) ? $formData['token'] : '') }}">Click here</a>

<p>Thanks</p>

</body>

</html>

