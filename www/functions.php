<?php

function connect_wildbook() {
	$connections = parse_ini_file(realpath("../database/connections.ini"), true);
	$connection_name = $connections["connections"]["use"];
	$connection = $connections[$connection_name];
	$wildbook = new mysqli($connection["host"], $connection["username"], $connection["passwd"], $connection["dbname"]);
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
