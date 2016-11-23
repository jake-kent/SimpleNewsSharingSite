<?php
session_start();
$valid_user = isset($_SESSION['user_id']);
if(isset($_SESSION['is_registered'])){
	$registered = $_SESSION['is_registered'];
}
include 'token.php';
$csrf = new csrf();
if(isset($_GET['user'])){
	$page_user_name = $_GET['user'];
	if($page_user_name == null){
		$valid_page_user_arg = false;
	}
	else{
		$valid_page_user_arg = true;
	}
}
else{
	$page_user_name = "Invalid User";
	$valid_page_user_arg = false;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>U.D.P.: <?php echo $page_user_name; ?> User Page</title>
	<style type="text/css">
	.page_header #return_home {position: absolute; top: 29px; left: 20px;}
	.page_header #log_out {position: absolute; top: 29px; right: 20px;}
	.invalid_user, .invalid_page_user_arg {position: absolute; top: 80px;}
	.invalid_page_user_name_arg {position: absolute; top: 0px; width: 50vw;}
	.user_info{position: absolute; top: 80px; margin: 2%; padding: 2%; background-color: beige; width: 91%; border-radius: 40px;}
	.user_info > h3{float: left; width: 100%; margin-bottom: 0px;}
	.user_info .user_id_card .profile_picture {width: 150px; height: 150px; float: left;}
	.user_info .user_id_card .profile_picture > img {border-radius: 10px;}
	.user_info .user_id_card .user_data {float: left; list-style: none; width: 80%; height: 120px;}
	.user_info .user_id_card .user_data > li{margin-bottom: 4px;}
	.user_info .user_id_card .user_data > .profile_picture_upload > form{margin-top: 20px;}
	.user_info .user_id_card .user_data > .profile_picture_upload > form .upload_prompt{border: 1px black solid; padding: 5px;}
	.user_info .authored_articles {float: left;}
	.user_info .authored_articles li > a > h4{margin-bottom: 10px;}
	.user_info .authored_articles li > h5{margin-top: 10px;}


	</style>
	<link rel="stylesheet" type="text/css" href="header.css">
</head>
<body>
	<?php if($valid_user) : ?>
	<div class="page_header">
		<form id="return_home" action="home.php" method="POST">
			<input type="submit" name="submit" value="Home">
		</form>
		<h2>The U-Drive Daily Post: <?php echo $page_user_name; ?> User Page</h2>
		<form id="log_out" action="index.php" method="POST">
			<input type="submit" name="log_out" value="Log Out">
		</form>
	</div>
	<?php if($valid_page_user_arg) : ?>
	<div class="user_info">
		<?php
		require 'validate.php';
		$stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE user_name=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $page_user_name);

		$stmt->execute();

		$stmt->bind_result($num_users);

		$stmt->fetch();

		$stmt->close();
		if($num_users == 1){
			$user_exists = true;
		}
		else{
			$user_exists = false;
		}
		?>
		<?php if($user_exists) : ?>
		<?php
		$stmt = $mysqli->prepare("SELECT first_name, last_name, profile_image FROM users WHERE user_name=?");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $page_user_name);

		$stmt->execute();

		$stmt->bind_result($profile_first_name, $profile_last_name, $profile_image);

		$stmt->fetch();

		$stmt->close();
		$profile_user_name = $page_user_name;
		if($page_user_name == $_SESSION['user_id']){
			$match_user = true;
		}
		else{
			$match_user = false;
		}
		?>
		<div class="user_id_card">
			<div class="profile_picture">
				<img src="<?php echo $profile_image; ?>" alt="Profile Picture" height="150" width="150">
			</div>
			<ul class="user_data">
				<li class="first_name">First Name: <?php echo $profile_first_name; ?></li>
				<li class="last_name">Last Name: <?php echo $profile_last_name; ?></li>
				<li class="user_name">Username: <?php echo $profile_user_name; ?></li>
				<?php if($match_user) : ?>
				<li class="profile_picture_upload">
					<form enctype="multipart/form-data" action="upload.php" method="POST">
						<input type="hidden" name="username" value="<?php echo $_SESSION['user_id']; ?>" />
						<input type="hidden" name="<?php echo $csrf->get_token_id(); ?>" value="<?php echo $csrf->get_token(); ?>" />
						Upload Profile Picture: <input class="upload_prompt" type="file" name="userfile" />
						<input type="submit" value="Upload" />
					</form>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<h3>Authored Articles:</h3>
	<ul class="authored_articles">
		<?php
		$stmt = $mysqli->prepare("SELECT id, title, post_date FROM articles WHERE user_name=? ORDER BY id DESC");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}
		$stmt->bind_param('s', $page_user_name);

		$stmt->execute();

		$stmt->bind_result($article_id, $article_title, $article_datetime);

		while($stmt->fetch()){
			printf("\t<li><a href='********************************/view.php?article_id=%s'><h4>%s</h4></a><h5>Written at: %s</h5></li>\n",
				$article_id, $article_title, $article_datetime);
		}
		$stmt->close();
		?>
	</ul>
<?php else : ?>
	<div class="invalid_page_user_name_arg">
		<h3> You have an entered a username that does not exist in our system. </h3>
		<h4> Please verify that you have entered the username correctly (<?php echo $page_user_name; ?>) </h4>
	</div>
<?php endif; ?>
</div>
<?php else : ?>
	<div class="invalid_page_user_arg">
		<h3> You have an invalid url syntax for the user page </h3>
		<h4> Please be sure to structure the user page url as follows: http://52.2.151.227/~kentjakel/module_3/CSE330-Module3-Jake-Kent-432101-Hannah-Mehrle-429406/user.php?user=(username) </h4>
	</div>
<?php endif; ?>
<?php else : ?>
	<div class="page_header">
		<h2>The U-Drive Daily Post</h2>
	</div>
	<div class="invalid_user">
		<h4> User Not Logged In </h4>
		<a href="********************************/">Please Return To The Login Page</a>
	</div>
<?php endif; ?>
</body>
</html>