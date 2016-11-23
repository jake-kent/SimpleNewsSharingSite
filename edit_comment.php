<?php
session_start();
$valid_user = isset($_SESSION['user_id']);
include 'token.php';
$csrf = new csrf();
?>
<!DOCTYPE html>
<html>
<head>
	<title>U.D.P.: Edit Comment</title>
	<style type="text/css">
	.page_header #return_home {position: absolute; top: 29px; left: 20px;}
	.page_header #log_out {position: absolute; top: 29px; right: 20px;}
	.comments {margin: 1% 0; list-style: none; padding: 0; position: absolute; top: 80px; width: 100%;}
	.comment {background-color: rgba(238, 221, 191, 0.7); padding: 10px; border-radius: 10px; margin-top: 5px;}
	.comment .comment_author {float: left; margin-right: 5px;}
	.comment input:nth-of-type(2) {background-color: rgba(153, 0, 0, 0.6);}
	.comment textarea {margin-top: 10px;} 
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
		<h2>The U-Drive Daily Post: Edit Comment</h2>
		<form id="log_out" action="index.php" method="POST">
			<input type="submit" name="log_out" value="Log Out">
		</form>
	</div>
	<div class="comments">
		<?php 
		require 'validate.php';
		$comment_id = $_POST['comment_id'];
		$stmt = $mysqli->prepare("select user_name, comment_content from comments where id=?");
		$stmt->bind_param('s', $comment_id);
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->execute();

		$stmt->bind_result($username, $comment_content);

		$stmt->fetch();

		printf("\t<div class='comment'><form action='comment_mod.php' method='POST'><input type='hidden' name='type_of_mod' value='edit'>\n<input type='hidden' name='".$csrf->get_token_id()."' value='".$csrf->get_token()."'><input type='hidden' name='id' value='%s'><div class='comment_author'> Written By: %s</div><br>Content(*):<br><textarea name='content' cols='100' rows='40'>%s</textarea><input type='submit' name='submit' value='Save Edits'></form></div>\n",
			$comment_id, $username, $comment_content);

		$stmt->close();
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