<?php
	include_once "template.php";
	begin_page("Home");
	
	
	if (isset($_POST['aname'])) {
		/*not including locations atm*/
		$wildbook = connect_wildbook();
		$add_useractivity = $wildbook->prepare('INSERT INTO `useractivity`(`uid`, `aname`) VALUES(?,?);');
		if (!$add_useractivity) echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
		$add_useractivity->bind_param("is",$_SESSION['current_user_id'],$_POST['aname']);
		if(!$add_useractivity->execute()) echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
	}
	
	
	
?>
<a href="home.php">Home</a>	
<?php	
	$wildbook->close();
	end_page();
?>