<?php
session_start();
include 'token.php';
$csrf = new csrf();
require 'validate.php';
if(isset($_POST['type_of_mod'])){
	if($_POST['type_of_mod'] == 'edit'){
		if($csrf->check_valid_token('post')){
			$article_id = $_POST['id'];
			$edited_title = $_POST['title'];
			$username = $_SESSION['user_id'];
			$edited_category = $_POST['category'];
			$article_link = $_POST['article_link'];
			$edited_content = $_POST['content'];
			if($edited_title == null || $edited_category == null || $edited_content == null){
				echo "You forgot to fill out one of the required fields. Please click return to the homepage and try to edit the article again.";
				echo "<br>";
				echo "<a href='********************************/home.php#article-".$article_id."'>Return to Article</a>";
			}
			else{
				$date = new DateTime("NOW");
				$post_date = $date->format('m/d/y');
				$stmt = $mysqli->prepare("UPDATE articles SET title=?, category=?, content=?, article_link=?, post_date=? WHERE id=?");
				$stmt->bind_param('ssssss', $edited_title, $edited_category, $edited_content, $article_link, $post_date, $article_id);
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
			$article_id = $_POST['article_id'];
			$username = $_SESSION['user_id'];
			$stmt_comm = $mysqli->prepare("DELETE FROM comments WHERE article_id=?");
			$stmt_comm->bind_param('s', $article_id);
			if(!$stmt_comm){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}

			$stmt_comm->execute();
			if($stmt_comm){
				echo "executed";
			}

			$stmt_comm->close();

			$stmt_art = $mysqli->prepare("DELETE FROM articles WHERE id=?");
			$stmt_art->bind_param('s', $article_id);
			if(!$stmt_art){
				printf("Query Prep Failed: %s\n", $mysqli->error);
				exit;
			}

			$stmt_art->execute();
			if($stmt_art){
				echo "executed";
			}

			$stmt_art->close();
			header("Location: ********************************/home.php");
		}
	}
	if($_POST['type_of_mod'] == 'new'){
		if($csrf->check_valid_token('post')){
			$article_title = $_POST['title'];
			$article_category = $_POST['category'];
			$article_author = $_SESSION['user_id'];
			$article_link = $_POST['article_link'];
			$article_content = $_POST['content'];
			$article_id = null;
			if($article_title == null || $article_category == null || $article_content == null){
				echo "You forgot to fill out one of the required fields. Please click return to the homepage and try to submit your article again.";
				echo "<br>";
				echo "<a href='********************************/home.php'>Return to Article</a>";
			}
			else{
			$date = new DateTime("NOW");
			$post_date = $date->format('m/d/y');
			$stmt = $mysqli->prepare("INSERT INTO articles (id, title, category, content, user_name, article_link, post_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param('sssssss', $article_id, $article_title, $article_category, $article_content, $article_author, $article_link, $post_date);
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