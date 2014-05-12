<?php
	include_once "template.php";
	begin_page("Home");
	
	$aname = $_GET['aname'];
	
	$wildbook = connect_wildbook();
	$aname_query = $wildbook->prepare('Select * from activity where aname = ?');
	if(!$aname_query) echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
	$aname_query->bind_param("s",$aname);
	if (!$aname_query->execute()) echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
	if(!$aname_query->fetch()) {
		
	}
	else {
	
	}
	
	
	
?>
<a href="home.php">Home</a>
<?php
	end_page();
?>
