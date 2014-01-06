<?php
include_once('Scripts/lock.php');

$community_id = urldecode(base64_decode($_GET['id']));

$activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share 
	WHERE community_id = :community_id AND direct=1) ORDER BY time DESC";
$activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$activity_query->execute(array(":community_id" => $user->getCommunityId()));

if ($community_id == $user->getCommunityId()) {
    $page_identifier = "school";
}
$profilepicexists = false;

include_once('welcome.php');
include_once('chat.php');

if (isset($_GET['f'])) {
    if ($_GET['f'] == 'p') {
        $feed_id = 'p';
    }
    else if ($_GET['f'] == 'f') {
        $feed_id = 'f';

        $activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share WHERE 
			community_id = :community_id AND year = :user_year AND direct = 1) AND visible = 1 ORDER BY time DESC";
        $activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $activity_query->execute(array(":community_id" => $user->getCommunityId(), ":user_year" => $user->getPosition()));
    }
    else if ($_GET['f'] == 'm') {
        $feed_id = 'm';

        $activity_query = "SELECT * FROM activity WHERE id IN (SELECT activity_id FROM activity_share WHERE 
		community_id = :community_id AND year = :user_year AND direct = 1) AND visible = 1 ORDER BY time DESC";
        $activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $activity_query->execute(array(":community_id" => $user->getCommunityId(), ":user_year" => $user->getPosition()));
    }
}
else {
    $feed_id = 'p';
}
?>

<html>
    <head>
        <title><?php echo $community->getName($community_id); ?></title>
    </head>
    <body>		
        <div class="container">
            <table class="info_table_layout">
                <tr>
                    <td style='width:220px;'>
                        <div class='profilepicturediv' style='background-image:url("<?php echo $community->getProfilePicture('chat', $community_id); ?>");'>
                        </div>
                    </td>
                    <td>
                        <p class='name_title'><?php echo $community->getName($community_id); ?></p>
                    </td>
                </tr>
            </table>
            <div id='feed_wrapper_scroller' style='padding-left:20px;border-top:1px solid lightgrey;border-bottom:1px solid lightgrey;'>
                <table cellspacing='0'>
                    <tr>
                        <td style='margin-right:5px;'>
                            <div id='p' filter_id = 'p' class="feed_selector 
                            <?php
                            if ($feed_id == 'p') {
                                echo 'active_feed';
                            }
                            ?>">Posts</div>
                        </td>
                        <td>
                            <div id='f' filter_id = 'f' class="feed_selector 
                            <?php
                            if ($feed_id == 'f') {
                                echo 'active_feed';
                            }
                            ?>">Files</div>
                        </td>
                        <td>
                            <div id='m' filter_id = 'm' class="feed_selector 
                            <?php
                            if ($feed_id == 'm') {
                                echo 'active_feed';
                            }
                            ?>">Members</div>
                        </td>
                    </tr>
                </table>
            </div>
            <div id='school_refresh'>
                <?php
                if ($feed_id == 'm') {
                    echo "
				<table>
				<tbody>";
    $members = $community->getMembers($community_id);
    foreach ($members as $member) {
        echo "<tr>";
        echo "<td>";
        echo "<a class='user_name' href='user?" . $member['id'] . "'>" . $member['name'] . "</a>";
        echo "</td>";
        echo "<td>";
        echo $member['year'];
        echo "</td>";
        echo "<td>";
        echo $system->humanTiming(strtotime($member['joined']));
        echo "</td>";
        if ($user->isAdmin() == true || $community->getLeaderId($community_id) == $user->getId()) {
            echo "<td>";
            echo "a";
            echo "</td>";
        }
        echo "</tr>";
    }

    echo "</tbody>
				</table>";
}
else if ($feed_id == 'p') {
    echo "<div class='home_feed_container'>";
    $array = $activity_query->fetchAll(PDO::FETCH_ASSOC);
    $count = count($array);
    foreach ($array as $activity) {
        $home->homeify($activity, $database_connection, $user);
    }
    echo "</div>";
}
?>
            </div>
        </div>	
    </body>
    <script>
        $('.feed_selector').click(function(event)
        {
            $('.feed_selector').removeClass('active_feed');
            $(this).addClass('active_feed');

            $('#school_refresh').fadeOut(100, function()
            {
                $(this).empty();
                $('#school_refresh').append('<center><img style="margin-top:50px;" src="Images/ajax-loader.gif"></img></center>');
                $(this).fadeIn();
            });

            var element_id = "#" + $(this).attr('id');
            var wrapper = "#" + $(this).parents('div[id]').attr('id');

            scrollH(element_id, wrapper, 400);

            var value = $(this).attr('filter_id');
            if (typeof value === "undefined")
            {

            }
            else
            {
                setCookie('school_feed', value);
            }
            getSchoolContent(value);
        });
        function getSchoolContent(feed_id)
        {
            var encrypted_id = "<?php echo $_GET['id']; ?>";
            $('#school_refresh').load("community??id=" + encrypted_id + "&f=" + feed_id + " #school_refresh", function(response) {
            });
        }
    </script>
</html>