<?php

function connect_wildbook() {
	$wildbook = new mysqli('localhost','root','password', 'wildbook');
	if (!$wildbook) { 
		echo('Could not connect to MySQL: ' . $wildbook->connect_errno);
	} 
	return $wildbook;
}

function user_exists($username) {
	$wildbook = connect_wildbook();
	$duplicate_check = $wildbook->prepare("SELECT 1 FROM `user` WHERE `username` = ?");
	$duplicate_check->bind_param("s", $username);
	$duplicate_check->execute();
	return ($duplicate_check->fetch());
}

function login($username, $uid) {
	$_SESSION['current_user_name'] = $username;
	$_SESSION['current_user_id'] = $uid;
}


?>
