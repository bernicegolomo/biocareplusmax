<!-- resources/views/emails/password_reset.blade.php -->

<p>Hello,</p>

<p>We received a request to reset your password. Please click on the link below to reset your password:</p>

<a href="{{ $resetLink }}">{{ $resetLink }}</a>

<p>If you did not request a password reset, please ignore this email.</p>

<p>Thank you,</p>
<p>BCM Team</p>
