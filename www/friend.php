<?php
include_once "template.php";
begin_page("Friend");

var_dump($_SESSION);
var_dump($_POST);


if( isset($_POST["requestee"]) ) {
	$requester = $_SESSION["current_user_id"];
	$requestee = $_POST["requestee"];
	if ($requester != $requestee) {

		$wildbook = connect_wildbook();
		$addrequest = $wildbook->prepare("INSERT INTO `request`(`requester`, `requestee`) VALUES (?,?);");
		if (!$addrequest) {
			echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
		}
		$addrequest->bind_param("ii",$requester,$requestee);
		if (!$addrequest->execute())
			echo "RExecute failed: (" . $wildbook->errno . ") " . $wildbook->error;

		$addfriend = $wildbook->prepare("INSERT INTO `friend`(`firstuid`, `seconduid`, `since`, `privacy`) VALUES (?,?,?,?);");
		if (!$addfriend) {
			echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
		}
		$addfriend->bind_param("iisi",
		$_SESSION["current_user_id"],
		$_POST["requestee"],
		$_POST["datetime"],
		$_POST["privacy"]);
		if (!$addfriend->execute())
			echo "FExecute failed: (" . $wildbook->errno . ") " . $wildbook->error;	
	}
	else {header("location:profile.php?search=$requestee");}
}
else if (isset($_POST["requester"]) ) {


}
else echo "what happened";
end_page();
?>
