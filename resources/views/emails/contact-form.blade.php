<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Enquiry</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111; line-height: 1.5;">
    <p>You've received a new enquiry from the Contact Us form on {{ config('app.name', 'Watch Market') }}.</p>
    <table style="width:100%; border-collapse: collapse; margin: 16px 0;">
        <tr><td style="padding:6px 0; font-weight:700; width:120px;">Name</td><td style="padding:6px 0;">{{ $name }}</td></tr>
        @if($email)
        <tr><td style="padding:6px 0; font-weight:700;">Email</td><td style="padding:6px 0;">{{ $email }}</td></tr>
        @endif
        <tr><td style="padding:6px 0; font-weight:700;">Phone</td><td style="padding:6px 0;">{{ $phone }}</td></tr>
        <tr><td style="padding:6px 0; font-weight:700;">Title</td><td style="padding:6px 0;">{{ $title }}</td></tr>
    </table>
    <p style="font-weight:700;">Message:</p>
    <p style="white-space: pre-line;">{{ $messageBody }}</p>
</body>
</html>
