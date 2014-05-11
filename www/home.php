<?php
	include_once "template.php";
	begin_page("Home");
?>
<h4>Home</h4>

<form action="profile.php" method="post"> 
username: <input name="search_username" type="text" maxlength="30"/> 
<input type="submit" />
</form>

<?php
	echo "Your Friends <br> ------------------------------------------------- <br>";
	$wildbook = connect_wildbook();
	$friend_query = $wildbook->prepare("SELECT `username` FROM `user` WHERE `uid` = (SELECT `seconduid` FROM `friend` WHERE `firstuid` = ?);");
	$friend_query->bind_param("i", user_id());
	$friend_query->execute();
	$friend_query->bind_result($username);
	while ($friend_query->fetch()) {
		echo "$username <br>";
	}

	echo "Add a Diary Post <br>";
?>

<form enctype="multipart/form-data" action="adddiarypost.php" method="post"> 
<label name="title">Title:</label>
<input name="title" type="text" maxlength="30"/>
<label name="content">Content:</label>
<textarea name="content"></textarea>
<label name="privacy">Share with:</label>
<select name="privacy">
	<option value ="1">Private</option>
	<option value ="2">Friends</option>
	<option value ="3">Friends of Friends</option>
	<option value ="4">Everyone</option>
</select>
<label name="photos[]">Photos:</label>
<input name="photos[]" type="file" accept="image/x-png, image/gif, image/jpeg" />
<input type="submit" />
</form>

<?php
	echo "Your timeline <br> ---------------------------------------------<br> ";
	$diary_query = $wildbook->prepare("SELECT `title`, `timestamp`, `content`, `lid`, `privacy` FROM diarypost WHERE `posteruid` = ?;");
	$diary_query->bind_param("i", user_id());
	$diary_query->execute();
	$diary_query->bind_result($title, $timestamp, $content, $lid, $privacy);
	while ($diary_query->fetch()) {
		echo $title; echo "<br>";
		echo $timestamp; echo "<br>";
		echo $content; echo "<br>";
		echo "-----------------------------------------------<br>";
	}

	end_page();
?>