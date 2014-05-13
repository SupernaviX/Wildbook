<?php
	include_once "template.php";
	begin_page();

	$lname = $_GET["name"];
	echo "<h4>$lname</h4><br/>";

	$wildbook = connect_wildbook();
	$loc_query = $wildbook->prepare("SELECT `lid`, `latitude`, `longitude` FROM `location` WHERE `lname` = ?");
	$loc_query->bind_param("s", $lname);
	$loc_query->execute();
	$loc_query->store_result();
	$loc_query->bind_result($lid, $latitude, $longitude);
	if ($loc_query->fetch()) {
		$exists = true;
	} else {
		echo "This location does not yet exist. Would you like to create it?";
		$exists = false;
		$latitude = 40.6944;
		$longitude = -73.9865;
	}
?>

<?php if (!$exists) { ?>
	<form action="addlocation.php" method="post">
		<input type="hidden" name="lname" value="<?php echo $lname ?>" />
		<input id="lat" type="hidden" name="latitude" value="<?php echo $latitude ?>" />
		<input id="lng" type="hidden" name="longitude" value="<?php echo $longitude ?>" />
		<input type="submit" value="Create" ?>
	</form>
<?php } 

	else {
		echo $lname ."<br>";
		
		echo "Activities at this location <br> --------------------------------------------------- <br>";
		$user_act_query = $wildbook->prepare("SELECT `aname` FROM `useractivitylocation` WHERE `lid` = ?");
		if (!$user_act_query) echo  "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
		$user_act_query->bind_param("i",$lid);		
		$user_act_query->execute();
		$user_act_query->store_result();
		$user_act_query->bind_result($aname);
		while ($user_act_query->fetch()) {
			echo "<br>$aname";
			?>
			<form action="like.php" method="post">
			<input type="hidden" value="<?php echo $aname ?>" name="aname">
			<input type="hidden" value="<?php echo $lid ?>" name="lid">
			<input type="hidden" value="<?php echo $lname ?>" name="lname">
			<input type="submit" value="Like" /><br>
			<?php
		}
		
		echo "Add an activity at this location <br>"; 
		?>
		<form action="like.php" method="post">
		Activity:  <input name="aname" type="text" maxlength="30"/>
		<input type="hidden" value="1" name="loc_add_act">
		<input type="hidden" value="<?php echo $lid ?>" name="lid">
		<input type="hidden" value="<?php echo $lname ?>" name="lname">
		<input type="submit" value="Like" />		
		
		<?php
		
	}
?>


<div style="width: 50%">
	<div id="my-map" style="width: 100%; height: 100%;"/>
</div>
<script type="text/javascript"
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBbMFulLS9iBqtaxzExN7koLGEpRsRHxxQ&libraries=places&sensor=false">
</script>
<script type="text/javascript">
	function initialize() {
		var latitude = <?php echo $latitude ?>;
		var longitude = <?php echo $longitude ?>;
		var lname = "<?php echo $lname ?>";
		var mapOptions = {
			center: new google.maps.LatLng(latitude, longitude),
			zoom: 15
		};
		var map = new google.maps.Map(document.getElementById("my-map"), mapOptions);

		var query = { query: lname };

	<?php if ($exists) { ?>

		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(latitude, longitude),
			map: map,
			title: lname
		});

	<?php } else { ?>

		service = new google.maps.places.PlacesService(map);
		service.textSearch(query, function(results, status) {
			if (status == google.maps.places.PlacesServiceStatus.OK && results.length > 0) {
				var marker = new google.maps.Marker({
					position: results[0].geometry.location,
					map: map,
					title: lname
				});

				map.setCenter(marker.getPosition());
				google.maps.event.addListener(map, 'click', function(event) {
					document.getElementById("lat").value = event.latLng.lat();
					document.getElementById("lng").value = event.latLng.lng();
					marker.setPosition(event.latLng);
				});
			}
		});

	<?php } ?>

	}
	google.maps.event.addDomListener(window, 'load', initialize);
</script>

<a href="home.php">Home</a>
<?php
	end_page();
?>