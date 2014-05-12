<?php
	include_once "functions.php";
	session_start();

	if (isset($_GET["id"])) {
		$wildbook = connect_wildbook();
		$get_video = $wildbook->prepare('SELECT `content`, `content_type` FROM `video` WHERE `vid` = ?');
		$get_video->bind_param("i", $_GET["id"]);
		$get_video->execute();
		$get_video->bind_result($content, $content_type);
		if ($get_video->fetch()) {
			header('Content-type: ' . $content_type);
			echo $content;
		}
		$wildbook->close();
	}
?>
