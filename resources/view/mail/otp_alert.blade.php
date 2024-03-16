<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One-Time Password (OTP)</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #4cb96b;
        }

        p {
            margin-bottom: 15px;
        }

        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #4cb96b;
        }

        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4cb96b;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>One-Time Password (OTP)</h1>
        <p>Hello [User's Name],</p>
        <p>Your One-Time Password (OTP) for verification is:</p>
        <p class="otp-code">[Your OTP Code]</p>
        <p>This OTP is valid for a short period. Please do not share it with anyone.</p>
        <p>If you did not request this OTP, please ignore this email.</p>

        <p>Best Regards,<br>Your [Your Company Name] Team</p>

        <p style="text-align: center;">
            <a href="[Your Verification Page URL]" class="cta-button">Verify Now</a>
        </p>
    </div>
</body>

</html>
