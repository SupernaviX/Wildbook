<?php
	include_once "functions.php";

	//Displays a diary post.
	function display_diary_post($did, $postername, $posteename, $title, $timestamp, $lname, $content) {
		$wildbook = connect_wildbook();
		$photo_query = $wildbook->prepare('SELECT `pid` FROM `photo` WHERE `did` = ?');
		$video_query = $wildbook->prepare('SELECT `vid`, `content_type` FROM `video` WHERE `did` = ?');
		$audio_query = $wildbook->prepare('SELECT `aid`, `content_type` FROM `audio` WHERE `did` = ?');
		$comments_query = $wildbook->prepare('SELECT `username`, `message`, `timestamp` '
											.'FROM `comment` `c` '
											.'JOIN `user` `u` ON `c`.`uid` = `u`.`uid` '
											.'WHERE `did` = ? '
											.'ORDER BY `timestamp`');
		$likes_query = $wildbook->prepare('CALL count_likes(?)');

		echo "<div style=\"max-width: 75%\">";
		if ($postername === $posteename)
			echo "$postername <br/>";
		else
			echo "$postername -> $posteename <br/>";
		echo $title; echo "<br/>";
		if (!empty($lname))
			echo "$timestamp at $lname <br/>";
		else
			echo "$timestamp <br/>";
		echo $content; echo "<br/>";

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

		$likes_query->bind_param("i",$did);
		$likes_query->execute();
		$likes_query->bind_result($likes_count);
		$likes_query->fetch();
		if ($likes_count > 0) {
			echo "-------------------------------------------------------<br>" .$likes_count ." people like this <br>";
		}
		
		echo '<div style="margin-left: 20px">';
		$comments_query->bind_param("i", $did);
		$comments_query->execute();
		$comments_query->bind_result($commenter, $message, $timestamp);
		while($comments_query->fetch()) {
			echo "<div><em>$commenter replied at $timestamp</em>"
				."<pre>$message</pre></div>";
		}
		
		echo '<form action="like.php" method="post">'.
			'<input type="hidden" value="' . $did . '" name="did">' .
			'<input type="submit" value="Like" />'.
			'</form>';
		
		echo '<div><form action="addcomment.php" method="post">'
			.'<input type="hidden" name="did" value="' . $did . '" />'
			.'<input type="hidden" name="posteename" value="' . $posteename . '" />'
			.'<textarea name="message"></textarea>'
			.'<input type="submit" value="Comment" />'
			.'</form></div>';

		echo "</div>";
		echo "</div>";
		$wildbook->close();
	}


	function display_diary_post_submission_form($posteeid, $title = "", $content = "", $lname = "", $privacy = 2, $errors = array()) { ?>
		<form enctype="multipart/form-data" action="adddiarypost.php" method="post">

		<input type="hidden" name="posteeuid" value="<?php echo $posteeid ?>" />

		<label name="title">Title:</label>
		<input name="title" type="text" maxlength="30" value="<?php echo $title ?>" />
		<?php echo list_errors($errors, "title") ?>
		<br />

		<label name="content">Content:</label>
		<textarea name="content"><?php echo $content ?></textarea>
		<?php echo list_errors($errors, "content") ?>
		<br />

		<label name="lname">Location:</label>
		<input name="lname" type="text" value="<?php echo $lname ?>" />
		<?php echo list_errors($errors, "lname") ?>
		<br />

		<label name="privacy">Share with:</label>
		<select name="privacy">
		<?php
			echo select_option(1, "Private", $privacy)
				.select_option(2, "Friends", $privacy)
				.select_option(3, "Friends of Friends", $privacy)
				.select_option(4, "Everyone", $privacy)
		?>
		</select>
		<?php echo list_errors($errors, "privacy") ?>
		<br />

		<label name="photos[]">Photos:</label>
		<input name="photos[]" type="file" accept="image/*" multiple="multiple"/>
		<?php echo list_errors($errors, "photos[]") ?>
		<br />

		<label name="videos[]">Videos:</label>
		<input name="videos[]" type="file" accept="video/*" multiple="multiple"/>
		<?php echo list_errors($errors, "videos[]") ?>
		<br />

		<label name="audio[]">Audio:</label>
		<input name="audio[]" type="file" accept="audio/*" multiple="multiple"/>
		<?php echo list_errors($errors, "audio[]") ?>
		<br />

		<input type="submit" />
		</form>

		<script type="text/javascript">
			$(function() {
				$('[name="lname"]').autocomplete({
					source: "search/locations.php"
				});
			})
		</script>
	<?php }

	function list_errors($error_list, $input_name) {
		if (empty($input_name) || !array_key_exists($input_name, $error_list))
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