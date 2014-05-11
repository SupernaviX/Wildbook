<?php
	include_once "functions.php";

	//Displays a diary post.
	function display_diary_post($did, $postername, $title, $timestamp, $content) {
		$wildbook = connect_wildbook();
		$photo_query = $wildbook->prepare('SELECT `pid` FROM `photo` WHERE `did` = ?');
		$video_query = $wildbook->prepare('SELECT `vid` FROM `video` WHERE `did` = ?');
		$audio_query = $wildbook->prepare('SELECT `aid` FROM `audio` WHERE `did` = ?');

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


	function display_diary_post_submission_form($title = "", $content = "", $privacy = 2, $errors = array()) {
		echo '<form enctype="multipart/form-data" action="adddiarypost.php" method="post">';

		echo '<label name="title">Title:</label>'
			.'<input name="title" type="text" maxlength="30" value="'. $title .'" />'
			.list_errors($errors["title"])
			.'<br />';

		echo '<label name="content">Content:</label>'
			.'<textarea name="content">' . $content .'</textarea>'
			.list_errors($errors["content"])
			.'<br />';

		echo '<label name="privacy">Share with:</label>'
			.'<select name="privacy">'
			.select_option(1, "Private", $privacy)
			.select_option(2, "Friends", $privacy)
			.select_option(3, "Friends of Friends", $privacy)
			.select_option(4, "Everyone", $privacy)
			.'</select>'
			.list_errors($errors["privacy"])
			.'<br />';

		echo '<label name="photos[]">Photos:</label>'
			.'<input name="photos[]" type="file" accept="image/*" multiple="multiple"/>'
			.list_errors($errors["photos[]"])
			.'<br />';

		echo '<label name="videos[]">Videos:</label>'
			.'<input name="videos[]" type="file" accept="video/*" multiple="multiple"/>'
			.list_errors($errors["videos[]"])
			.'<br />';

		echo '<label name="audio[]">Audio:</label>'
			.'<input name="audio[]" type="file" accept="audio/*" multiple="multiple"/>'
			.list_errors($errors["audio[]"])
			.'<br />';

		echo '<input type="submit" />';
		echo '</form>';
	}

	function list_errors($error_list) {
		if (empty($error_list))
			return '';
		$result = "<ul>";
		foreach ($error_list as $error) {
			$result .= "<li>$error</li>";
		}
		$result .= "</ul>";
		return $result;
	}

	function select_option($value, $name, $selected) {
		if ($value === $selected) {
			return "<option value=\"$value\" selected=\"selected\">$name</option>";
		} else {
			return "<option value=\"$value\">$name</option>";
		}
	}

?>