<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Matchmaking Service</title>
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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .header img {
            max-width: 150px;
            height: auto;
            border-radius: 10px;
        }

        .company-logo {
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

        .update-image-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .update-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .match-card {
            display: flex;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            border-radius: 8px;
            flex-wrap: wrap;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }

        .match-details {
            flex: 1;
            padding: 10px;
        }

        .match-details h2 {
            margin-top: 0;
        }

        .cta-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4cb96b;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .social-icons a {
            display: inline-block;
            margin: 0 10px;
        }

        .social-icons img {
            width: 30px;
            height: auto;
        }

        .login-btn {
            text-align: center;
            margin: 20px 0;
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-image {
                border-radius: 8px 0 0 8px;
                max-width: 100%;
            }

            .match-card {
                flex-direction: column;
            }

            .match-details {
                padding-top: 0;
            }
        }

        /* Custom styles for paragraphs */
        .highlight-green {
            color: #4cb96b;
        }

        .large-text {
            font-size: 18px;
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
        <p class="highlight-green large-text">Hello <?php echo $name ?>,We've found some potential matches for you. Take a look!</p>
        <!-- Match 1 -->
        <?php foreach ($Alluser as $data) { ?>
            <div class="match-card">
                <img src="https://choicemarriage.com/storage/<?php echo $data->user_profile_image ?>" alt="Match 1" class="profile-image" width="150" height="150">
                <div class="match-details">
                    <h5 style="color: red;"><?php echo $data->user_id;  ?></h5>
                    <?php
                    $dob = $data->user_dob;
                    $diff = (date('Y') - date('Y', strtotime($dob)));
                    ?>
                    <p><span style="color: red;">Age:</span> <span style="color: green;"><?php echo $diff; ?></span></p>
                    <p><span style="color: red;">Location:</span> <span style="color: green;"><?php echo $data->user_Permanent_state . ',' . $data->user_Permanent_city; ?></span></p>
                    <p><span style="color: red;">Occupation:</span> <span style="color: green;"><?php echo $data->user_employed_In; ?></span> </p>
                    <a href="https://choicemarriage.com/member-profile/<?php echo $data->user_id ?>" class="cta-button">View Profile</a>
                </div>
            </div>
        <?php } ?>

        <p class="highlight-green large-text">Don't miss out on these potential matches. Log in to your account to explore further.</p>

        <div class="login-btn">
            <a href="https://choicemarriage.com/login" class="cta-button">Login</a>
        </div>
        <div class="social-icons">
            <a href="{{ $fb }}"><img style="height: 31px;" src="https://choicemarriage.com/storage/facebook.png" alt="Facebook"></a>
            <a href="{{ $in }}"><img style="height: 31px;" src="https://choicemarriage.com/storage/instagram.png" alt="Instagram"></a>
            <a href="{{ $ld }}"><img style="height: 31px;" src="https://choicemarriage.com/storage/linkedin.png" alt="LinkedIn"></a>
            <a href="{{ $yt }}"><img style="height: 31px;" src="https://choicemarriage.com/storage/youtube.png" alt="YouTube"></a>
            <a href="{{ $x }}"><img style="height: 31px;" src="https://choicemarriage.com/storage/x.png" alt="Close"></a>
        </div>
    </div>
</body>

</html>
