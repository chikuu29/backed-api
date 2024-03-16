<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
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
        <h1>Registration Successful</h1>
        <p>Hello <?php echo $name ?>,</p>
        <p>Congratulations! Your registration was successful. Welcome to our community.</p>
        <p>You can now log in to your account and start exploring our features and services.</p>
        <p>Thank you for joining us!</p>

        <p>Best Regards,<br>Your patrabibaha Team</p>

        <p style="text-align: center;">
            <a href="<?php echo $url ?>" class="cta-button">Log In Now</a>
        </p>
    </div>
</body>

</html>
