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

function logout() {
	unset($_SESSION['current_user_name']);
	unset($_SESSION['current_user_id']);
}

function user_id() {
	return $_SESSION['current_user_id'];
}

function user_name() {
	return $_SESSION['current_user_name'];
}
/*THESE FUNCTIONS KEEP RETURNING NULL, I HAVE NO IDEA WHY*/
function get_uid($username) {
	$wildbook = connect_wildbook();
	$get_uid = $wildbook->prepare("SELECT `uid` FROM `user` WHERE `username` = ?");
	$get_uid->bind_param("s", $username);
	$get_uid->execute();
	$get_uid->bind_result($uid);
	$get_uid->fetch();
	return ($uid);
}

function get_username($uid) {
	$wildbook = connect_wildbook();
	$get_username = $wildbook->prepare("SELECT `username` FROM `user` WHERE `uid`= ?");
	$get_username->bind_param("i", $uid);
	$get_username->execute();
	$get_username->bind_result($username);
	$get_username->fetch();
	return ($username);
}
/*checks if other user already sent friend request*/
function check_req($firstuid,$seconduid) {	 
	$wildbook = connect_wildbook();
	$chk_req = $wildbook->prepare("SELECT 1 FROM `request` WHERE `requester` = ? and `requestee` = ?;");
	if (!$chk_req) {
		echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
	}
	$chk_req->bind_param("ii", $seconduid, $firstuid);	
	if (!$chk_req->execute())
		echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;	
	return($chk_req->fetch());
}
/*checks if the users are friends*/
function check_friend($firstuid,$seconduid) {
	$wildbook = connect_wildbook();
	$chk_friend = $wildbook->prepare("SELECT 1 FROM `accepted_friends` WHERE `firstuid` = ? and `seconduid` = ?;");
	if(!$chk_friend)
		echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
	$chk_friend->bind_param("ii", $seconduid, $firstuid);
	if (!$chk_friend->execute())
		echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
	return($chk_friend->fetch());		
}



?>
