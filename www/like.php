<?php
	include_once "template.php";
	begin_page("Home");
	
	$wildbook = connect_wildbook();
	
	/*activty and activity&location likes*/
	if ( isset($_POST['aname'], $_POST['lid'],$_POST['lname'],$_POST['loc_add_act']) ) {
		$lname = $_POST['lname'];
		$aname_query = $wildbook->prepare('select 1 from activity where aname = ?');
		$aname_query->bind_param("s",$_POST['aname']);
		$aname_query->execute();
		$aname_query->store_result();
		if ($aname_query->fetch()) {
		
			$add_useractloc = $wildbook->prepare('INSERT INTO `useractivitylocation`(`uid`, `aname`,`lid`) VALUES(?,?,?);');
			if (!$add_useractloc ) echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
			$add_useractloc->bind_param("isi", $_SESSION['current_user_id'], $_POST['aname'], $_POST['lid']);
			$add_useractloc->execute();
			
			header("location:location.php?name=$lname");
			
		}
		else {
			echo $_POST['aname'] . " does not exist<br>";
			?> <a href="location.php?name= <?php echo $lname ?>">Back</a><br> <?php
			
		}
	}
	elseif(isset($_POST['lid'],$_POST['lname'])) {		
		$lname = $_POST['lname'];
		$add_useractloc = $wildbook->prepare('INSERT INTO `useractivitylocation`(`uid`, `aname`,`lid`) VALUES(?,?,?);');
		$add_useractloc->bind_param("isi", $_SESSION['current_user_id'], $_POST['aname'], $_POST['lid']);
		if(!$add_useractloc->execute()) echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
		header("location:location.php?name=$lname");
	}		
	else if (isset($_POST['aname'])) {	
		$aname = $_POST['aname'];
		$add_useractivity = $wildbook->prepare('INSERT INTO `useractivity`(`uid`, `aname`) VALUES(?,?);');
		if (!$add_useractivity) echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
		$add_useractivity->bind_param("is",$_SESSION['current_user_id'],$_POST['aname']);
		if(!$add_useractivity->execute()) echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
		header("location:activity.php?aname=$aname");
	}
	/*diarypost  likes*/
	if (isset($_POST['did'])) {
		echo $_POST['did'];
		$add_diarylike = $wildbook->prepare('INSERT INTO `diarylike`(`uid`, `did`) VALUES(?,?);');
		$add_diarylike->bind_param("ii",$_SESSION['current_user_id'],$_POST['did']);
		if(!$add_diarylike->execute()) echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
		header("location:home.php");
	}
	
	
	
?>
<a href="home.php">Home</a>	
<?php	
	$wildbook->close();
	end_page();
?>