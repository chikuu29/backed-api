<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>membership expired</title>
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
            background-image: url({{$foter}});
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

        .social-icons i {
            z-index: 10000;
            font-size: 30px;
            margin-right: 11px;
            margin-top: 25px;
            color: #007bff;
        }

        .login-btn {
            text-align: center;
            margin-top: 20px;
        }

        .login-btn a {
            display: inline-block;
            text-decoration: none;
        }

        .login-btn button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-btn button:hover {
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

            .social-icons i {
                font-size: 24px;
                margin-top: 15px;
            }

            .login-btn button {
                padding: 8px 16px;
                font-size: 14px;
            }

            .social-icons {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .social-icons a {
                margin: 0 10px;
                display: inline-block;
                /* Adjust size as needed */
                /* Adjust size as needed */
            }

            .imgh {
                height: 31px;
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
        <p style="color: green;">Your account is currently running with a free membership. As a result, you have not been able to fully utilize our website's services. Please upgrade your account and contact your life partner.</p>

        <p style="color: green;">Best Regards,<br><span style="color: green;"> Choicemarriage Team</span></p>

        <div class="update-image-container">
            <img src="{{ $membershipimg }}" alt="Update Image" class="update-image">
        </div>

        <p style="text-align: center;">
            <a href="#" class="cta-button" style="display: inline-block; padding: 12px 24px; font-size: 18px; font-weight: bold; color: #fff; background-color: #007bff; border: 2px solid #007bff; border-radius: 5px; text-decoration: none; transition: background-color 0.3s ease;">UPGRADE NOW</a>
        </p>

        <br>

        <div class="social-icons">
            <a href="{{ $fb }}"><img style=" height: 31px;" src="https://choicemarriage.com/storage/facebook.png" alt="Facebook"></a>
            <a href="{{ $in }}"><img style=" height: 31px;" src="https://choicemarriage.com/storage/instagram.png" alt="Instagram"></a>
            <a href="{{ $ld }}"><img style=" height: 31px;" src="https://choicemarriage.com/storage/linkedin.png" alt="LinkedIn"></a>
            <a href="{{ $yt }}"><img style=" height: 31px;" src="https://choicemarriage.com/storage/youtube.png" alt="YouTube"></a>
            <a href="{{ $x }}"><img style=" height: 31px;" src="https://choicemarriage.com/storage/x.png" alt="Close"></a>
        </div>

    </div>
</body>

</html>
