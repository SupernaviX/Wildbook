<?php

function connect_wildbook() {
	$connections = parse_ini_file(realpath($_SERVER["DOCUMENT_ROOT"] . "/../database/connections.ini"), true);
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
	$exists = $duplicate_check->fetch();
	$wildbook->close();
	return $exists;
}

function get_lid($lname) {
	$wildbook = connect_wildbook();
	$duplicate_check = $wildbook->prepare("SELECT `lid` FROM `location` WHERE `lname` = ?");
	$duplicate_check->bind_param("s", $lname);
	$duplicate_check->execute();
	$duplicate_check->bind_result($lid);
	$duplicate_check->fetch();
	$wildbook->close();
	return $lid;
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
	$wildbook->close();
	return $uid;
}

function get_username($uid) {
	$wildbook = connect_wildbook();
	$get_username = $wildbook->prepare("SELECT `username` FROM `user` WHERE `uid`= ?");
	$get_username->bind_param("i", $uid);
	$get_username->execute();
	$get_username->bind_result($username);
	$get_username->fetch();
	$wildbook->close();
	return $username;
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
	$req = $chk_req->fetch();
	$wildbook->close();
	return $req;
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
	$friend = $chk_friend->fetch();
	$wildbook->close();
	return $friend;		
}

/*returns if a post is visible to viewer (firstuid is viewer, seconduid is owner of post/friendship, privacy is set by seconduid */
function visible($firstuid,$seconduid,$privacy) {
	if($privacy == 1) {	// private
		if ($firstuid != $seconduid) return false;
		else return true;
	}
	else if ($privacy == 2) {	// visible to friends only
		if(check_friend($firstuid,$seconduid) or $firstuid == $seconduid) return true;
		else return false;
	}
	else if ($privacy == 3) {
		$wildbook = connect_wildbook();
		$fof_query = $wildbook->prepare("SELECT 1 FROM `fof` WHERE `firstuid` = ? and `seconduid`= ?;");
		if(!$fof_query) echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
		$fof_query->bind_param("ii",$firstuid,$seconduid);
		if (!$fof_query->execute()) echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
		$fof = $fof_query->fetch();
		$wildbook->close();
		if($fof or check_friend($firstuid,$seconduid) or $firstuid == $seconduid) return true;
		else return false;
	}
	else {return true;}	//visible to everyone
}
/*returns the distance between two users*/
function distance($firstuid, $seconduid) {
	$wildbook = connect_wildbook();
	$fof_query = $wildbook->prepare("SELECT 1 FROM `fof` WHERE `firstuid` = ? and `seconduid`= ?;");
	$fof_query->bind_param("ii",$firstuid,$seconduid);
	$fof_query->execute();
	$fof = $fof_query->fetch();
	$wildbook->close();
	
	if ($firstuid == $seconduid) return 1;
	else if (check_friend($firstuid,$seconduid)) return 2;
	else if ($fof) return 3;
	else return 4;
}
?>
