<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gender Change Update</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

            .social-icons a {
                display: inline-block;
                margin: 0 10px;
            }

            .social-icons i {
                font-size: 11px;
                margin-right: 11px;
                margin-top: 25px;
                color: #007bff;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ $imageurl }}" alt="Logo Image">
            <span class="mail-date">{{ $date }}</span>
        </div>
        <div class="update-image-container">
            <img src="{{ $baner }}" alt="Update Image" class="update-image">
        </div>
        <div class="content">
            <h3>{{ $type }} Update</h3>
            <p>Your request has been successfully completed. and your {{ $type }} has been updated in your profile. To ensure the security of your account, kindly proceed with logging in.</p>
            <p>Best regards,<br>{{ env('DB_USERNAME') }} Team</p>
        </div>
        <div class="login-btn">
            <a href="https://choicemarriage.com/login"><button>Login</button></a>
        </div>
        <div class="social-icons">
            <a href="{{ $fb }}"><i class="fab fa-facebook"></i></a>
            <!-- <a href="#"><i class="fab fa-twitter"></i></a> -->
            <a href="{{ $in }}"><i class="fab fa-instagram"></i></a>
            <a href="{{ $ld }}"><i class="fab fa-linkedin"></i></a>
            <a href="{{ $yt }}"><i class="fab fa-youtube"></i></a>
            <a href="{{ $x }}"><i class="fas fa-times"></i></a>
        </div>
    </div>
</body>

</html>
