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
            text-align: center;
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
            display: block;
            text-align: center;
            margin-top: 10px;
        }

        .update-image-container {
            text-align: center;
            margin-top: 20px;
        }

        .update-image {
            max-width: 100%;
            height: auto;
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

        .social-icons img {
            height: 31px;
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

            .update-image {
                border-radius: 5px;
            }

            .social-icons {
                height: auto;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .social-icons a {
                margin: 0 10px;
                display: inline-block;
            }

            .social-icons img {
                height: 24px;
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
        <h1>Registration Successful</h1>
        <p style="color: #4cb96b;">Hello <?php echo $name ?>,</p>

        <p style="color: #4cb96b;">
            Congratulations on successfully completing your registration on ChoiceMarriage.com! You've taken the first step towards finding your perfect match, and we couldn't be happier to have you with us. Here's to new beginnings, exciting connections, and the journey ahead. Feel free to explore, engage, and reach out if you need any assistance. Wishing you every success in your quest for love and companionship! To find your perfect match, click the login button.
        </p>
        <p style="color: #4cb96b;">Best Regards,<br>ChoiceMarriage Team</p>

        <p style="text-align: center;">
            <a href="<?php echo $url ?>" class="cta-button">Log In Now</a>
        </p>

        <div class="social-icons">
            <a href="{{ $fb }}"><img src="https://choicemarriage.com/storage/facebook.png" alt="Facebook"></a>
            <a href="{{ $in }}"><img src="https://choicemarriage.com/storage/instagram.png" alt="Instagram"></a>
            <a href="{{ $ld }}"><img src="https://choicemarriage.com/storage/linkedin.png" alt="LinkedIn"></a>
            <a href="{{ $yt }}"><img src="https://choicemarriage.com/storage/youtube.png" alt="YouTube"></a>
            <a href="{{ $x }}"><img src="https://choicemarriage.com/storage/x.png" alt="Close"></a>
        </div>
    </div>
</body>

</html>
