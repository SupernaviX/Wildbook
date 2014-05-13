<?php
	include_once "../functions.php";
	session_start();

	$search_term = '%' . $_GET['term'] . '%';

	$wildbook = connect_wildbook();
	$locations_query = $wildbook->prepare("SELECT `lname` FROM `location` WHERE `lname` LIKE ?");
	echo $locations_query->error;
	$locations_query->bind_param("s", $search_term);
	$locations_query->execute();
	$locations_query->bind_result($lname);
	$locations = array();
	while($locations_query->fetch()) {
		$locations[] = $lname;
	}
	echo json_encode($locations);
?>