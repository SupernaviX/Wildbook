<?php
include_once "template.php";
begin_page("Friend");


if( isset($_POST["requestee"]) ) {
	$requester = $_SESSION["current_user_id"];
	$requestee = $_POST["requestee"];
	$req_username = get_username($requestee);
	if ($_POST["remove"]) {
		$wildbook = connect_wildbook();
		$delete = $wildbook->prepare("DELETE FROM `friend` WHERE `firstuid`=? and `seconduid`=?;");
		if(!$delete)
			echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
		$delete->bind_param("ii",$requester,$requestee);
		if (!$delete->execute())
			echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
		$delete->bind_param("ii",$requestee,$requester);
		if (!$delete->execute())
			echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
		header("location:profile.php?search=$req_username");
	}
	else if ($requester != $requestee) {
		$wildbook = connect_wildbook();
		if (!$_POST["accept"]) {
			$addrequest = $wildbook->prepare("INSERT INTO `request`(`requester`, `requestee`) VALUES (?,?);");
			if (!$addrequest) {
				echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
			}
			$addrequest->bind_param("ii",$requester,$requestee);
			if (!$addrequest->execute())
				echo "RExecute failed: (" . $wildbook->errno . ") " . $wildbook->error;
		}
		else {
			$del_request = $wildbook->prepare("DELETE FROM `request` WHERE `requester`=?");
			if (!$del_request) {
				echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
			}			
			$del_request->bind_param("i",$requestee);
			if (!$del_request->execute())
				echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;			
		}
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
			echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;	
		header("location:profile.php?search=$req_username");
	}
	else {header("location:profile.php?search=$req_username");}
}
else echo "what happened";
end_page();
?>
