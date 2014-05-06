<html>
<head>
<title>Wildbook</title> <!-- we should separate this boilerplate into a function -->
</head>
<body>
<?php
session_start();
$username = $_SESSION['username'];
if( isset($_POST['search_username']) ) {
	$search_user = $_POST['search_username'];
	header("location:profile.php?search=$search_user");
}
else {
	$search = $_GET['search'];
	
	include 'functions.php';
	$link = connect_wildbook();
	if (user_exists($search)) {
		echo "<h4>"; echo $search; echo"</h4>";
	}
	else {
		echo "User does not exist";
	}
	mysql_close($link); 
}

?>
</body>
<br><br><br>
<?php
echo "<a href=\"home.php?username=$username\">Home</a> <br />";
?>
</html>