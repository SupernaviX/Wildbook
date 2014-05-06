<html>
<head>
<title>Wildbook</title>
</head>
<body>
<h4>Home</h4>

<?php
$username = $_GET['username'];
session_start();
$_SESSION['username'] = $username;


$link = mysql_connect('localhost','root','password'); 
if (!$link) { 
	die('Could not connect to MySQL: ' . mysql_error()); 
} 
$wildbook = mysql_select_db('wildbook',$link) or die("Could not select wildbook" . mysql_error());

$uid_query = mysql_query("select uid from user where username = '$username';") 
		or die('Invalid query: ' . mysql_error());
$row = mysql_fetch_array($uid_query);
$uid = $row['uid'];
?>
<form action="profile.php" method="post"> 
username: <input name="search_username" type="text" maxlength="30"/> 
<input type="submit" />
</form>

<?php

echo "Your Friends <br> ------------------------------------------------- <br>";
$friend_query = mysql_query("select username from user where uid = (select seconduid from friend where firstuid = uid);") 
		or die('Invalid query: ' . mysql_error());
while ($row = mysql_fetch_array($friend_query)) {
 $friend = $row['username'];
 echo "$friend <br>";
}

echo "Add a Diary Post <br>";
?>
<form action="adddiarypost.php" method="post"> 
title: <input name="title" type="text" maxlength="30"/> 
content : <input name="content" type="text" maxlength="30"/>
<select name="privacy">
<option value ="1">Private</option>
<option value ="2">Friends</option>
<option value ="3">Friends of Friends</option>
<option value ="4">Everyone</option>
<input type="submit" />
</form>
<?php
echo "Your timeline <br> ---------------------------------------------<br> ";
$diary_query = mysql_query("select * from diarypost where posteruid = $uid;") 
		or die('Invalid query: ' . mysql_error());
while ($row = mysql_fetch_array($diary_query)) {
	echo $row['title']; echo "<br>";
	echo $row['timestamp']; echo "<br>";
	echo $row['content']; echo "<br>";
	$lid = $row['lid'];
	$privacy = $row['privacy'];
	echo "-----------------------------------------------<br>";
}
mysql_close($link); 
?>
</body>

</html>