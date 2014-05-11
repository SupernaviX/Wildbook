<?php
	include_once "template.php";
	begin_page("Wildbook");
	$username = $_SESSION['current_user_name'];
	if( isset($_POST['search_username']) ) {
		$search_user = $_POST['search_username'];
		header("location:profile.php?search=$search_user");
	}
	else {
		$search = $_GET['search'];
		$search_uid = get_uid($search);	// the process get_uid not functioning... ignore this
		if (user_exists($search)) {
			echo "<h4>"; echo $search; echo"</h4>";
		}
		else {
			echo "User does not exist";
		}
	}
?>
<form action="friend.php" method="post">
<input type="submit" value="Add Friend"/>
<input type="hidden" value="$search_uid" name="requestee">
<select name="privacy">
<option value ="1">Private</option>
<option value ="2">Friends</option>
<option value ="3">Friends of Friends</option>
<option value ="4">Everyone</option>
</select>
</form>
<a href=\"home.php\">Home</a>
<?php
end_page();
?>