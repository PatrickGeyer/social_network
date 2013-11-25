<?php
include_once('welcome.php');
?>
<head>
	
</head>
<body>
<div>
<?php
echo "Users:";
	$search_query = $_GET['q'];
	$db_query = mysql_query('SELECT * FROM users WHERE name LIKE %$search_query%');
	while($result = mysql_fetch_assoc($db_query))
	{
		echo "<div style='padding-bottom:50px;'";
		echo "<a href='user?".urlencode(base64_encode($result['id']))."'>";
		echo $result['name'];
		echo "</a>";
		echo "</div>";
	}
?>
</div>
</body>

?>