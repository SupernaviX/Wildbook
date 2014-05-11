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
		$search_uid = get_uid($search);
		if (user_exists($search)) {
			echo "<h4>"; echo $search; echo"</h4>";
			$accept = check_req($username,$search);
			
			?>
			<form action="friend.php" method="post">
			<input type="submit" value="<?php if($accept) echo "Accept Friend"; else echo "Add Friend"; ?>"/>
			<input type="hidden" value="<?php echo $search_uid ?>" name="requestee">
			<input type="hidden" value="<?php echo date("Y-m-d H:i:s") ?>" name="datetime">
			<select name="privacy">
			<option value ="1">Private</option>
			<option value ="2">Friends</option>
			<option value ="3">Friends of Friends</option>
			<option value ="4">Everyone</option>
			</select>
			</form>
			<?php
		}
		else {
			echo "User does not exist";
		}
	}
?>

<a href="home.php">Home</a>
<?php
end_page();
?>