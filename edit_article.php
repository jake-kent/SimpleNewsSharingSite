<?php
session_start();
$valid_user = isset($_SESSION['user_id']);
include 'token.php';
$csrf = new csrf();
?>
<!DOCTYPE html>
<html>
<head>
	<title>U.D.P.: Edit Article</title>
	<style type="text/css">
	.page_header #return_home {position: absolute; top: 29px; left: 20px;}
	.page_header #log_out {position: absolute; top: 29px; right: 20px;}
	#articles{position: absolute; top: 80px; width: 100%;}
	#articles ul {list-style: none; width: 98%; margin: 1%; padding: 0;}
	#articles ul .article {background-color: rgba(153, 0, 0, 0.6); border-radius: 10px; height: 100%; margin-bottom: 15px; padding: 15px 20px 20px 20px;}
	#articles .article_title {width: 50%; text-transform: capitalize; margin-right: 10px; font-weight: bold;}
	#articles .article_author {font-size: 14px; width: 100%; border-bottom: 1px solid black;}
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
		<h2>The U-Drive Daily Post: Edit Article</h2>
		<form id="log_out" action="index.php" method="POST">
			<input type="submit" name="log_out" value="Log Out">
		</form>
	</div>
	<div id="articles" class="articles">
		<?php 
		require 'validate.php';
		$edited_title = $_POST['article_title'];
		$stmt = $mysqli->prepare("select title, category, content, user_name, article_link from articles where title=?");
		$stmt->bind_param('s', $edited_title);
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->execute();

		$stmt->bind_result($title, $category, $content, $user_name, $article_link);

		$stmt->fetch();
		$cat_G = '';
		$cat_S = '';
		if($category=='G'){
			$cat_G = 'checked';
		}
		elseif($category=='S'){
			$cat_S = 'checked';
		}

		printf("\t<div id='article' class='article'><form action='submit_edit.php' method='POST'><input type='hidden' name='type_of_mod' value='edit'><input type='hidden' name='%s' value='%s'><input type='hidden' name='id' value='%s' class='article_title'>Title(*): <input type='text' name='title' value='%s' class='article_title'>Category(*): <input type='radio' name='category' value='G' %s class='article_category'>G<input type='radio' name='category' value='S' %s class='article_category'>S<div class='article_author'> Written By: %s</div>Edit the link associated with your article: <input type='text' name='article_link' value='%s' class='article_link'><br>Content(*):<textarea name='content' cols='100' rows='40' class='article_content'>%s</textarea><input type='submit' name='submit' value='Save Edits'></form></div>\n",
			$csrf->get_token_id(), $csrf->get_token(), $_POST['article_id'], $title, $cat_G, $cat_S, $user_name, $article_link, $content);
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