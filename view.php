<?php
session_start();
$valid_user = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
	<title>U.D.P.: View Article</title>
	<style type="text/css">
	.page_header #return_home {position: absolute; top: 29px; left: 20px;}
	.page_header #log_out {position: absolute; top: 29px; right: 20px;}
	#articles{position: absolute; top: 80px; width: 100%;}
	#articles > ul {list-style: none; width: 98%; margin: 1%; padding: 0;}
	#articles > ul .article {background-color: rgba(153, 0, 0, 0.6); border-radius: 10px; height: 100%; margin-bottom: 15px; padding: 15px 5px 20px 5px;}
	#articles .article_title {text-transform: capitalize; float: left; margin-right: 10px; font-weight: bold;}
	#articles .article_author {font-size: 14px; width: 100%; border-bottom: 1px solid black;}
	#articles .article_content {margin-top: 10px; margin-left: 15px; margin-right: 15px;}
	#articles .article_actions {position: relative; bottom: -10px; border-top: 1px solid black;}
	#articles .article_actions form:nth-of-type(1) input:nth-of-type(1) {margin-left: 0px;}
	.article .comment_header {border-top: 1px black solid; margin-bottom: 0px;}
	.comments {margin: 0 0 1% 0; list-style: none; padding: 0; position: relative; top: 10px;}
	.comment {background-color: rgba(238, 221, 191, 0.7); padding: 10px; border-radius: 10px; margin-top: 5px;}
	.comment div:nth-of-type(1) {float: left; margin-right: 5px;}
	.comment div:nth-of-type(3) {margin: 10px 0; background-color: white;} 
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
		<h2>The U-Drive Daily Post: View Article</h2>
		<form id="log_out" action="index.php" method="POST">
			<input type="submit" name="log_out" value="Log Out">
		</form>
	</div>
	<div id="articles" class="articles">
		<?php 
		require 'validate.php';
		//check for link
		if(isset($_GET['article_id'])){
			$total_article_id = $_GET['article_id'];
			$check_link = $mysqli->prepare("select article_link from articles where id=?");
			$check_link->bind_param('s', $total_article_id);
			if(!$check_link){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}
			$check_link->execute();
			$check_link->bind_result($article_link);
			$check_link->fetch();
			if($article_link==null){
				$link_exists = false;
			}
			else{
				$link_exists = true;
			}
			$check_link->close();
			if($link_exists){
				header("Location: ".$article_link."");
			}
			else{
				$comments_array = array();
				$comments = $mysqli->prepare("select id, article_id, user_name, post_date, comment_content from comments where article_id=? order by id DESC");
				$comments->bind_param('s', $total_article_id);
				if(!$comments){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}
				$comments->execute();
				$comments->bind_result($comment_id, $article_id, $comment_user_name, $comment_content, $comment_time_stamp);
				while($comments->fetch()){
					if(!array_key_exists($article_id, $comments_array)){
						$comments_array[$article_id] = array();
						array_push($comments_array[$article_id], array($comment_id, $article_id, $comment_user_name, $comment_content, $comment_time_stamp));
					}
					else{
						array_push($comments_array[$article_id], array($comment_id, $article_id, $comment_user_name, $comment_content, $comment_time_stamp));
					}
				}
				$comments->close();
				$articles_with_comments = array();
				foreach($comments_array as $comments_by_article){
					foreach($comments_by_article as $single_comment){
						array_push($articles_with_comments, $single_comment[1]);
					}
				}



				$stmt = $mysqli->prepare("select id, title, category, content, user_name from articles where id='".$total_article_id."'");
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}

				$stmt->execute();

				$stmt->bind_result($id, $title, $category, $content, $user_name);


				echo "<ul>\n";
				while($stmt->fetch()){
					if($user_name==$_SESSION['user_id'] && $_SESSION['is_registered'] == 1){
						$match_user_edit = "<form action='edit_article.php' method='POST'>\n<input type='hidden' name='article_id' value='".$id."'>\n<input type='hidden' name='article_title' value='".$title."'>\n<input type='submit' name='edit' value='Edit'>\n</form>";
						$match_user_delete = "<form action='submit_edit.php' method='POST'>\n<input type='hidden' name='type_of_mod' value='delete'>\n<input type='hidden' name='article_id' value='".$id."'>\n<input type='hidden' name='article_title' value='".$title."'>\n<input type='submit' name='edit' value='Delete'>\n</form>";
					}
					else{
						$match_user_edit = "";
						$match_user_delete = "";
					}
					if($_SESSION['is_registered'] == 1){
						$match_user_comment = "<form id='submit_new_comment' action='submit_comment.php' method='POST'>\n<input type='hidden' name='article_id' value='".$id."'>\n<input type='submit' name='submit' value='Submit New Comment'>\n</form>";
					}
					else{
						$match_user_comment = "";
					}
					$articles_comments = "";
					if(in_array($id, $articles_with_comments)){
						$articles_comments .= "<h4 class='comment_header'>Comments:</h4><ul class='comments'>";
						foreach($comments_array[$id] as $temp_comment){
							$comment_to_show = "<li class='comment'>";
							$comment_to_show .= "<div> Comment By: <a href='********************************/user.php?user=".$temp_comment[2]."'>".$temp_comment[2]."</a></div>\n";
							$comment_author = $temp_comment[2];

							$comment_to_show .= "<div> Posted At: ".$temp_comment[3]."</div>\n";
							$comment_to_show .= "<div>".$temp_comment[4]."</div>\n";
							if($comment_author == $_SESSION['user_id'] && $_SESSION['is_registered'] == 1){
								$comment_id = $temp_comment[0];
								$comment_to_show .= "<form action='edit_comment.php' method='POST'>\n<input type='hidden' name='comment_id' value='".$comment_id."'>\n<input type='hidden' name='comment_author' value='".$temp_comment[2]."'>\n<input type='hidden' name='comment_content' value='".$temp_comment[4]."'>\n<input type='submit' name='edit' value='Edit'>\n</form>";
								$comment_to_show .= "<form action='comment_mod.php' method='POST'>\n<input type='hidden' name='type_of_mod' value='delete'>\n<input type='hidden' name='comment_id' value='".$comment_id."'><input type='submit' name='edit' value='Delete'>\n</form>";
							}
							$comment_to_show .= "</li>";
							$articles_comments .= $comment_to_show;
						}
						$articles_comments .= "</ul>";
					}
					else{
						$articles_comments .= "<h4 class='comment_header'>Comments:</h4><ul class='comments'>";
						$articles_comments .+ "No One Has Commented On This Article Yet.";
						$articles_comments .= "</ul>";
					}
					printf("\t<li id='article-%s' class='article'>\n<div class='article_title'>%s</div>\n<div class='article_category'> (%s)</div>\n<div class='article_author'> Written By: <a href='********************************/user.php?user=%s'>%s</a></div>\n<div class='article_content'>%s</div>\n<div class='article_actions'>Your Actions:<div class='article_edit'>%s</div><div class='article_delete'>%s</div><div class='article_comment'>%s</div></div>\n%s</li>\n",
						$id, $title, $category, $user_name, $user_name, nl2br($content), $match_user_edit, $match_user_delete, $match_user_comment, $articles_comments);
				}
				echo "</ul>\n";

				$stmt->close();
			}
		}
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