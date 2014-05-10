<?php
include 'template.php';
begin_page("Wildbook");

echo '<h4>Create Profile</h4>';

/*$link = mysql_connect('localhost','root','password'); 
if (!$link) { 
	die('Could not connect to MySQL: ' . mysql_error()); 
} 
$wildbook = mysql_select_db('wildbook',$link) or die("Could not select wildbook" . mysql_error());


$user_query = mysql_query("SELECT username from user") 
	or die('Invalid query: ' . mysql_error());
while($row = mysql_fetch_array($user_query)) {
	$tempuser = $row['username'];
	if($tempuser == $username) {
		echo "This username is already taken <br />";
		$info_accepted = 0;
	}
}*/
$info_accepted = 1;
$username = $_POST['username'];
if (user_exists($username)) {
	echo "This username is already taken <br />";
	$info_accepted = 0;
}


$password = $_POST['password'];
$password2 = $_POST['password2'];
if($password != $password2) {
	echo "The passwords do not match <br />";
	$info_accepted = 0;
}

$age = $_POST['age'];
if ($age > 99 || $age < 0) {
	echo "Age is not valid <br />";
	$info_accepted = 0;
}

$city = $_POST['city'];

if($info_accepted == 0) {
	echo "<a href=\"login.php\">Sign Up</a> <br />";
}
else {	
	$insert = mysql_query("insert into user (username,password,age,city) values ('$username', '$password', $age, '$city');") 
	or die('Invalid query: ' . mysql_error());
	echo "<a href=\"home.php?username=$username\">Profile Created!</a>";
}
mysql_close($link); 

end_page();
?>