<?php
	include_once "functions.php";
	session_start();
	$lname = $_POST["lname"];
	$lat = $_POST["latitude"];
	$lng = $_POST["longitude"];

	$wildbook = connect_wildbook();
	$add_location = $wildbook->prepare("INSERT INTO `location` (`lname`, `latitude`, `longitude`) VALUES (?, ?, ?)");
	$add_location->bind_param("sdd", $lname, $lat, $lng);
	$add_location->execute();
	$wildbook->close();

	header("location:location.php?name=" . $lname);
?>