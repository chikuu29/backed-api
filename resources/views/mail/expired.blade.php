<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Expired</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header img {
            max-width: 150px;
            height: auto;
            border-radius: 10px;
        }

        .mail-date {
            font-size: 14px;
            color: #777;
        }

        .content {
            font-size: 16px;
            line-height: 1.6;
            color: green;
        }

        .update-image-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .update-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        h3 {
            text-align: center;
        }

        .social-icons {
            text-align: center;
            margin-top: 20px;
            background-size: cover;
            background-position: center;
            padding: 20px;
            border-radius: 10px;
            height: 83px;
        }

        .social-icons a {
            display: inline-block;
            margin: 0 10px;
        }

        .social-icons img {
            height: 31px;
            transition: transform 0.3s;
        }

        .social-icons img:hover {
            transform: scale(1.1);
        }

        .login-btn {
            text-align: center;
            margin-top: 20px;
        }

        .login-btn a {
            text-decoration: none;
        }

        .login-btn button,
        .cta-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .login-btn button:hover,
        .cta-button:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media only screen and (max-width: 768px) {
            .container {
                max-width: 90%;
            }
        }

        @media only screen and (max-width: 480px) {
            .header img {
                max-width: 100px;
            }

            .mail-date {
                font-size: 12px;
            }

            .content {
                font-size: 14px;
            }

            .update-image-container {
                margin-bottom: 10px;
            }

            .update-image {
                border-radius: 5px;
            }

            .social-icons {
                height: auto;
            }

            .social-icons img {
                height: 24px;
            }

            .login-btn button,
            .cta-button {
                padding: 8px 16px;
                font-size: 14px;
            }

            .social-icons {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .social-icons a {
                margin: 0 5px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ $imageurl }}" alt="Logo Image">
            <span class="mail-date">18 05 2024</span>
        </div>
        <div class="update-image-container">
            <img src="{{ $baner }}" alt="Update Image" class="update-image">
        </div>
        <p class="content">Your membership has expired. Please note that due to the expiration of your membership, you may not have
            access to certain features. We kindly request you to renew your membership and utilize India's No1 matrimonial
            choicemarriage.com. Choose your best option on the site.</p>

        <p class="content">Best Regards,<br><span style="color: green;">Choicemarriage Team</span></p>

        <div class="update-image-container">
            <img src="{{ $membershipimg }}" alt="Membership Image" class="update-image">
        </div>

        <p style="text-align: center;">
            <a href="https://choicemarriage.com/subscription/plan" class="cta-button">UPGRADE NOW</a>
        </p>

        <div class="social-icons" style="background-image: url({{$foter}});">
            <a href="{{ $fb }}"><img src="https://choicemarriage.com/storage/facebook.png" alt="Facebook"></a>
            <a href="{{ $in }}"><img src="https://choicemarriage.com/storage/instagram.png" alt="Instagram"></a>
            <a href="{{ $ld }}"><img src="https://choicemarriage.com/storage/linkedin.png" alt="LinkedIn"></a>
            <a href="{{ $yt }}"><img src="https://choicemarriage.com/storage/youtube.png" alt="YouTube"></a>
            <a href="{{ $x }}"><img src="https://choicemarriage.com/storage/x.png" alt="X"></a>
        </div>
    </div>
</body>

</html>
