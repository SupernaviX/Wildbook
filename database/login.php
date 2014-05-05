<html>
<head>
<title>Wildbook</title>
</head>
<body>
<h4>Login</h4>

<form action="login.php" method="post"> 
username: <input name="username" type="text" maxlength="30"/> 
password : <input name="password" type="text" maxlength="30"/>
<input type="submit" />
</form>
<?php
if( isset($_POST['username']) && isset($_POST['password']) ) {
	$link = mysql_connect('localhost','root','password'); 
	if (!$link) { 
		die('Could not connect to MySQL: ' . mysql_error()); 
	} 
	$wildbook = mysql_select_db('wildbook',$link) or die("Could not select wildbook" . mysql_error());

	$info_accepted = 1;

	$username = $_POST['username'];
	$user_query = mysql_query("select username from user;") 
		or die('Invalid query: ' . mysql_error());
	$un_exists = 0;
	while( ($row = mysql_fetch_array($user_query)) && $un_exists == 0 ) {
		$tempuser = $row['username'];
		if($tempuser == $username) {
			$un_exists = 1;
		}
	}
	if($un_exists == 0) {
		$info_accepted = 0; // username does not exist
	}
	else {
		$password = $_POST['password'];
		$pass_query = mysql_query("SELECT password from user where username = '$username'") 
		or die('Invalid query: ' . mysql_error());
		while($row = mysql_fetch_array($pass_query)) {
			$temppass = $row['password'];
			if ($temppass != $password) {
				$info_accepted = 0;
			}
		}
	}

	if ($info_accepted == 1) {
		header("location:home.php?username=$username");
	}
	else {
		echo "Username/password invalid, try again";
	}
}
mysql_close($link); 
?>

<br><br><br>
<h4>Sign up</h4>
<form action="createprofile.php" method="post"> 
username: <input name="username" type="text" maxlength="30"/> <br>
password : <input name="password" type="text" maxlength="30"/><br>
re-enter password : <input name="password2" type="text " maxlength="30"/><br>
age : <input name="age" type="text" /><br>
city : <input name="city" type="text" maxlength="30"/><br>
<input type="submit" />
</form>




</body>
</html>
