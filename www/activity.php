<?php
	include_once "template.php";
	begin_page($_GET['aname']);
	logout_search();
	
	$wildbook = connect_wildbook();
	$aname_query = $wildbook->prepare('Select * from activity where aname =?');
	if (!$aname_query) echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
	$aname = $_GET['aname'];
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
		echo "Activity $aname does not exist<br>";
		$aname_like = $wildbook->prepare('Select * from activity where aname like ?');
		$like = "%".$aname."%";
		$aname_like->bind_param("s",$like);
		$aname_like->execute();
		$aname_like->bind_result($like_aname);
		while($aname_like->fetch()) {
			echo "Did you mean <a href=\"activity.php?aname=$like_aname\">$like_aname</a> <br>";
		}	
		
		echo "Would you like to add $aname to the list of activities? <br>";
		?>
		<form action="addactivity.php" method="post">
		<input type="hidden" value="<?php echo $aname ?>" name="aname">
		<input type="submit" value="Yes" />
		</form>		
		<?php
	}
	
	
	
?>
<a href="home.php">Home</a>
<?php
	end_page();
?>
