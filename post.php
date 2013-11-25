<?php
include_once('Scripts/lock.php');
$post_number = 0;
function humanTiming ($time)
{

	    $time = time()-3600 - $time; // to get the time since that moment // also - hour for mismatch

	    $tokens = array (
	    	31536000 => 'year',
	    	2592000 => 'month',
	    	604800 => 'week',
	    	86400 => 'day',
	    	3600 => 'hour',
	    	60 => 'minute',
	    	1 => 'second'
	    	);

	    foreach ($tokens as $unit => $text) 
	    {
	    	if ($time < $unit)
	    		continue;
	    	$numberOfUnits = floor($time / $unit);
	    	return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
	    }
	}
	?>
	<html>
	<head>	
		<link rel="stylesheet" type="text/css" href="CSS/home.css">
		
		<script src="http://code.jquery.com/jquery-latest.min.js"></script>
		<script>
		function submitlike(id, type)
		{
			$.post("Scripts/like.php", { id: id, type: type}, function(data)
			{
				if(type==1)
				{
					$('#'+id+'likes').text(data);
				}
				else
				{
					$('#'+id+'dislikes').text(data);
				}
			});
		}
		</script>
		<script>
		function submitcomment(comment_text, post_id)
		{
			$.post("Scripts/submitcomment.php", { comment_text: comment_text, post_id: post_id}, function(data)
			{
				$('#commentcomplete_'+post_id).load('home.php #commentcomplete_'+post_id);
			});
		}
		</script>
		<script>
		$('.icon').click(function() {
			$('.icon').fadeTo('slow', 0.5);
		});
		function openDialog(){
			$( "#status_image_selector" ).click();
		}
		</script>
		<script>
		function show_Confirm(post_id)
		{
			$('#delete1_post_'+post_id).hide();
			$('#delete_post_'+post_id).css('visibility','visible').hide().fadeIn('slow');
		}
		function delete_post(post_id)
		{
			$.post("Scripts/delete_post.php", { post_id: post_id}, function(){
				$('#single_post_'+post_id).slideUp();
				$('#commentcomplete_'+post_id).slideUp();
				$('#comment_time_'+post_id).slideUp();
				$('#delete_post_'+post_id).slideUp();
			});
		}
		</script>
		
		<title>View: Post</title>
	</head>
	<body>
		<?php include_once('welcome.php');?>
		<?php include_once('chat.php');?>
		<?php include_once('friends_list.php');?>
		<?php
		$activity_query = mysql_query("SELECT * FROM activity WHERE id = ".$_GET['id']."");
		?>
		<div class="container" id = "container">
			<?php
			if($_GET['id'] != "")
			{
				while($activity = mysql_fetch_array($activity_query))
				{
					$post_number++;
					$db_query_comments=mysql_query("SELECT * FROM comments WHERE post_id = ".$activity['id']." ORDER BY time ASC");
					$db_queryuser=mysql_query("SELECT * FROM users WHERE id =".$activity['user_id'].";");
					$rawtime = $activity['time'];
					$time = strtotime($rawtime);
					$profile = mysql_fetch_array($db_queryuser);
					$vote_percentage = $activity['dislikes'] / ($activity['dislikes'] + $activity['likes']) * 100;
					$vote_percentage= round($vote_percentage, 1);

					echo '<div id="single_post_'.$activity['id'].'" class="singleupdate">';
					echo "<table class='singleupdate'><tr><td class='updatepic'>";
					echo "<a class='userlink' href='user?id=".$activity['user_id']."'/>";
					echo "<div class='imagewrap'><img style='max-height:200px;' class='img' src='".$profile['profile_picture_thumb']."'></img></div></a></td><td class='update'>";
					echo "<a class='userlink' href='user?id=".$activity['user_id']."'/>";
					echo $activity['user_name'];
					echo " </a>";

					if($activity['dislikes'] == 0 && $activity['likes'] != 0)
					{
						$vote_percentage = 100;
					}
					if($activity['type'] == "text")
					{
						echo "<hr style='line-height:22px;display: block; margin: 10px 0;'>";
						echo $activity['status_text'];
					}
					else if($activity['type'] == "profile")
					{
						$phrase_string = strtolower($activity['user_gender'])."_".$user['default_language'];
						echo $phrases['profile_picture_'.$phrase_string];
					}
					else if($activity['type'] == "file")
					{
						$phrase_string = strtolower($user['default_language']);
						echo $phrases['file_share_'.$phrase_string]."<br/><br/>";
						echo "<span style='font-style: italic;'>".$activity['description']."</span>";

						$link = preg_split ("/[\s,]+/", $activity['status_text']);
						end($link);
						$index = key($link);
						echo "<br><a style='text-decoration:none; color:grey;' href='".$activity['status_text']."''>".$link[$index]."</a>";
					}
					else if ($activity['type'] == "folder")
					{
						echo $activity['status_text'];
					}
					echo '</td><td style="vertical-align:top;"><a href="#!" onclick="submitlike('.$activity["id"].', 1); return false;"><img class="icon" src="Images/Icons/icons/thumb-up.png"></a><span id='.$activity['id'].'likes>'.$activity["likes"].'</span></td><td style="vertical-align:top;">
					<a href="#!" onclick="submitlike('.$activity["id"].', 0); return false;"><img class="icon" src="Images/Icons/icons/thumb.png"></a><span id='.$activity['id'].'dislikes>'.$activity['dislikes'].'</span></td><td style="vertical-align:top;"><span title="Post Popularity" class="vote_percentage" id='.$activity['id'].'vote_percentage>'.$vote_percentage.'%</span></td>';
					echo "</tr><tr><td></td></tr></table>";
					echo "</div>";
					echo "<div class='comments' id = 'commentcomplete_".$activity['id']."'>";
					$num = mysql_num_rows($db_query_comments);
					if($num <= 0)
					{
						echo '<div style="border: 0px;" id= comment_div_'.$activity['id'].' class="chatinput">';
					}
					else
					{
							//echo "<center><span id = show_".$activity['id']." style='font-weight: bold; color:lightgrey; font-size: 15px;'>Show ".$num." comments</span></center>";
						echo '<div id= comment_div_'.$activity['id'].' style="padding:10px;border:1px solid lightgrey;">';
					}
					while($recordcomments = mysql_fetch_array($db_query_comments))
					{
						$rawtime = $recordcomments['time'];
						$time = strtotime($rawtime);

						echo "<table style='font-size: 0.9em;'><tr><td style='vertical-align:top;'>";
						echo "<a class='userlink' href='user.php?id=".$recordcomments['commenter_id']."'>";
						echo $recordcomments['commenter_name']."</a>";
						echo "</td><td>";
						echo $recordcomments['comment_text'].'</td><td><span style="color:lightgrey; font-size: 0.8em;">'.humanTiming($time).' ago</span>';
						echo "</tr></table>";
						echo "</span>";
					}
					if($num < 10)
					{
						echo '<textarea class="inputtext" id="comment_'.$activity['id'].'" name="text" onkeydown="if (event.keyCode == 13) { submitcomment(this.value, '.$activity['id'].'); return false; }" placeholder="Write a comment..."></textarea>';
					}
					else
					{
						echo '<textarea class="inputtext" id= "comment_'.$activity['id'].'" name="text" onkeydown="if (event.keyCode == 13) { submitcomment(this.value, '.$activity['id'].'); return false; }" placeholder="Are you sure you don\'t want to chat instead?"></textarea>';
					}
					echo "</div>";
					if($activity['user_id'] == $user['id'])
					{
						echo "<span id='delete1_post_".$activity['id']."' onclick='show_Confirm(".$activity['id'].");'
						style='font-family: century gothic; cursor:pointer;font-size:0.8em; position:absolute;color:grey;left:20; bottom:25;'>delete</span>";
						echo "<span id='delete_post_".$activity['id']."' onclick='delete_post(".$activity['id'].");'
						style='font-family: century gothic; visibility:hidden; position:absolute;cursor:pointer;font-size:0.8em; color:red; left:20; bottom:25;'>Confirm</span>";
					}
					echo "<span id='comment_time_".$activity['id']."' style='font-size:0.8em; color:grey; float:right;'> ".humanTiming($time)." ago -</span>";
				}
				if($post_number == 0)
				{
					echo "<br><br><span style='margin-left:30%;font-family: century gothic;'>Sorry, this post has been removed by its owner!</span>";
				}
				echo '</center></div>';
			}
			?>	
		</div>
		<script>
		function showhide(element)
		{
			$(element).toggle( "slide" );
		});
}
</script>
</body>
</html>