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
		if (user_exists($search)) {
			echo "<h4>"; echo $search; echo"</h4>";
		}
		else {
			echo "User does not exist";
		}
	}
	echo "<br/><br/><br/><a href=\"home.php\">Home</a> <br />";
	end_page();
?>
