<?php
session_start();
require 'validate.php';
include 'token.php';
$csrf = new csrf();
if(isset($_POST['type_of_mod'])){
	if($_POST['type_of_mod'] == 'edit'){
		if($csrf->check_valid_token('post')){
			$comment_id = $_POST['id'];
			$username = $_SESSION['user_id'];
			$edited_content = $_POST['content'];
			$date = new DateTime("NOW");
			$post_date = $date->format('Y-m-d H:i:s');
			if($edited_content == null){
				echo "You forgot to fill out the content field of your comment. Please click return to the homepage and try to edit the comment again.";
				echo "<br>";
				echo "<a href='********************************/home.php'>Return to Home Page</a>";
			}
			else{
				$stmt = $mysqli->prepare("update comments set comment_content='?', post_date='?' where id='?'");
				$stmt->bind_param('sss', $edited_content, $post_date, $comment_id);
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}

				$stmt->execute();

				$stmt->close();
				header("Location: ********************************/home.php#article-".$article_id."");
			}
		}
	}
	if($_POST['type_of_mod'] == 'delete'){
		if($csrf->check_valid_token('post')){
			echo "in delete";
			$comment_id = $_POST['comment_id'];
			echo $comment_id;
			$stmt_comm = $mysqli->prepare("DELETE FROM comments WHERE id=?");
			$stmt_comm->bind_param('s', $comment_id);
			if(!$stmt_comm){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}

			$stmt_comm->execute();
			if($stmt_comm){
				echo "executed";
			}

			$stmt_comm->close();
			header("Location: ********************************/home.php");
		}
	}
	if($_POST['type_of_mod'] == 'new'){
		if($csrf->check_valid_token('post')){
			$comment_id = null;
			$article_id = $_POST['article_id'];
			$comment_author = $_SESSION['user_id'];
			$comment_content = $_POST['content'];
			if($comment_content == null){
				echo "You forgot to fill out the content field of your comment. Please click return to the homepage and try to submit your comment again.";
				echo "<br>";
				echo "<a href='********************************/home.php'>Return to Home Page</a>";
			}
			else{
				$stmt = $mysqli->prepare("INSERT INTO comments (id, article_id, user_name, comment_content, post_date) VALUES (?, ?, ?, ?, NOW())");
				$stmt->bind_param('ssss', $comment_id, $article_id, $comment_author, $comment_content);
				if(!$stmt){
					printf("Query Prep Failed: %s\n", $mysqli->error);
					exit;
				}

				$stmt->execute();

				$stmt->close();
				header("Location: ********************************/home.php");
			}
		}
	}
}

?>