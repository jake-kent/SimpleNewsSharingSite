<?php
require 'validate.php';

$user = $_POST['username'];
$pass_guess = $_POST['password'];
if($user == null || $pass_guess == null){
	header("Location: ********************************/index.php?login=invalid");
}
else{
	$stmt = $mysqli->prepare("SELECT COUNT(*), user_name, password, registered FROM users WHERE user_name=?");

	$stmt->bind_param('s', $user);

	$stmt->execute();

	$stmt->bind_result($cnt, $user_id, $pwd_hash, $registered);

	$stmt->fetch();

	$stmt->close();
	if( $cnt == 1 && crypt($pass_guess, $pwd_hash)==$pwd_hash){
		session_start();
		$_SESSION['user_id'] = $user_id;
		$_SESSION['is_registered'] = $registered;
		header("Location: ********************************/home.php");
	}else{
		header("Location: ********************************/index.php?login=failed");
	}
}

?>