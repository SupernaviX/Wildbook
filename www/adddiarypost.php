<?php
	include_once "template.php";
	begin_page();

	$errors = array();
	// This section only runs if you're making a new post.
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		if (!empty($_POST["title"]))
			$title = $_POST["title"];
		else
			$errors["title"][] = "Post must have a title";
		$content = $_POST["content"];
		$privacy = intval($_POST["privacy"]);
		if ($privacy < 1 || $privacy > 4) {
			$errors["privacy"][] = "Invalid privacy level";
		}
		if (!empty($_FILES["photos"])) {
			$photo_upload_error = false;
			foreach ($_FILES["photos"]["errors"] as $error) {
				if ($error > 0) {
					$photo_upload_error = true;
					$errors["photos[]"][] = "Error uploading a photo.";
				}
			}
		}
		if (!empty($_FILES["audio"])) {
			$audio_upload_error = false;
			foreach ($_FILES["audio"]["errors"] as $error) {
				if ($error > 0) {
					$audio_upload_error = true;
					$errors["audio[]"][] = "Error uploading an audio file.";
				}
			}
		}
		if (!empty($_FILES["videos"])) {
			$video_upload_error = false;
			foreach ($_FILES["videos"]["errors"] as $error) {
				if ($error > 0) {
					$video_upload_error = true;
					$errors["videos[]"][] = "Error uploading a video.";
				}
			}
		}
		if (empty($errors)) {
			$wildbook = connect_wildbook();
			$make_post = $wildbook->prepare(
				'INSERT INTO `diarypost` (`posteruid`, `posteeuid`, `title`, `timestamp`, `content`, `privacy`)
					VALUES (?, ?, ?, NOW(), ?, ?);');
			$uid = user_id();
			$make_post->bind_param("iissi", $uid, $uid, $title, $content, $privacy);
			$make_post->execute();
			$did = $make_post->insert_id;

			if (!empty($_FILES["photos"]['tmp_name'] && !empty($_FILES["photos"]['tmp_name'][0]))) {
				$file_count = count($_FILES["photos"]['tmp_name']);
				$upload_photo = $wildbook->prepare('INSERT INTO `photo` (`did`, `content`, `content_type`, `privacy`) VALUES (?, ?, ?, ?)');
				for ($index = 0; $index < $file_count; ++$index) {
					$contents = file_get_contents($_FILES["photos"]['tmp_name'][$index]);
					$content_type = $_FILES["photos"]['type'][$index];
					$upload_photo->bind_param("issi", $did, $contents, $content_type, $privacy);
					$upload_photo->execute();
				}
			}

			if (!empty($_FILES["videos"]['tmp_name'] && !empty($_FILES["videos"]['tmp_name'][0]))) {
				$file_count = count($_FILES["videos"]['tmp_name']);
				$upload_video = $wildbook->prepare('INSERT INTO `video` (`did`, `content`, `content_type`, `privacy`) VALUES (?, ?, ?, ?)');
				for ($index = 0; $index < $file_count; ++$index) {
					$contents = file_get_contents($_FILES["videos"]['tmp_name'][$index]);
					$content_type = $_FILES["videos"]['type'][$index];
					$upload_video->bind_param("issi", $did, $contents, $content_type, $privacy);
					$upload_video->execute();
				}
			}

			if (!empty($_FILES["audio"]['tmp_name'] && !empty($_FILES["audio"]['tmp_name'][0]))) {
				$file_count = count($_FILES["audio"]['tmp_name']);
				$upload_audio = $wildbook->prepare('INSERT INTO `audio` (`did`, `content`, `content_type`, `privacy`) VALUES (?, ?, ?, ?)');
				for ($index = 0; $index < $file_count; ++$index) {
					$contents = file_get_contents($_FILES["audio"]['tmp_name'][$index]);
					$content_type = $_FILES["audio"]['type'][$index];
					$upload_audio->bind_param("issi", $did, $contents, $content_type, $privacy);
					$upload_audio->execute();
				}
			}

			header("location:home.php");
		}
	}

	// utility method
	function select_option($value, $name, $selected) {
		if ($value === $selected) {
			echo "<option value=\"$value\" selected=\"selected\">";
		} else {
			echo "<option value=\"$value\">";
		}
		echo $name, "</option>";
	}

	//utility method
	function list_errors($error_list) {
		if (empty($error_list))
			return;
		echo "<ul>";
		foreach ($error_list as $error) {
			echo "<li>$error</li>";
		}
		echo "</ul>";
	}
?>
<form enctype="multipart/form-data" action="adddiarypost.php" method="post"> 
	<label name="title">Title:</label>
	<input name="title" type="text" maxlength="30" value="<?php echo $title ?>" />
	<?php list_errors($errors["title"]) ?>
	<br />

	<label name="content">Content:</label>
	<textarea name="content"><?php echo $content ?></textarea>
	<?php list_errors($errors["content"]) ?>
	<br />

	<label name="privacy">Share with:</label>
	<select name="privacy">
	<?php
		select_option(1, "Private", $privacy);
		select_option(2, "Friends", $privacy);
		select_option(3, "Friends of Friends", $privacy);
		select_option(4, "Everyone", $privacy);

	?>
	</select>
	<?php list_errors($errors["privacy"]); ?>
	<br />

	<label name="photos[]">Photos:</label>
	<input name="photos[]" type="file" accept="image/*" multiple="multiple"/>
	<?php list_errors($errors["photos[]"]); ?>
	<br />

	<label name="videos[]">Videos:</label>
	<input name="videos[]" type="file" accept="video/*" multiple="multiple"/>
	<?php list_errors($errors["videos[]"]); ?>
	<br />

	<label name="audio[]">Audio:</label>
	<input name="audio[]" type="file" accept="audio/*" multiple="multiple"/>
	<?php list_errors($errors["audio[]"]); ?>
	<br />

	<input type="submit" />
</form>