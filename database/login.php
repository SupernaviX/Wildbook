<html>
<head>
<title>Wildbook</title>
</head>
<body>
<h4>Login</h4>

<form action="home.php" method="post"> 
username: <input name="username" type="text" /> 
password : <input name="password" type="text" />
<input type="submit" />
</form>

<br><br><br>
<h4>Sign up</h4>
<form action="createprofile.php" method="post"> 
username: <input name="username" type="text" /> <br>
password : <input name="password" type="text" /><br>
re-enter password : <input name="password2" type="text" /><br>
age : <input name="age" type="text" /><br>
city : <input name="city" type="text" /><br>
<input type="submit" />
</form>

<?php

?>


</body>
</html>
