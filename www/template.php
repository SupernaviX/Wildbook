<?php
include_once "functions.php";
include_once "partials.php";
session_start();
function begin_page($title) {
	echo "<html><head><title>$title</title></head><body>";
	if (!$_SESSION["current_user_id"] && $_SERVER['PHP_SELF'] != "/login.php" && $_SERVER['PHP_SELF'] != "/createprofile.php")
		header("location:login.php");
}

function end_page() {
	echo "</body></html>";
}

?>