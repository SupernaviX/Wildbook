<?php
	include_once "functions.php";
	session_start();

	if (isset($_GET["id"])) {
		$wildbook = connect_wildbook();
		$get_audio = $wildbook->prepare('SELECT `content`, `content_type` FROM `audio` WHERE `aid` = ?');
		$get_audio->bind_param("i", $_GET["id"]);
		$get_audio->execute();
		$get_audio->bind_result($content, $content_type);
		if ($get_audio->fetch()) {
			header('Content-type: ' . $content_type);
			echo $content;
		}
		$wildbook->close();
	}
?>
