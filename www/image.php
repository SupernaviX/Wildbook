<?php
	include_once "functions.php";
	session_start();

	if (isset($_GET["id"])) {
		$wildbook = connect_wildbook();
		$get_image = $wildbook->prepare('SELECT `content`, `content_type` FROM `photo` WHERE `pid` = ?');
		$get_image->bind_param("i", $_GET["id"]);
		$get_image->execute();
		$get_image->bind_result($content, $content_type);
		if ($get_image->fetch()) {
			header('Content-type: ' . $content_type);
			echo $content;
		}
		$wildbook->close();
	}
?>
