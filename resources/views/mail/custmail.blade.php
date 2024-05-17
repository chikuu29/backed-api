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
            margin: 0 auto;
            width: 150px;
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
    </style>
</head>

<body>
    <?php
    //echo ';;;';
    //dd(html_entity_decode(htmlspecialchars($messagedata)))

    ?>
    <div class="container">
        <img src="{{ $imageurl }}" alt="Your Logo" class="logo">
        <h1>{{ $Subject }}</h1>
        <p>Dear User,</p>
        <div>
            <?php echo html_entity_decode(htmlspecialchars($messagedata)); ?>
        </div>
        <!-- Footer with icons -->
        <div class="footer-icons">
            <a href="{{$fb}}"><i class="fab fa-facebook" style="font-size: 30px;"></i></a>
            <a href="{{$in}}"><i class="fab fa-instagram" style="font-size: 30px;"></i></a>
            <a href="{{$ld}}"><i class="fab fa-linkedin" style="font-size: 30px;"></i></a>
            <a href="{{$yt}}"><i class="fab fa-youtube" style="font-size: 30px;"></i></a>
            <a href="{{$x}}"><i class="fas fa-times" style="font-size: 30px;"></i></a>
        </div>
    </div>
</body>

</html>
