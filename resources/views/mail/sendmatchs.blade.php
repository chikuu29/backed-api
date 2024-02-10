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
			text-align: center;
		}

		.company-logo {
			max-width: 100%;
			height: auto;
		}

		.match-card {
			display: flex;
			border: 1px solid #ddd;
			margin-bottom: 20px;
			border-radius: 8px;
		}

		.profile-image {
			width: 150px;
			/* Set the width as needed */
			height: auto;
			border-radius: 8px 0 0 8px;
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
			/* Adjust the size of social media icons */
			height: auto;
		}
	</style>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
		integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
		crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
	<div class="container">
		<div class="header">
			<img src="https://admin.choicemarriage.com/api/storage/logo_image/<?php echo $logo ?>" alt="Company Logo" class="company-logo">
			<h1>Your Matchmaking Service</h1>
		</div>
		<p>Hello <?php echo $name ?>,</p>
		<p>We've found some potential matches for you. Take a look!</p>

		<!-- Match 1 -->
		<?php foreach($Alluser as $data){ ?>
		<div class="match-card">
			<img src="https://admin.choicemarriage.com/api/storage/<?php echo $data->user_profile_image ?>" alt="Match 1" class="profile-image">
			<div class="match-details">
				<h5 ><?php echo $data->user_id ;  ?></h5>
							<?php
				$dob=$data->user_dob;
				$diff = (date('Y') - date('Y',strtotime($dob)));
				
			?>
				<p>Age: <?php echo $diff; ?></p>
				<p>Location: <?php echo $data->user_Permanent_state .','.$data->user_Permanent_city ; ?></p>
				<p>Occupation:  <?php echo $data->user_employed_In ; ?> </p>
				<a href="https://choicemarriage.com/member-profile/<?php echo $data->user_id ?>" class="cta-button">View Profile</a>
			</div>
		</div>
		<?php } ?>

		

		<!-- Add more match cards as needed -->

		<p>Don't miss out on these potential matches. Log in to your account to explore further.</p>

	  <!--<div class="footer">
			<p>Best Regards,<br>Your Matchmaking Team</p>
			<div class="social-icons">
				<a href="SocialMediaLink1" target="_blank"><i class="fa-brands fa-facebook"></i></a>
				<a href="SocialMediaLink2" target="_blank"><i class="fa-brands fa-twitter"></i></a>
				 Add more social media links and icons as needed 
			</div>
		</div> -->
	</div>
</body>

</html>

