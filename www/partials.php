<?php
	include_once "functions.php";

	//Displays a diary post.
	function display_diary_post($did, $postername, $title, $timestamp, $content) {
		static $photo_query = NULL;
		static $video_query = NULL;
		static $audio_query = NULL;
		if (is_null($photo_query) || is_null($video_query) || is_null($audio_query)) {
			$wildbook = connect_wildbook();
			$photo_query = $wildbook->prepare('SELECT `pid` FROM `photo` WHERE `did` = ?');
			$video_query = $wildbook->prepare('SELECT `vid` FROM `video` WHERE `did` = ?');
			$audio_query = $wildbook->prepare('SELECT `aid` FROM `audio` WHERE `did` = ?');
		}

		echo "<div style=\"max-width: 75%\">";
		echo $title; echo "<br>";
		echo $timestamp; echo "<br>";
		echo $content; echo "<br>";

		$photo_query->bind_param("i", $did);
		$photo_query->execute();
		$photo_query->bind_result($pid);
		while($photo_query->fetch()) {
			echo "<img src=\"image.php?id=$pid\" style=\"max-width: 100%\"/><br/>";
		}

		$video_query->bind_param("i", $did);
		$video_query->execute();
		$video_query->bind_result($vid, $content_type);
		while($video_query->fetch()) {
			echo "<video controls><source src=\"video.php?id=$vid\" type=\"$content_type\"></video><br/>";
		}

		$audio_query->bind_param("i", $did);
		$audio_query->execute();
		$audio_query->bind_result($aid, $content_type);
		while($audio_query->fetch()) {
			echo "<audio controls><source src=\"audio.php?id=$aid\" type=\"$content_type\"></audio><br/>";
		}

		echo "</div>";
	}
?>