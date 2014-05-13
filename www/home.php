<?php
	include_once "template.php";
	begin_page("Home");
?>
<h4>Home</h4>
<?php 
	echo "<a href=\"logout.php\">Logout</a> <br><br><br>";
	$username = $_SESSION["current_user_name"];
	echo "Welcome $username!<br><br><br>";
?>

<form id="searchform" action="search.php" method="POST">
	<label name="term">Search:</label>
	<input id="search" name="term" type="text" />
	<input id="type" name="type" type="hidden" />
	<input type="submit" value ="Search"/>
</form>

<script type="text/javascript">
	$.widget("custom.uberAutocomplete", $.ui.autocomplete, {
		_renderItem: function( ul, item ) {
			var renderString = (item.value != "")
				? item.label + " (" + item.value + ")"
				: item.label;
			return $( "<li>" )
				.append( $( "<a>" ).text( renderString ) )
				.appendTo( ul );
		}
	})
	$("#search").uberAutocomplete({
		source: "search/all.php",
		response: function(event, ui) {
			ui.content.unshift({label: $("#search").val(), value: ""});
		},
		select: function(event, ui) {
			event.preventDefault();
			$("#search").val(ui.item.label);
			$("#type").val(ui.item.value);
			return false;
		},
	});
</script>

<?php

	echo "Friend Requests <br> ------------------------------------------------- <br>";
	$wildbook = connect_wildbook();
	$req_query = $wildbook->prepare("SELECT `requester` FROM `request` WHERE `requestee` = ?");
	$req_query->bind_param("i", $_SESSION["current_user_id"]);
	$req_query->execute();
	$req_query->store_result();
	$req_query->bind_result($reqid);
	while ($req_query->fetch()) {
		$request = get_username($reqid);		
		echo "<a href=\"profile.php?search=$request\">$request</a> <br>"; 	
	}
	$wildbook->close();
	
	echo "Your Friends <br> ------------------------------------------------- <br>";
	$wildbook = connect_wildbook();
	$friend_query = $wildbook->prepare("SELECT `username` FROM `user` WHERE `uid` = (SELECT `seconduid` FROM `accepted_friends` WHERE `firstuid` = ?)");
	$uid = user_id();
	$friend_query->bind_param("i", $uid);
	$friend_query->execute();
	$friend_query->store_result();
	$friend_query->bind_result($username);
	while ($friend_query->fetch()) {
		echo "<a href=\"profile.php?search=$username\">$username</a> <br>";
	}
	$wildbook->close();

	echo "Add a Diary Post <br>";
	display_diary_post_submission_form($uid);

	echo "Your timeline <br> ---------------------------------------------<br> ";
	$wildbook = connect_wildbook();
	$diary_query = $wildbook->prepare('CALL timeline(?)');

	$diary_query->bind_param("i", $uid);
	$diary_query->execute();
	$diary_query->store_result();
	$diary_query->bind_result($did, $postername, $posteename, $title, $timestamp, $lname, $content);
	while ($diary_query->fetch()) {
		display_diary_post($did, $postername, $posteename, $title, $timestamp, $lname, $content);
		echo "---------------------------------------------<br>";
	}
	$wildbook->close();
	
	echo "Your liked activities <br> ------------------------------------------<br>";
	$wildbook = connect_wildbook();
	$activity_query = $wildbook->prepare('SELECT `aname` FROM `useractivity` WHERE `uid` = ?');
	if (!$activity_query) echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
	$activity_query->bind_param("i", $_SESSION["current_user_id"] );
	$activity_query->execute();
	$activity_query->store_result();
	$activity_query->bind_result($aname);
	while ($activity_query->fetch()) {
		echo "<a href=\"activity.php?aname=$aname\">$aname</a> <br>";
	}
	$wildbook->close();

	end_page();
?>