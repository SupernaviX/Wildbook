<?php
	include_once "template.php";
	begin_page("Home");
	
	$aname = $_GET['aname'];
	
	$wildbook = connect_wildbook();
	$aname_query = $wildbook->prepare('Select * from activity where aname = ?');
	$aname_query->bind_param("s",$aname);
	if (!$aname_query->execute()) echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
	if($aname_query->fetch()) {
		echo "$aname <br>";
		?>
		<form action="like.php" method="post">
		<input type="hidden" value="<?php echo $aname ?>" name="aname">
		<input type="submit" value="Like" />
		</form>
		<?php
	}
	else {
		echo "would you like to add $aname to the list of activities? <br>";
	}
	
	
	
?>
<a href="home.php">Home</a>
<?php
	end_page();
?>
