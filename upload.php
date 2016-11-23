<?php
session_start();
include 'token.php';
require 'validate.php';
$csrf = new csrf();
if($csrf->check_valid_token('post')){

// Get the filename and make sure it is valid
	$filename = str_replace(' ', '_', basename($_FILES['userfile']['name']));
	echo $filename;
	if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
		echo "Invalid filename";
		exit;
	}

// Get the username and make sure it is valid
	$username = $_POST['username'];
	if( !preg_match('/^[\w_\-]+$/', $username) ){
		echo "Invalid username";
		exit;
	}

	switch ($_FILES['userfile']['error']) {
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
		echo "File too large: max size 20 MB";
		exit;
	}

	$full_path = sprintf("********************************/user_images/%s", $filename);

	if( move_uploaded_file($_FILES['userfile']['tmp_name'], $full_path) ){
		chmod($full_path, 0777);
		echo "upload success";
		$short_path = "user_images/".$filename."";
		$stmt = $mysqli->prepare("UPDATE users SET profile_image=? WHERE user_name=?");
		$stmt->bind_param('ss', $short_path, $username);
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->execute();

		$stmt->close();
		header("Location: ********************************/user.php?user=".$username);
		exit;
	}else{
		echo "failure";
		exit;
	}
}
?>