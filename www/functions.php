<?php

function connect_wildbook() {
	$link = mysql_connect('localhost','root','password'); // This should use mysqli
	if (!$link) { 
		die('Could not connect to MySQL: ' . mysql_error()); 
	} 
	$wildbook = mysql_select_db('wildbook',$link) or die("Could not select wildbook" . mysql_error());
	return $link;
}

function user_exists($username) {
	$user_query = mysql_query("SELECT username from user") 
		or die('Invalid query: ' . mysql_error());
	while($row = mysql_fetch_array($user_query)) {
		$tempuser = $row['username'];
		if($tempuser == $username) {
			return true;
		}
	}
	return false;
}


?>
