<?php
	include_once "template.php";
	begin_page("Wildbook");

	if (isset($_GET['search'])) {
		$search = $_GET['search'];
		$search_uid = get_uid($search);
	} else {
		$search = user_name();
		$search_uid = user_id();
	}
	if (user_exists($search)) {
		echo "<h4>"; echo $search; echo"</h4>";
		$accept = check_req($_SESSION['current_user_id'],$search_uid);
		$are_friends = check_friend($_SESSION['current_user_id'],$search_uid);
	} else {
		echo "User does not exist";
		echo "<a href=\"home.php\">Home</a>"
		end_page();
		exit;
	}

	if ($search_uid !== user_id()) {
		?>

		<form action="friend.php" method="post">
			<input type="submit" value="<?php if($accept) echo "Accept Friend"; else if($are_friends) echo "Remove Friend"; else echo "Add Friend"; ?>"/>
			<input type="hidden" value="<?php echo $search_uid ?>" name="requestee">
			<input type="hidden" value="<?php echo $accept ?>" name="accept">
			<input type="hidden" value="<?php echo $are_friends ?>" name="remove">
			<input type="hidden" value="<?php echo date("Y-m-d H:i:s") ?>" name="datetime">
			<select name="privacy">
			<option value ="1">Private</option>
			<option value ="2">Friends</option>
			<option value ="3">Friends of Friends</option>
			<option value ="4">Everyone</option>
			</select>
		</form>

		<?php
		echo "<h4>Post in ${search}'s Diary</h4>";
		display_diary_post_submission_form($search_uid);
	}
	/*display all of a users friends*/
	echo "Friends <br> ------------------------------------------------- <br>";
	$wildbook = connect_wildbook();
	$friend_query = $wildbook->prepare("select seconduid, privacy from friend f where f.firstuid = ?;");
	if(!$friend_query) echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
	$friend_query->bind_param("i", $search_uid);
	if (!$friend_query->execute()) echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
	$friend_query->bind_result($friend_uid,$privacy);
	while ($friend_query->fetch()) {
		if(visible($_SESSION['current_user_id'],$search_uid,$privacy)) {
			$username = get_username($friend_uid);
			echo "<a href=\"profile.php?search=$username\">$username</a> <br>";
		}
	}

	echo "Posts <br> ------------------------------------------------- <br>";
	$uid = user_id();
	$distance = distance($uid, $search_uid);
	$post_query = $wildbook->prepare('SELECT `did`, `username`, `title`, `timestamp`, `content` '
		.'FROM `diarypost` `dp` '
		.'JOIN `user` `u` ON `dp`.`posteruid` = `u`.`uid` '
		.'WHERE `posteeuid` = ? AND `privacy` >= ? '
		.'ORDER BY `timestamp` DESC;');
	$post_query->bind_param("ii", $search_uid, $distance);
	$post_query->execute();
	$post_query->bind_result($did, $postername, $title, $timestamp, $content);
	while ($post_query->fetch()) {
		display_diary_post($did, $postername, $search, $title, $timestamp, $content);
		echo "------------------------------------------------- <br>";
	}
?>

<a href="home.php">Home</a>
<?php
end_page();
?>