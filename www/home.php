<?php
	include_once "template.php";
	begin_page("Home");
?>
<h4>Home</h4>
<?php 
	echo "<a href=\"logout.php\">Logout</a> <br><br><br>";
	$username = $_SESSION["current_user_name"];
	echo "Welcome $username!<br><br><br>";
?>
<form action="profile.php" method="get">
username: <input name="search" type="text" maxlength="30"/>
<input type="submit" />
</form>

<?php

	echo "Friend Requests <br> ------------------------------------------------- <br>";
	$wildbook = connect_wildbook();
	$req_query = $wildbook->prepare("SELECT `requester` FROM `request` WHERE `requestee` = ?;");
	$req_query->bind_param("i", $_SESSION["current_user_id"]);
	$req_query->execute();
	$req_query->bind_result($reqid);
	while ($req_query->fetch()) {
		$request = get_username($reqid);		
		echo "<a href=\"profile.php?search=$request\">$request</a> <br>"; 	
	}
	
	echo "Your Friends <br> ------------------------------------------------- <br>";
	$friend_query = $wildbook->prepare("SELECT `username` FROM `user` WHERE `uid` = (SELECT `seconduid` FROM `accepted_friends` WHERE `firstuid` = ?);");
	$uid = user_id();
	$friend_query->bind_param("i", $uid);
	$friend_query->execute();
	$friend_query->bind_result($username);
	while ($friend_query->fetch()) {
		echo "<a href=\"profile.php?search=$username\">$username</a> <br>";
	}

	echo "Add a Diary Post <br>";
	display_diary_post_submission_form($uid);

	echo "Your timeline <br> ---------------------------------------------<br> ";
	$diary_query = $wildbook->prepare('CALL timeline(?)');

	$diary_query->bind_param("i", $uid);
	$diary_query->execute();
	$diary_query->store_result();
	$diary_query->bind_result($did, $postername, $posteename, $title, $timestamp, $content);
	while ($diary_query->fetch()) {
		display_diary_post($did, $postername, $posteename, $title, $timestamp, $content);
		echo "---------------------------------------------<br>";
	}
	$wildbook->close();
	end_page();
?>