<?php
	include "../functions.php";
	
	$term = $_GET['term'];

	$results = array();

	$wildbook = connect_wildbook();
	$search = $wildbook->prepare("CALL search(?)");
	$search->bind_param("s", $term);
	$search->execute();
	$search->bind_result($name, $type);
	while ($search->fetch()) {
		$results[] = array("label" => $name, "value" => $type); 
	}
	$wildbook->close();
	echo json_encode($results);
?>