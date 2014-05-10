<?php
include_once "functions.php";
function begin_page($title) {
	session_start();
	echo "<html><head><title>$title</title></head><body>";
}

function end_page() {
	echo "</body></html>";
}

?>