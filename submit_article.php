<?php
session_start();
$valid_user = isset($_SESSION['user_id']);
include 'token.php';
$csrf = new csrf();
?>
<!DOCTYPE html>
<html>
<head>
	<title>U.D.P.: Submit Article</title>
	<style type="text/css">
	.page_header #return_home {position: absolute; top: 29px; left: 20px;}
	.page_header #log_out {position: absolute; top: 29px; right: 20px;}
	#articles{position: absolute; top: 80px; width: 100%;}
	#articles ul {list-style: none; width: 98%; margin: 1%; padding: 0;}
	#articles ul .article {background-color: rgba(153, 0, 0, 0.6); border-radius: 10px; height: 100%; margin-bottom: 15px; padding: 15px 20px 20px 20px;}
	#articles .article_title {width: 50%; text-transform: capitalize; margin-right: 10px; font-weight: bold;}
	#articles .article_author {font-size: 14px; width: 100%;}
	#articles .article_link {width: 30%;}
	#articles .article_content {margin-top: 10px; width: 98%;}
	.invalid_user {position: absolute; top: 80px;}
	</style>
	<link rel="stylesheet" type="text/css" href="header.css">
</head>
<body>
	<?php if($valid_user) : ?>
	<div class="page_header">
		<form id="return_home" action="home.php" method="POST">
			<input type="submit" name="submit" value="Home">
		</form>
		<h2>The U-Drive Daily Post: Submit Article</h2>
		<form id="log_out" action="index.php" method="POST">
			<input type="submit" name="log_out" value="Log Out">
		</form>
	</div>
	<div id="articles" class="articles">
		<?php 
		require 'validate.php';
		$user_name = $_SESSION['user_id'];

		printf("\t<div id='article' class='article'><form action='submit_edit.php' method='POST'><input type='hidden' name='type_of_mod' value='new'><input type='hidden' name='%s' value='%s'>Title(*): <input type='text' name='title' value='' class='article_title'>Category(*): <input type='radio' name='category' value='G' class='article_category' checked>G<input type='radio' name='category' value='S' class='article_category'>S<div class='article_author'> Written By: %s</div><input type='hidden' name='article_author' value='%s'>Associate a link with your article: <input type='text' name='article_link' class='article_link'><br>Content(*):<textarea name='content' cols='100' rows='40' class='article_content'></textarea><input type='submit' name='submit' value='Submit Article'></form></div>\n",
			$csrf->get_token_id(), $csrf->get_token(), $user_name, $user_name);
		?>
	</div>
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