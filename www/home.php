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
<form action="profile.php" method="post"> 
username: <input name="search_username" type="text" maxlength="30"/> 
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
<input name="photos[]" type="file" accept="image/*" multiple="multiple"/>
<input type="submit" />
</form>

<?php
	echo "Your timeline <br> ---------------------------------------------<br> ";
	$diary_query = $wildbook->prepare("SELECT `did`, `title`, `timestamp`, `content`, `lid`, `privacy` FROM diarypost WHERE `posteruid` = ?;");

	$diary_query->bind_param("i", user_id());
	$diary_query->execute();
	$diary_query->store_result();
	$diary_query->bind_result($did, $title, $timestamp, $content, $lid, $privacy);
	while ($diary_query->fetch()) {
		echo "<div style=\"max-width: 75%\">";
		echo $title; echo "<br>";
		echo $timestamp; echo "<br>";
		echo $content; echo "<br>";

		if (!isset($photo_query))
			$photo_query = $wildbook->prepare("SELECT `pid` FROM `photo` WHERE `did` = ?");
		$photo_query->bind_param("i", $did);
		$photo_query->execute();
		$photo_query->bind_result($pid);

		while($photo_query->fetch()) {
			echo "<img src=\"image.php?id=$pid\" style=\"max-width: 100%\"/>";
		}
		echo "</div>";
		echo "-----------------------------------------------<br>";
	}

	end_page();
?>