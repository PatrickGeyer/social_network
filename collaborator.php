<?php
include_once('welcome.php');
include_once('chat.php');
include_once('friends_list.php');

$collaboration_id = $_GET['ci'];
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="CSS/collaborator.css" />
	<script>
	setInterval(function(){refreshCollaboration();}, 2000);

	function refreshCollaboration()
	{
		$.post('Scripts/collaborator.class.php', {action: 'getSrc', id : <?php echo $collaboration_id; ?>}, function(response)
		{
					//alert(response);
					$('.collaborator').html(response);
				});
	}
	$('#about_edit_show').blur(function()
	{
		submitData();
	});
	function submitData()
	{
		var about = $('#about_edit_show').html();
		var email = '';
		var school = '';
		var year = '';
		$.post('Scripts/user.class.php', {about : about, email : email, year : year}, function(response)
		{
			$('#about_saved').fadeIn(function()
			{
				$('#about_saved').fadeOut(1000);
			});

  			//alert(response);
  		});
	}
	</script>
</head>
<body>
	<div class='container'>
		<span class='thin_blue_header'><?php echo $collaborator->getName($collaboration_id) ?></span><br><br><br>
		<div class='collaborator' contenteditable>Loading...</div>
	</div>
</body>
</html>