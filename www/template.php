<?php
include_once "functions.php";
include_once "partials.php";
session_start();
function begin_page($title = "Wildbook") {
	if (empty($_SESSION["current_user_id"]) && $_SERVER['PHP_SELF'] != "/login.php" && $_SERVER['PHP_SELF'] != "/createprofile.php")
		header("location:login.php");
	echo "<html><head><title>$title</title></head><body>";
}

function end_page() {
	echo "</body></html>";
}

?>