<html>
<head>
<title>Wildbook</title>
</head>
<body>
<h4>Create Profile</h4>

<?php
$link = mysql_connect('localhost','root','password'); 
if (!$link) { 
	die('Could not connect to MySQL: ' . mysql_error()); 
} 
$wildbook = mysql_select_db('wildbook',$link) or die("Could not select wildbook" . mysql_error());

$info_accepted = 1;

$username = $_POST['username'];
$user_query = mysql_query("SELECT username from user") 
	or die('Invalid query: ' . mysql_error());
while($row = mysql_fetch_array($user_query)) {
	$tempuser = $row['username'];
	if($tempuser == $username) {
		echo "This username is already taken <br />";
		$info_accepted = 0;
	}
	if(strlen($tempuser) > 30) {
		echo "This username is too long (30 characters or less) <br />";
		$info_accepted = 0;		
	}
}
$password = $_POST['password'];
$password2 = $_POST['password2'];
if($password != $password2) {
	echo "The passwords do not match <br />";
	$info_accepted = 0;
}
if(strlen($password) > 30) {
	echo "Password is too long (30 characters or less) <br />";
	$info_accepted = 0;
}

$age = $_POST['age'];
if ($age > 99 || $age < 0) {
	echo "Age is not valid <br />";
	$info_accepted = 0;
}

$city = $_POST['city'];
if (strlen($city) > 30) {
	echo "City name too long (30 characters or less) <br />";
	$info_accepted = 0;
}

if($info_accepted == 0) {
	echo "<a href=\"login.php\">Sign Up</a> <br />";
}
else {
	
	$insert = mysql_query("insert into user (username,password,age,city) values ('$username', '$password', $age, '$city');") 
	or die('Invalid query: ' . mysql_error());
	echo "<a href=\"home.php\">Profile Created!</a> <br />";
}
?>
</body>
</html>
