<?php
	include_once "functions.php";
	session_start();
	
	$aname = $_POST['aname'];
	$wildbook = connect_wildbook();
	$addactivity = $wildbook->prepare("INSERT INTO `activity` (`aname`) VALUES (?)");
	$addactivity->bind_param("s",$aname);
	$addactivity->execute();
	
	$wildbook->close();
	header("location:activity.php?aname=" . $aname);
?>