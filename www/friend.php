<?php
include_once "template.php";
begin_page("Friend");

var_dump($_SESSION);
var_dump($_POST);


if( isset($_POST["requestee"]) ) {
	$requester = $_SESSION["current_user_id"];
	echo $_SESSION["current_user_id"];
	echo session_id();
	$requestee = $_POST["requestee"];
	$privacy = $_POST["privacy"];
	

	$wildbook = connect_wildbook();
	$addquery = $wildbook->prepare("INSERT INTO `request`(`requester`, `requestee`) VALUES (?,?);");
	if (!$addquery) {
		echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
	}
	$addquery->bind_param("ii",$requester,$requestee);
	if (!$addquery->execute())
		echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
	else header("location:home.php");	
}
else if (isset($_POST["requester"]) ) {


}
else echo "what happened";
end_page();
?>
