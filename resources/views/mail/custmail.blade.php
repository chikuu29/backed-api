<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $Subject }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        /* Add your custom styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        p {
            text-align: left;
        }

        .logo {
            display: block;
            width: 100%;
            height: 175px;
        }

        .footer-icons {
            text-align: center;
            margin-top: 20px;
        }

        .footer-icons a {
            display: inline-block;
            margin: 0 10px;
            color: #666666;
            text-decoration: none;
        }

        .footer-icons a:hover {
            color: #000000;
        }

        p img {
            height: 100px;
            width: 200px;
        }

        .social-icons {
            text-align: center;
            margin-top: 20px;
            background-image: url({{ $foter }});
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

        /* Responsive styles */
        @media only screen and (max-width: 600px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 15px;
            }



            p img {
                height: auto;
                width: 100%;
                max-width: 200px;
            }

            .social-icons {
                height: auto;
                padding: 15px;
            }

            .social-icons img {
                height: 24px;

            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div>
            <img src="{{ $imageurl }}" alt="Your Logo" class="logo">
        </div>

        <h1>{{ $Subject }}</h1>
        <p>Dear, {{ $name }}</p>
        <div>
            <?php echo html_entity_decode(htmlspecialchars($messagedata)); ?>
        </div>
        <img src="{{ $imagepath }}" alt="event" style="<?php echo $imagepath == '200'? 'display: none;': 'display: block;' ?>">
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
