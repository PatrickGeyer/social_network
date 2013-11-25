<?php
include_once('database.class.php');
include_once('extends.class.php');

class Home extends Database
{
	private $phrases;
	private $extend;
	public function __construct()
	{
		parent::__construct();
		$phrases_query = "SELECT * FROM phrases;";
		$phrases_query = $this->database_connection->prepare($phrases_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$phrases_query->execute();
		$this->phrases = $phrases_query->fetch(PDO::FETCH_ASSOC);
		$this->extend = new Extend;
	}
	function homeify($activity, $database_connection, $user)
	{
		$post_number = 0;

		$db_query_comments = "SELECT * FROM comments WHERE post_id = :activity_id ORDER BY time ASC";
		$db_query_comments = $this->database_connection->prepare($db_query_comments, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$db_query_comments->execute(array(":activity_id" => $activity['id']));
		$db_queryuser = "SELECT * FROM users WHERE id =".$activity['user_id'].";";
		$activity_time = strtotime($activity['time']);
		$db_queryuser = $this->database_connection->prepare($db_queryuser, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$profile = $db_queryuser->execute();
		$profile = $db_queryuser->fetch();
		if($activity['dislikes'] + $activity['likes'] != 0)
		{
			$vote_percentage = $activity['dislikes'] / ($activity['dislikes'] + $activity['likes']) * 100;
		}
		else
		{
			$vote_percentage = 0;
		}
		$vote_percentage = round($vote_percentage, 1);
		echo '<div id="single_post_'.$activity['id'].'" class="singlepostdiv">';
		echo "<div id='".$activity['id']."'>";
		echo "<table onmouseover='refreshContent(".$activity['id'].");' class='singleupdate'>
		<tr>
		<td style='min-width:80px;' class='updatepic'>";
		echo "<a class='user_name_post' href='user?id=".urlencode(base64_encode($activity['user_id']))."'>";
		echo "<div class='imagewrap'><img style='max-height:200px;' class='img' src='".$profile['profile_picture_thumb']."'></img></div>
		</a>
		</td>
		<td class='update'>";
		echo "<a class='user_name_post' href='user?id=".urlencode(base64_encode($activity['user_id']))."'>";
		echo $activity['user_name'];
		echo " </a>";

		if($activity['dislikes'] == 0 && $activity['likes'] != 0)
		{
			$vote_percentage = 100;
		}
		if($activity['type'] == "text")
		{
			echo "<hr style='line-height:22px;display: block; margin: 10px 0;'>";
			$activity['status_text'] = str_replace("<img", "<img onclick='initiateTheater(this.src,".$activity['id'].");' ", $activity['status_text']);
			echo "<span style='word-break: break-all; word-wrap:break-all;'>".$activity['status_text'].'</span>';
		}
		else if($activity['type'] == "profile")
		{
			if($user['default_language'] == "")
			{
				$phrase_language = "en";
			}
			else
			{
				$phrase_language = strtolower($user['default_language']);
			}	
			$phrase_string = strtolower($activity['user_gender'])."_".$phrase_language;
			$phrase_string = "profile_picture_".$phrase_string;
			echo $phrases[$phrase_string];
		}
		else if($activity['type'] == "video")
		{
			$phrase_string = strtolower($activity['user_gender'])."_".$user['default_language'];
			echo $phrases['profile_picture_'.$phrase_string];
			echo "<embed src='".$activity."'></embed>";
		}
		else if($activity['type'] == "file")
		{
			if($user->getLanguage() == "")
			{
				$phrase_string = "en";
			}
			else
			{
				$phrase_string = strtolower($user->getLanguage());
			}	
			$phrase_query = "file_share_".$phrase_string;
			echo $this->phrases[$phrase_query]."<br/><br/>";
			echo "<span style='font-style: italic;'>".$activity['description']."</span>";
			echo "<br><a style='text-decoration:none; color:grey;' href='".$activity['status_text']."'>File</a>";
		}
		else if ($activity['type'] == "folder")
		{
			$phrase_string = strtolower($user['default_language']);
			$phrase_string = $this->phrases['folder_share_'.$phrase_string];
			$phrase_string = str_replace('$folder', "<a href='".$activity['status_text']."'>".$activity['activity_name']."'</a>", $phrase_string);
			echo $phrase_string;
		}
		else if ($activity['type'] == "abdicate")
		{
			$phrase_string = strtolower($user['default_language']);
			$phrase_string = $this->phrases['abdicate_'.$phrase_string];
			$phrase_string = str_replace('$group', "<a href='".$activity['status_text']."'>".$activity['activity_name']."'</a>", $phrase_string);
			echo $phrase_string;
		}

		$who_liked_query = "SELECT * FROM `votes` WHERE post_id = :activity_id AND vote_value = 1;";
		$who_liked_query = $database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$who_liked_query->execute(array(":activity_id" => $activity['id']));
		$like_count = $who_liked_query->rowCount();
		echo '</td>
		<td style="vertical-align:top; left:0;min-width:50px;">
		<span class="who_liked_hover" activity_id="'.$activity['id'].'" style="text-decoration:none;" onclick="submitlike('.$activity["id"].', '.$activity['user_id'].' ,1);">
		<img class="icon" src="Images/Icons/icons/thumb-up.png"></img>
		<span id='.$activity['id'].'likes>'.$like_count.'</span>
		<div class="who_liked" id="who_liked_'.$activity['id'].'">
		';
		$liked_number =  $who_liked_query->rowCount();
		$iteration = 0;
		$who_liked_all = $who_liked_query->fetchAll(PDO::FETCH_ASSOC);
		foreach($who_liked_all as $who_liked)
		{
			$iteration++;
			$who_liked_query = "SELECT name FROM `users` WHERE id = :user_id;";
			$who_liked_query = $database_connection->prepare($who_liked_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$who_liked_query->execute(array(":user_id" => $who_liked['user_id']));
			$who_liked = $who_liked_query->fetch(PDO::FETCH_ASSOC);
			if($iteration == 1)
			{
				echo $who_liked['name'];
			}
			else
			{
				echo ",<br>".$who_liked['name'];
			}
		}

		if($liked_number == 0)
		{
			echo "No one has liked this post yet.";
		}

		echo "</div></span></td>"; 
	//<td style="vertical-align:top;min-width:50px;">
	//<a href="#!" onclick="submitlike('.$activity["id"].', 0); return false;">
	//<img class="icon" src="Images/Icons/icons/thumb.png">
	//</a>
	//</td>
	//<td style="vertical-align:top;min-width:50px;">
	//	<span title="Post Popularity" class="vote_percentage" id='.$activity['id'].'vote_percentage>'.$vote_percentage.'%</span>
	//	</td>
		echo "</tr><tr><td></td><td>";
		$num = $db_query_comments->rowCount();			
		echo "<div class='comments' id = 'commentcomplete_".$activity['id']."'>";		
		if($num >= 0)
		{				
			echo '<div id= comment_div_'.$activity['id'].' class="comment_box">';
		}
		else
		{
			echo '<div style="border: 0px;" id= comment_div_'.$activity['id'].' class="chatinput">';	
		}

		$this->getComments($activity['id'], $database_connection);

		echo "<div id='comment_input_".$activity['id']."'><table style='width:100%;margin-top:10px;'><tr><td style='vertical-align:top;width:40px;'><img src='".$user->getProfilePicture('chat')."'></img></td><td style='vertical-align:top;'>";
		if($num < 10)
		{
			echo '<div data-placeholder="Write a comment..." contenteditable class="inputtext" id="comment_'.$activity['id'].'" name="text" onkeydown="if (event.keyCode == 13) 
			{ submitcomment($(this).html(), '.$activity['id'].'); return false; }"></div>';
		}
		else
		{
			echo '<div data-placeholder="Are you sure you don\'t want to chat instead?" contenteditable class="inputtext" id= "comment_'.$activity['id'].'" name="text" onkeydown="if (event.keyCode == 13) 
			{ submitcomment($(this).html(), '.$activity['id'].'); return false; }"></div>';
		}
		echo "</td><td style='vertical-align:top;width:60px;'><button
		onclick='submitcomment($(&quot;#comment_".$activity['id']."&quot;).html(), ".$activity['id'].");'
		style='margin-left:10px;' class='pure-button-secondary smallest inputtext_send'>send</button>";
		echo "</td></tr></table></div>";
		if($num >= 0)
		{
			echo "</div>"; 
		//close comment container div
		}
		else
		{
			echo "</div>"; 
		// close chatinput div
		}
		echo "</div>"; 
	//close comment complete div

		echo "<span id='post_time_".$activity['id']."' style='font-size:0.8em; color:grey; float:right;'> ".$this->extend->humanTiming($activity_time)." -</span>";
		if($activity['user_id'] == $user->getId())
		{
			echo "<span class='delete' id='delete1_post_".$activity['id']."' onclick='show_Confirm(".$activity['id'].");'
			style='font-family: century gothic; cursor:pointer;font-size:0.8em; color:grey; float:left;'>delete</span>";
			echo "<span class='delete' id='delete_post_".$activity['id']."' onclick='delete_post(".$activity['id'].");'
			style='font-family: century gothic; visibility:hidden; cursor:pointer;font-size:0.8em; color:red; float:left;'>Confirm</span>";
		}
		echo "</td></tr></table>";
		echo "</div>"; 
	// close single_post div
		echo "</div>";

	}
	function getComments($activity_id)
	{
		$db_query_comments = "SELECT * FROM comments WHERE post_id = :activity_id ORDER BY time ASC";
		$db_query_comments = $this->database_connection->prepare($db_query_comments, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$db_query_comments->execute(array(":activity_id" => $activity_id));

		while($recordcomments = $db_query_comments->fetch(PDO::FETCH_ASSOC))
		{
			$rawtime = $recordcomments['time'];
			$time = strtotime($rawtime);

			$comment_user = "SELECT name, profile_picture_chat_icon FROM users WHERE id = :user_id;";
			$comment_user = $this->database_connection->prepare($comment_user, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$comment_user->execute(array(":user_id" => $recordcomments['commenter_id']));
			$comment_user = $comment_user->fetch(PDO::FETCH_ASSOC);

			echo "<table id='post_comment_".$activity_id."' style='font-size: 0.9em;'><tr><td style='vertical-align:top;' rowspan='2'>";
			echo "<img src=".$comment_user['profile_picture_chat_icon']."></img></td><td style='vertical-align:top;'>";
			echo "<a class='userdatabase_connection' href='user?id=".urlencode(base64_encode($recordcomments['commenter_id']))."'>";
			echo "<span style='font-weight:bold;white-space:nowrap;color:grey;font-size:13px;padding-left:5px;padding-right:5px;'>".$comment_user['name']."</span></a>";
			echo "";
			echo "<span style='font-size:13px;'>".$recordcomments['comment_text'].'</span>
			</td></tr><tr><td colspan=2><span style="color:black; font-size: 0.8em;">- '.$this->extend->humanTiming($time).'</span>';
			echo "</tr></table><hr style=' margin:5px; margin-left:-10px; margin-right:-10px; border-color: white;'>";
		}
	}
}
if($_SERVER['REQUEST_METHOD'] == "POST")
{
	if(isset($_POST['activity_id']))
	{
		$home = new Home;
		$home->getComments($_POST['activity_id']);
	}
}
?>