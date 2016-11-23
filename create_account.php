<?php
require 'validate.php';

$first = $_POST['first_name'];
$last = $_POST['last_name'];
$user = $_POST['user_name'];
$plain_pass = $_POST['password'];
if(isset($_POST['request_reg'])){
	$r_r = $_POST['request_reg'];
}
else{
	$r_r = "off";
}

if($first == null || $last == null || $user == null || $plain_pass == null){
	header("Location: ********************************/new_user.php?signup=invalid");
}
else{
	if($r_r = "on"){
		$request_reg = 1;
	}
	else{
		$request_reg = 0;
	}
	$pass = crypt($plain_pass);
	$dupe = $mysqli->prepare("select count(*) from users where user_name='?'");
	$dupe->bind_param('s', $user);
	if(!$dupe){
		printf("Query Prep Failed: %s\n", $mysqli->error);
		exit;
	}

	$dupe->execute();
	$dupe->bind_result($num_users);
	$dupe->close();
	if($num_users > 0){
		$duplicate_user = true;
	}
	else{
		$duplicate_user = false;
	}

	if($duplicate_user){
		header("Location: ********************************/new_user.php?signup=dupe");
	}
	else{
		$stmt = $mysqli->prepare("insert into users (first_name, last_name, user_name, password, request_registered) values (?, ?, ?, ?, ?)");
		if(!$stmt){
			printf("Query Prep Failed: %s\n", $mysqli->error);
			exit;
		}

		$stmt->bind_param('sssss', $first, $last, $user, $pass, $request_reg);

		$stmt->execute();

		$stmt->close();

		header("Location: ********************************/");
	}
}
?>