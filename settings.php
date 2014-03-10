<?php
include_once('welcome.php');
include_once('chat.php');
$allschools = "SELECT id, name, FROM community";
$allschools = $database_connection->prepare($allschools);
$allschools->execute();
$allschools = $allschools->fetchAll(PDO::FETCH_ASSOC);
$emailvalid = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('Scripts/lock.php');
    $database_connection->beginTransaction();

    if ($user->getGender() == "Male") {
        $activity_query = "INSERT INTO activity (user_id, status_text, type) 
        VALUES(:user_id' changed his profile picture','profile');
        INSERT INTO activity_share(activity_id, community_id) VALUES(" . $database_connection->lastInsertId() . ", :community_id)";
        $activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $activity_query->execute(array(":user_id" => $user->getId(), ":community_id" => $user->getCommunityId()));
    }
    else {
        $activity_query = "INSERT INTO activity (user_id, status_text, type) 
        VALUES(:user_id, ' changed her profile picture','profile')";
        $activity_query = $database_connection->prepare($activity_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $activity_query->execute(array(":user_id" => $user->getId()));
    }
    $database_connection->commit();
}
?>
<?php
$current_tab = 'profile';
if (isset($_GET['tab'])) {
    $current_tab = $_GET['tab'];
}
?>
<head>
    <script src="Scripts/external/jquery-ui-1.10.3.js"></script>
    <title>Settings</title>
    <script>
        var current_tab = '<?php echo $current_tab; ?>';
        $(document).on('click', '#update_settings', function() {
            if (current_tab == "profile") {
                updateProfile();
            } else if (current_tab == "files") {
                updateFiles();
            }
        });

        function updateProfile() {
            var email = $('#email_primary').val();
            var password = $('#password_first').val();
//            var language = ;
//            var community = ;
//            var position = ;
        }
        function updateFiles() {

        }

        $(document).on('input propertychange change', '#current_password, #password_first, #password_second', function() {
            checkPassword();
        });

        function checkPassword() {
            var previous = '<?php echo $user->getPassword(); ?>';
            var current = $('#current_password').val();
            var first = $('#password_first').val();
            var second = $('#password_second').val();

            if (previous != current) {

            }

            if (first != second) {
                $('#pass_error').css('display', 'block');
            }
        }
    </script>
</head>
<body>
    <?php
    $personaldir = "User/Profilepictures/" . $user->getId();
    if (is_file($personaldir)) {
        if ($handle = opendir($personaldir)) {
            while (false !== ($entry = readdir($handle))) {
                if (strlen($entry) > 4) {
                    $personalfilepath = $personaldir . "/" . $entry;
                }
            }
        }
    }
    ?>
    <div class='global_container'>
        <?php            include_once 'left_bar.php'; ?>
        <div class="container container_full">
            <table>
                <tr>
                    <td>
                        <div class='box_container box_container_nav' style='width:300px;margin-top:23px;'>
                            <h3><?php echo $user->getName(); ?></h3>
                            <ul>
                                <li class="section <?php if ($current_tab == "profile") echo "current"; ?>">
                                    <a <?php if ($current_tab == "profile") echo "class='current'"; ?> href="settings?tab=profile">Profile</a>
                                </li>
                                <li class="section <?php if ($current_tab == "files") echo "current"; ?>">
                                    <a <?php if ($current_tab == "files") echo "class='current'"; ?> href="settings?tab=files">Files</a>
                                </li>
                                <li class="section <?php if ($current_tab == "notifications") echo "current"; ?>">
                                    <a <?php if ($current_tab == "notifications") echo "class='current'"; ?> href="settings?tab=notifications">Notifications</a>
                                </li>
                                <li class="section <?php if ($current_tab == "security") echo "current"; ?>">
                                    <a <?php if ($current_tab == "security") echo "class='current'"; ?> href="settings?tab=security">Security</a>
                                </li>
                                <li class="section <?php if ($current_tab == "privacy") echo "current"; ?>">
                                    <a <?php if ($current_tab == "privacy") echo "class='current'"; ?> href="settings?tab=privacy">Privacy</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        <div class='box_container' style='width:430px;margin-top:23px;'>
                            <?php if ($current_tab == "profile") : ?>
                                <h3>Profile</h3>
                                <ul>
                                    <li class='section'>
                                        <label class='settings'>Email</label>
                                        <input type="text" placeholder="Email..." autocomplete="off" id="email_primary" value="<?php
                                        if ($user->getEmail() != '') {
                                            echo $user->getEmail();
                                        }
                                        ?>"/>                        
                                    </li>
                                    <li class='section'>
                                        <label class='settings'>Password</label>
                                        <span class='user_preview_name edit_hidden'>Edit...</span>
                                        <div class="hidden_section" hidden>
                                            <input type="password" placeholder="Current Password" autocomplete="off" id="current_password"/>
                                            <hr>
                                            <input type="password" placeholder="New Password" autocomplete="off" id="password_first" /><br />
                                            <input type="password" placeholder="Confirm" autocomplete="off" id="password_second" />
                                            <span style='display: none;' class='info_warning' id='pass_error'>Passwords do not match.</span>
                                        </div>
                                    </li>
                                    <li class='section'>
                                        <label class='settings'>Language</label>
                                        <div class='default_dropdown_selector' style='display:inline-block;' wrapper_id='language_selector'>
                                            <span class='default_dropdown_preview'>English</span>
                                            <div class='default_dropdown_wrapper' id='language_selector'>
                                                <ul class='default_dropdown_menu'>
                                                    <li value='en' class='default_dropdown_item' controller_id='language_selector'>
                                                        <span>English</span>
                                                    </li>
                                                    <li value='ge' class='default_dropdown_item' controller_id='language_selector'>
                                                        <span>German</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    <li class='section'>
                                        <label class='settings'>Community</label>
                                        <div class='default_dropdown_selector' style='display:inline-block;' wrapper_id='community_selector'>
                                            <span class='default_dropdown_preview'><?php echo $user->getCommunityName(); ?></span>
                                            <div class='default_dropdown_wrapper' id='community_selector'>
                                                <ul class='default_dropdown_menu'>
                                                    <?php
                                                    foreach ($community->getCommunities() as $community) {
                                                        echo "<li value='" . $community['id']
                                                        . "' class='default_dropdown_item' controller_id='community_selector'><span>"
                                                        . $community['name']
                                                        . "</span></li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    <li class='section'>
                                        <label class='settings'>Current Position</label>
                                        <div class='default_dropdown_selector' style='display:inline-block;' wrapper_id='position_selector'>
                                            <span class='default_dropdown_preview'><?php echo $user->getPosition(); ?></span>
                                            <div class='default_dropdown_wrapper' id='position_selector'>
                                                <ul class='default_dropdown_menu'>
                                                    <?php
                                                    $years = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14);
                                                    foreach ($years as $year) {
                                                        echo "<li value='" . $year
                                                        . "' class='default_dropdown_item' controller_id='position_selector'><span>"
                                                        . $year
                                                        . "</span></li>";
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </li>
                                    <?php elseif ($current_tab == "files") : ?>
                                    <h3>Files</h3>
                                    <ul>
                                        <li class='section'>
                                            <p>These are the files settings</p>
                                        </li>
                                    <?php elseif ($current_tab == "notifications") : ?>
                                        <h3>Notifications</h3>
                                        <ul>
                                            <li class='section'>
                                                <input type="checkbox" style='display:inline-block;' />
                                                <p style='display:inline-block;'>Receive notifications for likes on your comments.</p> 
                                            </li>
                                            <li class='section'>
                                                <input type="checkbox" style='display:inline-block;' />
                                                <p style='display:inline-block;'>Receive notifications for comments on you posts.</p> 
                                            </li>
                                            <li class='section'>
                                                <input type="checkbox" style='display:inline-block;' />
                                                <p style='display:inline-block;'>Receive notifications for likes on your comments.</p> 
                                            </li>
                                        <?php else : ?>In development<?php endif; ?>
                                        <li class="section">
                                            <button id='update_settings' class='pure-button-success small' style=''>Update</button>
                                        </li>

                                    </ul>
                                    </div>
                                    </td>
                                    </tr>
                                    </table>


                                    </div>
                                    </div>
                                    </body>