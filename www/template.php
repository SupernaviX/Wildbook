<?php
include_once "functions.php";
include_once "partials.php";
session_start();
function begin_page($title = "Wildbook") {
	if (empty($_SESSION["current_user_id"]) && $_SERVER['PHP_SELF'] != "/login.php" && $_SERVER['PHP_SELF'] != "/createprofile.php")
		header("location:login.php");
	echo '<html>'
			.'<head>'
				."<title>$title</title>"
				."<style type=\"text/css\">\n"
					."html { height: 100% }\n"
					."body { height: 100% }\n"
					."#my-map { height: 100% }\n"
				."</style>\n"
				.'<link rel="stylesheet" type="text/css" href="styles/jquery-ui-1.10.4.custom.min.css">'
				.'<script type="text/javascript" src="scripts/jquery-1.10.2.js"></script>'
				.'<script type="text/javascript" src="scripts/jquery-ui-1.10.4.custom.min.js"></script>'
			."</head>"
			."<body>";
}

function end_page() {
	echo "</body></html>";
}

?>