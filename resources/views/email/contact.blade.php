<!DOCTYPE html>
<html lang="en" >
<head>
	<title>Contact Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif;font-size:16px">

<h1>Youu have received a contact email.</h1>
<p>Name: {{ $mailData['name'] }}</p>
<p>Email: {{ $mailData['email'] }}</p>
<p>Subject: {{ $mailData['subject'] }}</p>
<p>Message:</p>
<p> {{ $mailData['message'] }}</p>
</body>
</html>
