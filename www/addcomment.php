<?php
	include_once "template.php";

	$did = $_POST["did"];
	$uid = user_id();
	$message = $_POST["message"];
	$posteename = $_POST["posteename"];

	$wildbook = connect_wildbook();
	$add_comment = $wildbook->prepare('INSERT INTO `comment` (`did`, `uid`, `message`, `timestamp`) VALUES (?, ?, ?, NOW())');
	$add_comment->bind_param("iis", $did, $uid, $message);
	$add_comment->execute();

	header('location:profile.php?search=' . $posteename);
?>