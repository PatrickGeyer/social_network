<?php
include ('extends.class.php');
$extends = new Extend;
?>
<head>
	<link rel="stylesheet" type="text/css" href="../CSS/search.css">
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script>
	$('.name_selector').hover(function(){
		$('.match').css('background-color', 'transparent');
	}, function()
	{
		//mouseleave
	});
	$('.match').hover(function(){
		$('.match').css('background-color', '#FAFAFA');
	});
	</script>
</head>
<?php
$return_data = "";
$suggestions = 0;
include_once('lock.php');
$searchTxt = $_POST['input_text'];
$return_data;

	$sql = "SELECT * FROM users WHERE INSTR(`name`, '{$searchTxt}') > 0;";
	$sql = $database_connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$sql->execute();
	$sql = $sql->fetchAll(PDO::FETCH_ASSOC);


	$group_sql = "SELECT * FROM `group` WHERE INSTR(`group_name`, '{$searchTxt}') > 0;";
	$group_sql = $database_connection->prepare($group_sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$group_sql->execute();
	$group_sql = $group_sql->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['search']) && $_POST['search'] == "universal")
{
	echo "<script>$('#names_universal').show();</script>";
	foreach($sql as $row) 
	{
		$suggestions++;
		if($searchTxt == $row['name'])
		{
			$return_data .= "<a class='search_option' href='user?id=".urlencode(base64_encode($row['id']))."'>
			<div class='match' onclick='addreceiver(".$row['id'].", &quot;".$row['name']."&quot;);' id='match1'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$extends->trimStr($row['name'], 30)."</span><br /><span class='school'>".$extends->trimStr($row['school'], 25)."&bull;Year ".$row['year']."</span></div><a class='search_option'/>"; 
		}
		if($suggestions == 1)
		{
			$return_data = "<a class='search_option' href='user?id=".urlencode(base64_encode($row['id']))."'>
			<div class='match' onclick='addreceiver(".$row['id'].", &quot;".$row['name']."&quot;);' id='match'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$extends->trimStr($row['name'], 30)."</span><br /><span class='school'>".$extends->trimStr($row['school'], 25)."&bull;Year ".$row['year']."</span></div><a class='search_option'/>"; 
		}
		else
		{
			$return_data .= "<a class='search_option' href='user?id=".urlencode(base64_encode($row['id']))."'>
			<div class='name_selector' onclick='addreceiver(".$row['id'].", &quot;".$row['name']."&quot;);' id='".$row['id']."'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$extends->trimStr($row['name'], 30)."</span><br /><span class='school'>".$extends->trimStr($row['school'], 25)."&bull;Year ".$row['year']."</span></div><a class='search_option'/>"; 
		}
	} 
	foreach($group_sql as $row) 
	{
		$suggestions++;
		if($searchTxt == $row['group_name'])
		{
			$return_data .= "<a class='search_option' href='group?id=".urlencode(base64_encode($row['id']))."'>
			<div class='match' onclick='addreceiver(".$row['id'].", &quot;".$row['group_name']."&quot;);' id='match1'>
			<img class='profile_picture' src='".$row['group_profile_picture_chat']."'></img>
			<span class='name'>".$row['group_name']."</span><br /><span class='school'>".$row['group_about']."</span></div><a class='search_option'/>"; 
		}
		if($suggestions == 1)
		{
			$return_data = "<a class='search_option' href='group?id=".urlencode(base64_encode($row['id']))."'>
			<div class='match' onclick='addreceiver(".$row['id'].", &quot;".$row['group_name']."&quot;);' id='match'>
			<img class='profile_picture' src='".$row['group_profile_picture_chat']."'></img>
			<span class='name'>".$row['group_name']."</span><br /><span class='school'>".$row['group_about']."</span></div><a class='search_option'/>"; 
		}
		else
		{
			$return_data .= "<a class='search_option' href='group?id=".urlencode(base64_encode($row['id']))."'>
			<div class='name_selector' onclick='addreceiver(".$row['id'].", &quot;".$row['group_name']."&quot;);' id='".$row['id']."'>
			<img class='profile_picture' src='".$row['group_profile_picture_chat']."'></img>
			<span class='name'>".$row['group_name']."</span><br /><span class='school'>".$row['group_about']."</span></div><a class='search_option'/>"; 
		}
	} 
	//$return_data .= "<div style='padding:5; border-top:1px dotted grey; text-align:center;background-color:transparent; position:static; bottom:0;'>
	//<a class='search_option' href='search?q=".$searchTxt."'><small>Show all results</small></a></div>";
	if($searchTxt == "")
	{
		$return_data = "<script>$('#names_universal').hide();</script>";
	}
}
else if(isset($_POST['search']) && $_POST['search'] == "message")
{
	echo "<script>$('#names').show();</script>";

	foreach($sql as $row) 
	{
		$suggestions++;
		if($searchTxt == $row['name'])
		{
			$return_data .= "<div class='match' onclick='addreceivermessage(".$row['id'].", &quot;".$row['name']."&quot;);' id='match1'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."&bull;Year ".$row['year']."</span></div>"; 
		}
		if($suggestions == 1)
		{
			$return_data = "<div class='match' onclick='addreceivermessage(".$row['id'].", &quot;".$row['name']."&quot;);' id='match'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."&bull;Year ".$row['year']."</span></div>"; 
		}
		else
		{
			$return_data .= "<div class='name_selector' onclick='addreceivermessage(".$row['id'].", &quot;".$row['name']."&quot;);' id='".$row['id']."'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."&bull;Year ".$row['year']."</span></div>"; 
		}
	} 
}
else if(isset($_POST['search']) && $_POST['search'] == "share")
{
	echo "<script>$('#names').show();</script>";

	foreach($group_sql as $row)
	{
		$suggestions++;
		if($searchTxt == $row['group_name'])
		{
			$return_data .= "<div class='match' onclick='addreceivershare(&quot;group&quot;, ".$row['id'].", &quot;".$row['group_name']."&quot;);' id='match1'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['group_name']."</span><br /><span class='school'>".$row['group_about']."</span></div>"; 
		}
		if($suggestions == 1)
		{
			$return_data = "<div class='match' onclick='addreceivershare(&quot;group&quot;, ".$row['id'].", &quot;".$row['group_name']."&quot;);' id='match'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['group_name']."</span><br /><span class='school'>".$row['group_about']."</span></div>"; 
		}
		else
		{
			$return_data .= "<div class='name_selector' onclick='addreceivershare(&quot;group&quot;, ".$row['id'].", &quot;".$row['group_name']."&quot;);' id='".$row['id']."'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['group_name']."</span><br /><span class='school'>".$row['group_about']."</span></div>"; 
		}
	}
	foreach($sql as $row) 
	{
		$suggestions++;
		if($searchTxt == $row['name'])
		{
			$return_data .= "<div class='match' onclick='addreceivershare(&quot;user&quot;, ".$row['id'].", &quot;".$row['name']."&quot;);' id='match1'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."</span></div>"; 
		}
		if($suggestions == 1)
		{
			$return_data = "<div class='match' onclick='addreceivershare(&quot;user&quot;, ".$row['id'].", &quot;".$row['name']."&quot;);' id='match'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."</span></div>"; 
		}
		else
		{
			$return_data .= "<div class='name_selector' onclick='addreceivershare(&quot;user&quot;, ".$row['id'].", &quot;".$row['name']."&quot;);' id='".$row['id']."'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."</span></div>"; 
		}
	} 
}

else
{
	echo "<script>$('#names').show();</script>";
	foreach($sql as $row) 
	{
		$suggestions++;
		if($searchTxt == $row['name'])
		{
			$return_data .= "<div class='match' onclick='addreceivergroup(".$row['id'].", &quot;".$row['name']."&quot;);' id='match1'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."</span></div>"; 
		}
		if($suggestions == 1)
		{
			$return_data = "<div class='match' onclick='addreceivergroup(".$row['id'].", &quot;".$row['name']."&quot;);' id='match'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."</span></div>"; 
		}
		else
		{
			$return_data .= "<div class='name_selector' onclick='addreceivergroup(".$row['id'].", &quot;".$row['name']."&quot;);' id='".$row['id']."'>
			<img class='profile_picture' src='".$row['profile_picture_chat_icon']."'></img>
			<span class='name'>".$row['name']."</span><br /><span class='school'>".$row['school']."</span></div>"; 
		}
	} 
}

if($suggestions == 0)
{
	$return_data .= "<div style='text-align:center;'><span class='school'>No Suggestions</span></div>";
}
if($searchTxt == "")
{
	$return_data .= "<script>$('#names').hide();</script>";
}
echo $return_data;
?>