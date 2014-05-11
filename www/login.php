<?php
	include_once "template.php";
	begin_page("Login");
?>

<h4>Login</h4>

<form action="login.php" method="post"> 
username: <input name="username" type="text" maxlength="30"/> 
password : <input name="password" type="password" /> 
<input type="submit" />
</form>

<?php
if( isset($_POST['username']) && isset($_POST['password']) ) {
	$wildbook = connect_wildbook();

	$username = $_POST['username'];
	$password = $_POST['password'];

	if (!($pass_query = $wildbook->prepare("SELECT `uid`, `passhash` FROM `user` WHERE `username` = ?")))
		echo "Prepare failed: (" . $wildbook->errno . ") " . $wildbook->error;
	$pass_query->bind_param("s", $username);
	if (!($pass_query->execute()))
		echo "Execute failed: (" . $wildbook->errno . ") " . $wildbook->error;
	$pass_query->bind_result($uid, $stored_passhash);
	if ($pass_query->fetch() && password_verify($password, $stored_passhash)) {
		login($username, $uid);
		header("location:home.php");
	}
	else
		echo "Username/password invalid, try again";
}

?>

<br><br><br>
<h4>Sign up</h4>
<form action="createprofile.php" method="post"> 
username: <input name="username" type="text" maxlength="30"/> <br>
password : <input name="password" type="password" /><br>
re-enter password : <input name="password2" type="password"/><br>
age : <input name="age" type="text" /><br>
city : <input name="city" type="text" maxlength="30"/><br>
<input type="submit" />
</form>

<?php end_page(); ?>