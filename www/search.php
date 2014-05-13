<?php
	include_once "template.php";
	begin_page();

	$term = $_POST["term"];
	$type = $_POST["type"];

	if ($type === "activity") {
		$titleText = "<a href=\"activity.php?aname=$term\">$term</a>";
	}
	else if ($type === "location") {
		$titleText = "<a href=\"location.php?name=$term\">$term</a>";
	}
	else {
		$titleText = $term;
	}
	echo "<h4>Search results for $titleText</h4>";
	if ($type === "user") {
		echo "<p><a href=\"profile.php?search=$term\">View ${term}'s profile</a></p>";
	}

	if (!$type) {
		echo "<p><a href=\"activity.php?aname=$term\">Do you enjoy ${term}?</a></p>";
		echo "<p><a href=\"location.php?name=$term\">Ever been to ${term}?</a></p>";
	}

	$uid = user_id();
	if ($type === "location") {
		echo "<h4>Posts from $term</h4>";
		$wildbook = connect_wildbook();
		$relevant_posts = $wildbook->prepare("CALL postsin(?, ?)");
		$relevant_posts->bind_param("si", $term, $uid);
		$relevant_posts->execute();
		$relevant_posts->bind_result($did, $postername, $posteename, $title, $timestamp, $content);
		while ($relevant_posts->fetch()) {
			display_diary_post($did, $postername, $posteename, $title, $timestamp, $content);
		}
		$wildbook->close();
	}
	else {
		echo "<h4>Posts about $term</h4>";
		$wildbook = connect_wildbook();
		$relevant_posts = $wildbook->prepare("CALL postsabout(?, ?)");
		$relevant_posts->bind_param("si", $term, $uid);
		$relevant_posts->execute();
		$relevant_posts->bind_result($did, $postername, $posteename, $title, $timestamp, $content);
		while ($relevant_posts->fetch()) {
			display_diary_post($did, $postername, $posteename, $title, $timestamp, $content);
		}
		$wildbook->close();
	}
	end_page();
?>