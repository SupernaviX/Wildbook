<html>
<head>
<title>Wildbook</title>
</head>
<body>
<h4>Home</h4>

<?php
$username = $_GET['username'];

$link = mysql_connect('localhost','root','password'); 
if (!$link) { 
	die('Could not connect to MySQL: ' . mysql_error()); 
} 
$wildbook = mysql_select_db('wildbook',$link) or die("Could not select wildbook" . mysql_error());

$uid_query = mysql_query("select uid from user where username = '$username';") 
		or die('Invalid query: ' . mysql_error());
$row = mysql_fetch_array($uid_query);
$uid = $row['uid'];

echo "Add a Diary Post <br>";
?>
<form action="adddiarypost.php" method="post"> 
title: <input name="title" type="text" maxlength="30"/> 
content : <input name="content" type="text" maxlength="30"/>
<input type="submit" />
</form>
<?php
echo "All Diary Posts <br> ---------------------------------------------<br> ";
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
?>
</body>

</html>
