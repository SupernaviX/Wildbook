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
		if (empty($errors)) {
			$wildbook = connect_wildbook();
			$make_post = $wildbook->prepare(
				'INSERT INTO `diarypost` (`posteruid`, `posteeuid`, `title`, `timestamp`, `content`, `privacy`)
					VALUES (?, ?, ?, NOW(), ?, ?);');
			$uid = user_id();
			$make_post->bind_param("iissi", $uid, $uid, $title, $content, $privacy);
			$make_post->execute();
			$did = $make_post->insert_id;

			if (!empty($_FILES["photos"])) {
				$file_count = count($_FILES["photos"]['tmp_name']);
				$upload_photo = $wildbook->prepare('INSERT INTO `photo` (`did`, `content`, `content_type`, `privacy`) VALUES (?, ?, ?, ?)');
				for ($index = 0; $index < $file_count; ++$index) {
					$contents = file_get_contents($_FILES["photos"]['tmp_name'][$index]);
					$content_type = $_FILES["photos"]['type'][$index];
					$upload_photo->bind_param("issi", $did, $contents, $content_type, $privacy);
					$upload_photo->execute();
				}
			}
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

	<label name="content">Content:</label>
	<textarea name="content"><?php echo $content ?></textarea>
	<?php list_errors($errors["content"]) ?>

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

	<label name="photos[]">Photos:</label>
	<input name="photos[]" type="file" accept="image/x-png, image/gif, image/jpeg" />
	<?php list_errors($errors["photos[]"]); ?>

	<input type="submit" />
</form>